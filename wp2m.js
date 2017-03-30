// purpose: register a wp2moodle button in the rich editor in the admin interface
(function() {
    tinymce.create('tinymce.plugins.wp2m', {
        init : function(ed, url) {
            ed.addButton('wp2m', {
                title : 'Wordpress 2 Moodle',
                image : url+'/icon.svg',
                onclick : function() {
                     ed.selection.setContent('[wp2moodle cohort="" group="" course="" class="wp2moodle" target="_self" authtext="" activity="0"]' + ed.selection.getContent() + '[/wp2moodle]');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('wp2m', tinymce.plugins.wp2m);
})();
