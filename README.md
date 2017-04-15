wp2moodle
=========

Wordpress-to-Moodle pass through authentication plugin (wordpress end). It takes the user that is logged onto wordpress and passes their details over to Moodle (encrypted using openssl), authenticates them, enrols them in one or more courses, then opens the course.

> Note: The link that is gnerated is internally timestamped and will expire to reduce bookmarking or sharing. *How long* before it expires is configured in the  [Moodle-end plugin](https://github.com/frumbert/wp2moodle-moodle).

Demo / Further documenation
---------------------------
Go to my wordpress demo site and register yourself, then try the links.

http://wp2moodle.coursesuite.ninja/

E-Commerce integration
----------------------
This plugin automatically integrates with MarketPress and WooCommerce. BUT! Since wpmu & woo both only let you download a file, rather than calling a *dynamic* link or service, you'll have to jump through some hoops... Create and upload a text file *as* your digital download that contains the group and/or cohort names you want the user to enrol into, and it's business as usual. So you might have a file called "HistoryCourse-wp2moodle.txt" which contains your passthrough parameters, for instance:

    cohort=History101
    group=semester2_2015
    course=abc123

Upload this file as your digital download, and that's all. A sale will cause a 301 redirect to the configured Moodle server with the SSO token.

> Note: The "download" file must be a text file and its name must end in **-wp2moodle.txt** for this to work.

Activating and configuring the plugin
-------------------------------
Note, you must to rename the zip to be just 'wp2moodle.zip' before you upload the plugin to wordpress. If the zip extracts to a sub-folder, it won't work!

1. Download the plugin, unzip it, **rename** the folder to be "wp2moodle"
2. Place the plugin in your *~/wp-content/plugins* folder.
3. In wp-admin, go to the plugins list and activate the plugin
4. Click wp2moodle on the wordpress menu
5. Set your moodle url (e.g. http://your-site.com/moodle/)
6. Set the shared secret. This is a salt that is used to encrypt data that is sent to Moodle. Using a guid (http://www.newguid.org/) is a good idea. It must match the shared secret that you use on the Moodle plugin. (https://github.com/frumbert/wp2moodle-moodle)
7. Other instructions are available on the settings page.

How to use the plugin
------------------
1. Edit a post or a page
2. Use the moodle button on the editor to insert the shortcode block around the text you want linked
3. When authenticated as subscriber, contributor, etc, click on the link.

> Note: If the user is not yet authenticated, no hyperlink is rendered. It's also best to avoid linking the admin accounts.

Shortcode examples
------------------

`[wp2moodle class='my-class' cohort='course1' target='_blank']<img src='path.gif'>Open my course[/wpmoodle]`

`[wp2moodle group='group2']A hyperlink[/wp2moodle]`

    `class`: the css classname to apply to the link (default: wp2moodle)
    `target`: the hyperlink target name to apply to the link (defaut: _self)
    `cohort`: (optional) the id [mdl_cohort.idnumber] of the moodle cohort in which to enrol the user (can be a comma-seperated list for multiple enrolments)
    `group`: (optional) the id [mdl_groups.idnumber] of the moodle group in which to enrol the user (can be a comma-seperated list for multiple enrolments)
    `course`: (optional) the id [mdl_courses.idnumber] of the moodle course in which to enrol the user (can be a comma-seperated list for multiple enrolments)
    `authtext`: (optional) the text that appears instead when the user is not yet logged on
    `activity`: (optional, number) the numerical index of an activity to open after enrolment. E.g. "2" will attempt to open the second activity in the destination course.

Requirements:
-------------
*openssl* needs to be present and available in your php installation. It's likely that it is.

Licence:
--------
GPL2, as per Moodle.
