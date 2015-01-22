<?php require_once(ABSPATH . 'wp-includes/pluggable.php'); ?>
<div class="wrap">
<h2><?php print WP2M_PUGIN_NAME ." ". WP2M_CURRENT_VERSION ?></h2>
<p>This plugin allows you to place a shortcode in a post that passes encrypted logon information to Moodle, using the <a href="https://github.com/frumbert/wp2moodle-moodle">Wp2Moodle authentication plugin</a> for Moodle 2.2+. The user will be created (if required) by Moodle and optionally enrolled in the specified Cohort(s) or Group(s).</p>
<p>Use the Moodle button on the rich editor to insert the shortcode, or enter the details manually using the examples below as a guide.</p>
<p>Example: <em>[wp2moodle class='css-classname' group='group1' cohort='class1' target='_blank' authtext='Please log on']launch the course[/wp2moodle]</em>.</p>
<p>Group enrolment example: <em>[wp2moodle group='group1']enrol in group 1[/wp2moodle]</em>.</p>
<p>Cohort enrolment example: <em>[wp2moodle cohort='business_cert3']enrol in Cert 3 Business[/wp2moodle]</em>.</p>
<p>Specifying multiple groups: <em>[wp2moodle group='eng14_a,math14_b,hist13_c']Math, English & History[/wp2moodle]</em>.</p>
<h2>Shortcode attributes:</h2>
<ul>
	<li><em>class</em>: optional, defaults to 'wp2moodle'; CSS class attribute of link</li>
	<li><em>cohort</em>: optional, idnumber of the cohort in which to enrol a user at the moodle end. You can specify multiple values using comma seperated strings.</li>
	<li><em>group</em>: optional, idnumber of the group in which to enrol a user at the moodle end (typically you use group <i>or</i> cohort). You can specify multiple values using comma seperated strings.</li>
	<li><em>target</em>: optional, defaults to '_self'; href target attribute of link</li>
	<li><em>authtext</em>: optional, defaults to content between shortcode tags; string to display if not yet logged on</li>
</ul>
<p class="description">Note: The link that is generated is timestamped and will expire, so it cannot be bookmarked or hijacked. You must set the expiry time in the Moodle plugin. You should allow reading time of the page when considering a timeout value, since the link is generated when the page is loaded, not when the link is clicked. </p>
<h2>Settings</h2>
<form method="post" action="options.php">
    <?php
		settings_fields( 'wp2m-settings-group' );
	?>
    <table class="form-table">
        <tr valign="top">
	        <th scope="row">Moodle Root URL</th>
	        <td><input type="text" name="wp2m_moodle_url" value="<?php echo get_option('wp2m_moodle_url'); ?>" size="60" />
	        <div class="description">Plugin will append the url <em style="text-decoration:underline"><?php echo WP2M_MOODLE_PLUGIN_URL ?></em>.</div>
	        </td>
        </tr>
         
        <tr valign="top">
    	    <th scope="row">Encryption secret</th>
        	<td><input type="text" name="wp2m_shared_secret" value="<?php echo get_option('wp2m_shared_secret'); ?>" size="60" />
        	<div class="description">Must match setting on Moodle plugin; a <a href="http://newguid.com/" target="_blank">GUID</a> makes a good key.</div>
        	</td>
        </tr>

        <tr valign="top">
	        <th scope="row">Update existing users</th>
	        <td>
	        	<label><input type="radio" name="wp2m_update_details" value="true" <?php echo (get_option('wp2m_update_details') != "false") ? "checked" : "" ?> /> Yes </label>
	        	<label><input type="radio" name="wp2m_update_details" value="false" <?php echo (get_option('wp2m_update_details') == "false") ? "checked" : "" ?> /> No </label>
				<div class="description">Whether Moodle will update the profile fields in Moodle for existing users.</div>
	        </td>
        </tr>
 
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
