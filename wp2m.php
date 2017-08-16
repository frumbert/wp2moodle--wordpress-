<?php
/*
Plugin Name: wp2moodle
Plugin URI: https://github.com/frumbert/wp2moodle--wordpress-
Description: A plugin that sends the authenticated users details to a moodle site for authentication, enrols them in the specified cohort
Requires: Moodle site with the wp2moodle auth plugin enabled (tested up to Moodle 3.1, Wordpress 4.4)
Version: 1.9
Author: Tim St.Clair
Author URI: http://frumbert.org
License: GPL2
*/

/*  Copyright 2012-2017

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?><?php

// some definition we will use
define( 'WP2M_PUGIN_NAME', 'Wordpress 2 Moodle (SSO)');
define( 'WP2M_CURRENT_VERSION', '1.9' );
define( 'WP2M_CURRENT_BUILD', '1' );
define( 'EMU2_I18N_DOMAIN', 'wp2m' );
define( 'WP2M_MOODLE_PLUGIN_URL', '/auth/wp2moodle/login.php?data=');

function wp2m_set_lang_file() {
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
		if (@file_exists($moFile) && is_readable($moFile)) {
			load_textdomain(EMU2_I18N_DOMAIN, $moFile);
		}

	}
}

wp2m_set_lang_file();

function wp2m_register_shortcode() {
	add_shortcode('wp2moodle', 'wp2moodle_handler');
}

/**
 * actions - register the plugin itself, it's settings pages and its wordpress hooks
 */
add_action( 'admin_menu', 'wp2m_create_menu' );
add_action( 'admin_init', 'wp2m_register_settings' );
register_activation_hook(__FILE__, 'wp2m_activate');
register_deactivation_hook(__FILE__, 'wp2m_deactivate');
register_uninstall_hook(__FILE__, 'wp2m_uninstall');
add_action ( 'init', 'wp2m_register_shortcode');
add_action ( 'init', 'wp2m_register_addbutton');

function wp2m_generate_encryption_key() {
	return base64_encode(openssl_random_pseudo_bytes(32));
}

function wp2m_is_base64($string) {
    $decoded = base64_decode($string, true);
    // Check if there is no invalid character in string
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) return false;
    // Decode the string in strict mode and send the response
    if (!base64_decode($string, true)) return false;
    // Encode and compare it to original one
    if (base64_encode($decoded) != $string) return false;
    return true;
}

/**
 * activating the default values
 */
function wp2m_activate() {

	$shared_secret = wp2m_generate_encryption_key();

	add_option('wp2m_moodle_url', 'http://localhost/moodle');
	add_option('wp2m_shared_secret', $shared_secret);
	add_option('wp2m_update_details', 'true');
}

/**
 * deactivating requires deleting any options set
 */
function wp2m_deactivate() {
	delete_option('wp2m_moodle_url');
	delete_option('wp2m_shared_secret');
	delete_option( 'wp2m_update_details' );
}

/**
 * uninstall routine
 */
function wp2m_uninstall() {
	delete_option( 'wp2m_moodle_url' );
	delete_option( 'wp2m_shared_secret' );
	delete_option( 'wp2m_update_details' );
}

/**
 * Creates a sub menu in the settings menu for the Link2Moodle settings
 */
function wp2m_create_menu() {
	add_menu_page(
		__('wp2Moodle', EMU2_I18N_DOMAIN),
		__('wp2Moodle', EMU2_I18N_DOMAIN),
		'manage_options',
		dirname(__FILE__).'/wp2m_settings_page.php',
		null,
		plugin_dir_url(__FILE__).'icon.svg'
	);
}

/**
 * Registers the settings that this plugin will read and write
 */
function wp2m_register_settings() {
	//register settings against a grouping (how wp-admin/options.php works)
	register_setting( 'wp2m-settings-group', 'wp2m_moodle_url' );
	register_setting( 'wp2m-settings-group', 'wp2m_shared_secret' );
	register_setting( 'wp2m-settings-group', 'wp2m_update_details' );
}

/**
 * Given a string and key, return the encrypted version (openssl is "good enough" for this type of data, and comes with modern php)
 */
function encrypt_string($value, $key) {
	if (wp2m_is_base64($key)) {
		$encryption_key = base64_decode($key);
	} else {
		$encryption_key = $key;
	}
	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	$encrypted = openssl_encrypt($value, 'aes-256-cbc', $encryption_key, 0, $iv);
	$result = str_replace(array('+','/','='),array('-','_',''),base64_encode($encrypted . '::' . $iv));
	return $result;
}

/* Not required in this plugin, but here's how you do it */
function decrypt_string($data, $key) {
	if (wp2m_is_base64($key)) {
		$encryption_key = base64_decode($key);
	} else {
		$encryption_key = $key;
	}
	list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
	return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}


/**
 * handler for the plugins shortcode (e.g. [wp2moodle cohort='abc123']my link text[/wp2moodle])
 * note: applies do_shortcode() to content to allow other plugins to be handled on links
 * when unauthenticated just returns the inner content (e.g. my link text) without a link
 */
