wp2moodle--wordpress-
=====================

Wordpress to Moodle pass through authentication plugin (wordpress end). Takes the user that is logged onto wordpress and passes their details over to Moodle, enrols them and authenticates.

Demo
----
Go to my wordpress demo site and register yourself, then try the links.

http://wordpress.frumbert.org/


Activating and configuring the plugin
-------------------------------
Note, you must to rename the zip to be just 'wp2moodle.zip' before you upload the plugin to wordpress. If the zip extracts to a sub-folder, it won't work!

1. Upload this to your wordpress (should end up being called /wp-content/plugins/wp2moodle/)
2. Activate the plugin
3. Click wp2moodle on the wordpress menu
4. Set your moodle url (e.g. http://your-site.com/moodle/)
5. Set the shared secret. This is a salt that is used to encrypt data that is sent to Moodle. Using a guid (http://newguid.com) is a good idea. It must match the shared secret that you use on the Moodle plugin. (https://github.com/frumbert/wp2moodle-moodle)
6. Other instructions are available on the settings page.

How to use the plugin
------------------
1. Edit a post or a page
2. Use the moodle button on the editor to insert shortcodes around the text you want linked
3. When authenticated as subscriber, contributor, etc, click on the link.

Note: If the user is not yet authenticated, no hyperlink is rendered. The link does not function for Wordpress admins.

Shortcode examples
------------------

[wp2moodle class='my-class' cohort='course1' target='_blank']<img src='path.gif'>Open my course[/wpmoodle]

[wp2moodle group='group2']A hyperlink[/wp2moodle]

class: the css classname to apply to the link (default: wp2moodle)
target: the hyperlink target name to apply to the link (defaut: _self)
cohort: (optional) the id [mdl_cohort.idnumber] of the moodle cohort in which to enrol the user
group: (optional) the id [mdl_groups.idnumber] of the moodle group in which to enrol the user

Licence:
--------
GPL2, as per Moodle.
