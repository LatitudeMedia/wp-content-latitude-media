<?php

$blocks = array(
    array(
        'attrs' => array(
            'name'  		=> 'news-with-hero-section',
            'title' 		=> __('News with hero section', 'ltm'),
            'path'  		=> 'homepage',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('News with hero section block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('News with hero section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'news-with-hero-section.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'news-list-section',
            'title' 		=> __('News list section', 'ltm'),
            'path'  		=> 'homepage',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('News list section block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('News list section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'news-list-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'spotlight-quote-section',
            'title' 		=> __('Spotlight quote section', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Spotlight quote section block', 'ltm'),
        'post_types' 	=> array( 'post' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Spotlight quote section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'spotlight-quote-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'in-house-ad-section',
            'title' 		=> __('In house ad section', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('In house ad section block', 'ltm'),
        'post_types' 	=> array( 'post' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('In house ad section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'in-house-ad-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'signup-form-section',
            'title' 		=> __('Signup form section', 'ltm'),
            'path'  		=> 'article',
            'display' => true,
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Signup form section block', 'ltm'),
        'post_types' 	=> array( 'post' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Signup form section', 'ltm') ),
        'mode' => false,
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'signup-form-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'inline-podcast-section',
            'title' 		=> __('Inline podcast section', 'ltm'),
            'path'  		=> 'article',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Inline podcast section block', 'ltm'),
        'post_types' 	=> array( 'post' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Inline podcast section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'inline-podcast-section.png',
                )
            )
        )
    ),

    // Sidebar blocks
    array(
        'attrs' => array(
            'name'  		=> 'sidebar-editors-picks-section',
            'title' 		=> __('Sidebar editors picks section', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Sidebar editors picks section block', 'ltm'),
//        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Sidebar editors picks section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'sidebar-editors-picks-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'sidebar-form-section',
            'title' 		=> __('Sidebar form section', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Sidebar form section block', 'ltm'),
//        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Sidebar form section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'sidebar-form-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'sidebar-news-list-section',
            'title' 		=> __('Sidebar news list section', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Sidebar news list section block', 'ltm'),
//        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Sidebar news list section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'sidebar-news-list-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'related-reading-section',
            'title' 		=> __('Related reading section', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Related reading section block', 'ltm'),
//        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Related reading section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'related-reading-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'sidebar-ad-banner-section',
            'title' 		=> __('Sidebar ad banner section', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Sidebar ad banner section block', 'ltm'),
//        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Sidebar ad banner section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'sidebar-ad-banner-section.png',
                )
            )
        )
    ),
    // END Sidebar blocks

    array(
        'attrs' => array(
            'name'  		=> 'ad-banner-section',
            'title' 		=> __('Ad banner section', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Ad banner section block', 'ltm'),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Ad banner section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'ad-banner-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'three-podcasts-section',
            'title' 		=> __('Three podcasts section', 'ltm'),
            'path'  		=> 'homepage',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Three podcasts section block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Three podcasts section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'three-podcasts-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'large-event-section',
            'title' 		=> __('Large event section', 'ltm'),
            'path'  		=> 'homepage',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('Large event section block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Large event section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'large-event-section.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'news-plates-section',
            'title' 		=> __('News plates section', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'table-col-before',
        'description' => __('News plates section block', 'ltm'),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('News plates section', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'news-plates-section.png',
                )
            )
        )
    ),
);

new ACFBlocks( $blocks );

if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(
        array(
            'page_title' => 'Theme Options',
            'menu_title' => 'Theme Options',
            'menu_slug'  => 'acf-options',
            'redirect'   => false,
        )
    );
}

if( function_exists('acf_add_options_sub_page') ) {
    acf_add_options_sub_page(array(
        'page_title' => 'Footer Settings',
        'menu_title' => 'Footer Settings',
        'parent_slug' => 'acf-options',
    ));
}

if( !function_exists('relationship_orderby_date') ) {

	/**
	 * Adding filter that allow ordering posts in search by date
	 *
	 * @param array $args The arguments.
	 * @param string $field The field.
	 * @param int $post_id The post ID.
	 * @return mixed
	 */
	function relationship_orderby_date($args, $field, $post_id)
	{
		$args['orderby'] = 'date';
		$args['order'] = 'desc';
		return $args;
	}

	add_filter('acf/fields/relationship/query', 'relationship_orderby_date', 10, 3);
}

if( !function_exists('acf_load_articles_with_sidebar_choices') ) {

    /**
     * Add registered custon sidebars to select field
     *
     * @param $field
     * @return mixed
     */
    function acf_load_articles_with_sidebar_choices($field)
    {

        $sidebar_areas = get_field('sidebar_areas', 'options');
        foreach ($sidebar_areas as $key => $sidebar) {
            $choices['custom-sidebar-' . $key] = $sidebar['name'];
        }

        if ($field['name'] === 'articles_with_sidebar_section') {
            foreach ($field['sub_fields'] as $key => $sub_fild) {
                if ($sub_fild['name'] === 'sidebar') {
                    $field['sub_fields'][$key]['choices'] = $choices;
                }
            }
        } else {
            $field['choices'] = $choices;
        }

        return $field;
    }

    add_filter('acf/load_field/name=sidebar_widget', 'acf_load_articles_with_sidebar_choices');
}

function registerCustomBlocks() {
    acf_register_block_type([
        'name' => 'begin-sidebar',
        'title' => 'Begin Sidebar',
        'path' => 'homepage',
        'render_template' => 'template-parts/blocks/homepage/begin-sidebar.php',
        'icon' => 'insert-before',
        'mode' => 'preview',
        'display' => true
    ]);
    acf_register_block_type([
        'name' => 'end-sidebar',
        'title' => 'End Sidebar',
        'path' => 'homepage',
        'render_template' => 'template-parts/blocks/homepage/end-sidebar.php',
        'icon' => 'insert-after',
        'mode' => 'preview',
        'display' => true
    ]);
}
add_action('acf/init', 'registerCustomBlocks');

function custom_insert_post_data( $data, $postarr ) {
    if (0 === $postarr['ID'] || $postarr['ID'] !== intval(get_option( 'page_on_front' )) ) {
        return $data;
    }

    $blocks = parse_blocks( wp_unslash( $data['post_content'] ) );

    $startSidebar = false;
    $endSidebar = false;
    foreach ( $blocks as $block ) {
        if ( 'acf/begin-sidebar' == $block['blockName'] ) {
            $startSidebar = true;
        }
        if ( 'acf/end-sidebar' == $block['blockName'] ) {
            $endSidebar = true;
        }
    }

    if($startSidebar && !$endSidebar) {
        $data['post_content'] .= '<!-- wp:acf/end-sidebar {"name":"acf/end-sidebar","data":{"sidebar_widget":"","_sidebar_widget":"field_6710e10a80a0a"},"mode":"preview"} /-->';
    }
    if(!$startSidebar && $endSidebar) {
        $data['post_content'] = '<!-- wp:acf/begin-sidebar {"name":"acf/begin-sidebar","data":[],"mode":"preview"} /-->' . $data['post_content'];
    }

    return $data;
}

add_filter( 'wp_insert_post_data', 'custom_insert_post_data', 10, 2 );