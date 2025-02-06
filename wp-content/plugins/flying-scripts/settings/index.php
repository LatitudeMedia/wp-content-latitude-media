<?php

// Register settings menu
function flying_scripts_register_settings_menu() {
    add_options_page('Flying Scripts', 'Flying Scripts', 'manage_options', 'flying-scripts', 'flying_scripts_view_view');
}
add_action('admin_menu', 'flying_scripts_register_settings_menu');

// Settings page
function flying_scripts_view_view() {
    // Validate nonce
    if (isset($_POST['submit']) && !wp_verify_nonce($_POST['flying-scripts-settings-form'], 'flying-scripts')) {
        echo '<div class="notice notice-error"><p>Nonce verification failed</p></div>';
        exit;
    }

    // Settings
    include 'view.php';
}
