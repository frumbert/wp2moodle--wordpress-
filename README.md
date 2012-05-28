wp2moodle--wordpress-
=====================

Wordpress to Moodle pass through authentication plugin (wordpress end). Takes the user that is logged onto wordpress and passes their details over to Moodle, enrols them and authenticates.

1. Upload this to your wordpress
2. Activate the plugin
3. Click wp2moodle on the menu
4. Set your moodle url (e.g. http://your-site.com/moodle/) and shared secret
5. Use the moodle button on the editor to insert shortcodes around the text you want linked

Shortcode example
-----------------

[wp2moodle class='my-class' cohort='course1' target='_blank']my link[/wpmoodle]

class: the css classname to apply to the link (default: wp2moodle)
target: the hyperlink target name to apply to the link (defaut: _self)
cohort: the name of the moodle cohort in which to enrol the user

Licence:
--------
GPL2, as per Moodle.
