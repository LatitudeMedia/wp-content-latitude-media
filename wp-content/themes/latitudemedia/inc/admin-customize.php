<?php
function add_category_column_section( $content, $column_name, $term_id ) {
    switch ( $column_name ) {
        case 'section':
            $section = get_field( 'section', 'category_' . $term_id );
            $content = $section ? sprintf('<a href="%s" target="_blank">%s</a>', get_edit_term_link($section->term_id, 'section'), $section->name) : '';
            break;
        default:
            break;
    }

    return $content;
}
add_filter( 'manage_category_custom_column', 'add_category_column_section', 10, 3 );

function add_new_category_column_section( $columns ) {
    $columns['section'] = __( 'Section', 'ltm' );
    return $columns;
}
add_filter( 'manage_edit-category_columns', 'add_new_category_column_section' );

if( !function_exists( 'ltm_admin_styles' ) ) {
    /**
     * Enqueue site styles
     */
    function ltm_admin_styles() {
        if ( !is_user_logged_in() ) {
            return;
        }

        wp_register_style('ltm-admin-front', get_template_directory_uri() . '/dist/css/admin.min.css', array(), filemtime( get_template_directory() . '/dist/css/admin.min.css') );
        wp_enqueue_style('ltm-admin-front');
    }
    add_action( 'wp_enqueue_scripts', 'ltm_admin_styles' );
}