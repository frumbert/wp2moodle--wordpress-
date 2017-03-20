<?php require_once(ABSPATH . 'wp-includes/pluggable.php'); ?>
<style>
.wp2m-table {width:100%;background-color:#fff;}
.wp2m-table td, .wp2m-table th { padding: 10px; vertical-align: top; align: left;}
.wp2m-table code {line-height:1.5;background-color:transparent;}
.wp2m-error {border-left:4px solid #c00;background-color:#fff;padding:10px;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);}
</style>
<div class="wrap">


<?php if (!extension_loaded('openssl')) { ?>
    <div class="wp2m-error">
    <h2>Warning!</h2><p>The <em>openssl</em> php extension has not been detected. You'll need to fix your PHP configuration before this plugin will operate.</p>
    </div>
<?php } ?>

<h2><?php print WP2M_PUGIN_NAME ." ". WP2M_CURRENT_VERSION ?></h2>
<p>This plugin allows you to place a shortcode in a post that passes encrypted logon information to Moodle, using the <a href="https://github.com/frumbert/wp2moodle-moodle">Wp2Moodle authentication plugin</a> for Moodle 2.2+. The user will be created (if required) by Moodle and optionally enrolled in the specified Cohort(s), Course(s) and/or Group(s).</p>
<p>Use the Moodle button on the rich editor to insert the shortcode, or enter the details manually using the examples below as a guide.</p>
<p>Example: <code>[wp2moodle class='css-classname' group='group1' cohort='class1' target='_blank' authtext='Please log on']launch the course[/wp2moodle]</code>.</p>
<table class="wp2m-table">
    <thead><tr><th>Attribute</th><th>Purpose</th><th>Example</th></tr></thead>
    <tbody>
    <tr><td><code>class</code></td><td> optional, defaults to 'wp2moodle'; CSS class attribute of link</td></tr>
    <tr><td><code>cohort</code></td><td> optional, idnumber of the cohort in which to enrol a user at the moodle end. You can specify multiple values using comma seperated strings.</td><td><code>[wp2moodle cohort='business_cert3']enrol in Cert 3 Business[/wp2moodle]</code></td></tr>
    <tr><td><code>group</code></td><td> optional, idnumber of the group in which to enrol a user at the moodle end (typically you use group <i>or</i> cohort). You can specify multiple values using comma seperated strings.</td><td><code>[wp2moodle group='eng14_a,math14_b,hist13_c']Math, English & History[/wp2moodle]</code></td></tr>
    <tr><td><code>course</code></td><td> optional, idnumber of the course in which to enrol a user at the moodle end. You can use multiple ids</td><td>(as above)</td></tr>
    <tr><td><code>target</code></td><td> optional, defaults to '_self'; href target attribute of link</td><td><code>target="_blank"</code></td></tr>
    <tr><td><code>authtext</code></td><td> optional, defaults to content between shortcode tags; string to display if not yet logged on</td></tr>
    <tr><td><code>activity</code></td><td> optional, numerical index of the first activity to open (> 0) if auto-open is enabled in the Moodle plugin</td></tr>
    </tbody>
</table>
<p class="description">The <em>idnumber</em> mentioned above is not the same as the course id (which is a number); moodle has a special field called "idnumber" which is an alphanumeric value. If you mix them up, it won't work!</p>
<p class="description">The link that is generated is timestamped and will expire, so it cannot be bookmarked or hijacked. You must set the expiry time in the Moodle plugin. You should allow reading time of the page when considering a timeout value, since the link is generated when the page is loaded, not when the link is clicked. </p>

<?php if (class_exists('WooCommerce') || class_exists('MarketPress')) { ?>
<h3>MarketPress or WooCommerce?</h3>
<p>Selling with MarketPress or WooCommerce? Create a text file called "yourcourse-wp2moodle.txt" (actually, the name only has to <strong>end with</strong> <code>-wp2moodle.txt</code>; the name before that can be whatever you like) and write ay attributes (shown above) on their own lines into it, like this:</p>
<table class="wp2m-table">
    <tr><td>
        <code>group=maths102_sem2</code><br>
        <code>cohort=2015allCourses</code>
    </td></tr>
</table>
<?php } ?>
<p>Upload this file as your digital download for the product. Then, after a purchase instead of a download, they will redirect to your Moodle site with an authentication token just like a shortcode link. Pretty neat, huh?</p>

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
            <th scope="row">Encryption secret<br><span style='font-weight:normal'>Must match Moodle</span></th>
            <td><input type="text" name="wp2m_shared_secret" value="<?php echo get_option('wp2m_shared_secret'); ?>" size="60" />
            <div class="description">Here is a freshly generated secure key: <code><?php echo base64_encode(openssl_random_pseudo_bytes(32)); ?></code>.</div>
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