function wp2moodle_handler( $atts, $content = null ) {

	// clone attribs over any default values, builds variables out of them so we can use them below
	// $class => css class to put on link we build
	// $cohort => text id of the moodle cohort in which to enrol this user
	// $group => text id of the moodle group in which to enrol this user
	// $course => text id of the course, if you just want to enrol a user directly to a course
	// $authtext => string containing text content to display when not logged on (defaults to content between tags when empty / missing)
	// $activity => index of the first activity to open, if autoopen is enabled in moodle
	extract(shortcode_atts(array(
		"cohort" => '',
		"group" => '',
		"course" => '',
		"class" => 'wp2moodle',
		"target" => '_self',
		"authtext" => '',
		"activity" => 0
	), $atts));

	if ($content == null || !is_user_logged_in() ) {
		if (trim($authtext) == "") {
			$url = '<a href="' . wp_registration_url() . '" class="'.esc_attr($class).'">' . do_shortcode($content) . '</a>'; // return content text linked to registration page  (value between start and end tag)
		} else {
			$url = '<a href="' . wp_registration_url() . '" class="'.esc_attr($class).'">' . do_shortcode($authtext) . '</a>'; // return authtext linked to registration page (value of attribute, if set)
		}
	} else {
		// url = moodle_url + "?data=" + <encrypted-value>
		$url = '<a target="'.esc_attr($target).'" class="'.esc_attr($class).'" href="'.wp2moodle_generate_hyperlink($cohort,$group,$course,$activity).'">'.do_shortcode($content).'</a>'; // hyperlinked content
	}
	return $url;
}

// wp2m filters are set at higher priority so they execute first

// over-ride the url for Marketpress *if* the download is a file named something-wp2moodle.txt
add_filter('mp_download_url', 'wp2m_download_url', 10, 3);

// over-ride the url for WooCommerce (has various download filters we have to try)
add_filter('woocommerce_download_file_redirect','woo_wp2m_download_url', 5, 2);
add_filter('woocommerce_download_file_force','woo_wp2m_download_url', 5, 2);
add_filter('woocommerce_download_file_xsendfile','woo_wp2m_download_url', 5, 2);

// woo shim to handle different arguments
function woo_wp2m_download_url($filepath, $filename) {
	wp2m_download_url($filepath, "", "");
}

// the download file is actually a text file containing the shortcode values
function wp2m_download_url($url, $order, $download) {

	if (strpos($url, 'wp2moodle.txt') !== false) {
		// mp url is full url = including http:// and so on... we want the file url
		$path = $_SERVER['DOCUMENT_ROOT'] . parse_url($url)["path"];
		$cohort = "";
		$group = "";
		$data = file($path); // now it's an array!
		foreach ($data as $row) {
			$pair = explode("=",$row);
			switch (strtolower(trim($pair[0]))) {
				case "group":
					$group = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
				case "cohort":
					$group = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
				case "course":
					$course = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
				case "activity":
					$activity = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
			}
		}
		$url = wp2moodle_generate_hyperlink($cohort,$group,$course,$activity);
		if (ob_get_contents()) { ob_clean(); }
		header('Location: ' . $url, true, 301); // redirect to this url
		exit();
	}
	return $url;
}

/*
If you are only using wp2moodle and not offering other marketpress products, you can hide the shipping info like this:
add_action('add_meta_boxes_product','remove_unwanted_mp_meta_boxes',999);
function remove_unwanted_mp_meta_boxes() {
	// shipping meta box
	remove_meta_box('mp-meta-shipping','product','normal');
	// download file box
    	// remove_meta_box('mp-meta-download','product','normal');
}
*/

/*
 * Function to build the encrypted hyperlink
 */
function wp2moodle_generate_hyperlink($cohort,$group,$course,$activity = 0) {

	// needs authentication; ensure userinfo globals are populated
	global $current_user;
    wp_get_current_user();

	$update = get_option('wp2m_update_details') ?: "true";

    $enc = array(
		"offset" => rand(1234,5678),						// just some junk data to mix into the encryption
		"stamp" => time(),									// unix timestamp so we can check that the link isn't expired
		"firstname" => $current_user->user_firstname,		// first name
		"lastname" => $current_user->user_lastname,			// last name
		"email" => $current_user->user_email,				// email
		"username" => $current_user->user_login,			// username
		"passwordhash" => $current_user->user_pass,			// hash of password (we don't know/care about the raw password)
		"idnumber" => $current_user->ID,					// int id of user in this db (for user matching on services, etc)
		"cohort" => $cohort,								// string containing cohort to enrol this user into
		"group" => $group,									// string containing group to enrol this user into
		"course" => $course,								// string containing course id, optional
		"updatable" => $update,								// if user profile fields can be updated in moodle
		"activity" => $activity						// index of first [visible] activity to go to, if auto-open is enabled in moodle
	);

	// encode array as querystring
	$details = http_build_query($enc);

	// encryption = 3des using shared_secret
	return rtrim(get_option('wp2m_moodle_url'),"/").WP2M_MOODLE_PLUGIN_URL.encrypt_string($details, get_option('wp2m_shared_secret'));
	//return get_option('wp2m_moodle_url').WP2M_MOODLE_PLUGIN_URL.'=>'.$details;
}

/**
 * initialiser for registering scripts to the rich editor
 */
function wp2m_register_addbutton() {
	if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {
	    add_filter('mce_external_plugins', 'wp2m_add_plugin');
	    add_filter('mce_buttons', 'wp2m_register_button');
	}
}
function wp2m_register_button($buttons) {
   array_push($buttons,"|","wp2m"); // pipe = break on toolbar
   return $buttons;
}
function wp2m_add_plugin($plugin_array) {
   $plugin_array['wp2m'] = plugin_dir_url(__FILE__).'wp2m.js';
   return $plugin_array;
}

?>
