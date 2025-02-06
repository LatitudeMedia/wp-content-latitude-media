<?php
// Set default config on plugin load if not set
function flying_scripts_set_default_config() {

    if (FLYING_SCRIPTS_VERSION !== get_option('FLYING_SCRIPTS_VERSION')) {
        
        if (get_option('flying_scripts_timeout') === false)
            update_option('flying_scripts_timeout', 5);

        if (get_option('flying_scripts_include_list') === false)
            update_option('flying_scripts_include_list', []);

        if (get_option('flying_scripts_disabled_pages') === false)
            update_option('flying_scripts_disabled_pages', []);

        update_option('FLYING_SCRIPTS_VERSION', FLYING_SCRIPTS_VERSION);
    }
}

add_action('plugins_loaded', 'flying_scripts_set_default_config');
