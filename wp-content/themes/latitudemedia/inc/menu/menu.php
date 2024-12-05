<?php

require_once ("class-main-menu-walker.php");
require_once ("class-accordion-menu-walker.php");

// Add color to each menu item and set inline style color
if( !function_exists('add_top_menu_atts') ) {
    /**
     * @param $atts
     * @param $item
     * @param $args
     * @return mixed
     */
    function add_top_menu_atts($atts, $item, $args){
        if( !$args->theme_location == 'top' ) {
            return $atts;
        }

        $color = get_field('color', $item);
        if(!empty($color)) {
            $atts['style'] = "--top-menu-hover-color: {$color}";
        }

        return $atts;
    }
    add_filter('nav_menu_link_attributes', 'add_top_menu_atts', 10, 3);
}

add_filter('wp_nav_menu_objects', 'wp_nav_menu_logos', 10, 2);

function wp_nav_menu_logos( $items, $args ) {

    if( $args->theme_location == 'footer-social' || $args->theme_location == 'footer-logos' ) {
        $size = ' width="180" height="70"';
        if($args->theme_location == 'footer-social') {
            $size = ' width="24" height="24"';
        }
        foreach ($items as &$item) {
            $logo = get_field('logo', $item);
            if ($logo) {
                $item->title = sprintf('<img src="%1$s" alt="%2$s" %3$s />', $logo, $item->title, $size);
            }

        }
    }

    return $items;
}

?>