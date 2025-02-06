<?php

// Add credit links in plugins list
function flying_scripts_add_shortcuts($links) {
    $plugin_shortcuts = array(
        '<a href="'.admin_url('options-general.php?page=flying-scripts').'">Settings</a>'
    );

    if (!defined('FLYING_PRESS_VERSION')) {
        $plugin_shortcuts[] =
      '<a href="https://flyingpress.com?ref=flying-scripts" target="_blank" style="color:#3db634;">Get FlyingPress</a>';
    }


    return array_merge($links, $plugin_shortcuts);
}
