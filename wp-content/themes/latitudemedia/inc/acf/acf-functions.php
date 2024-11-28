<?php

$blocks = array(
    // START COMMON blocks

    array(
        'attrs' => array(
            'name'  		=> 'content-wrapper',
            'title' 		=> __('Content wrapper', 'ltm'),
            'path'  		=> 'common',
            'display'  		=> true,
        ),
        'icon'  		=> 'align-center',
        'description' => __('Content wrapper', 'ltm'),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Content wrapper', 'ltm') ),
        "supports" =>  array(
            "jsx" =>  true,
            "anchor" =>  true,
            "color" => true,
            "baseColor" => true,
            "align" => true,
            "mode"  => false
        ),
        'mode' => 'preview',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'ad-banner-section',
            'title' 		=> __('Ad banner block', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'pressthis',
        'description' => __('Ad banner block', 'ltm'),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Ad banner block', 'ltm') ),
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
            'name'  		=> 'logo-and-text',
            'title' 		=> __('Logo and text', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'align-left',
        'description' => __('Logo and text', 'ltm'),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Logo and text', 'ltm') ),
        "supports" =>  array(
            "jsx" =>  true,
        ),
        'mode' => 'preview',
        'enqueue_assets' => function(){
            wp_enqueue_style( 'block-acf-logo-and-text', get_template_directory_uri() . '/dist/css/blocks/logo-and-text.min.css' );
            wp_enqueue_style( 'block-acf-image-and-text-block', get_template_directory_uri() . '/dist/css/blocks/image-and-text-block.min.css' );
        },
        'styles'  => [
            [
                'name' => 'default',
                'label' => __('Default', 'ltm'),
                'isDefault' => true,
            ],
            [
                'name' => 'type2',
                'label' => __('Type 2', 'ltm'),
                'isDefault' => true,
            ],
            [
                'name' => 'type3',
                'label' => __('Type 3', 'ltm'),
                'isDefault' => true,
            ],
            [
                'name' => 'type4',
                'label' => __('Type 4', 'ltm'),
                'isDefault' => true,
            ],
        ],
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'logo-and-text.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'content-with-background-block',
            'title' 		=> __('Content with background block', 'ltm'),
            'path'  		=> 'common',
            'display'  		=> true,
        ),
        'icon'  		=> 'align-full-width',
        'description' => __('Content with background block', 'ltm'),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Content with background block', 'ltm') ),
        "supports" =>  array(
            "jsx" =>  true,
        ),
        'mode' => 'preview',
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/content-with-background-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'content-with-background-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'styled-button-block',
            'title' 		=> __('Styled button block', 'ltm'),
            'path'  		=> 'common',
            'display'  		=> true,
        ),
        'icon'  		=> 'button',
        'description' => __('Styled button block', 'ltm'),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Styled button block', 'ltm') ),
        "supports" =>  array(
            "jsx" =>  true,
        ),
        'mode' => 'preview',
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/styled-button-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'styled-button-block.png',
                )
            )
        )
    ),
    // END COMMON blocks

    // START POST blocks
    array(
        'attrs' => array(
            'name'  		=> 'spotlight-quote-section',
            'title' 		=> __('Spotlight quote block', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'testimonial',
        'description' => __('Spotlight quote block', 'ltm'),
        'post_types' 	=> array( 'post', 'events' ),
        'category'  	=> 'ltm-post-blocks',
        'keywords'    => array( __('Spotlight quote block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/spotlight-quote-section.min.css',
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
            'title' 		=> __('In house ad block', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'media-document',
        'description' => __('In house ad block', 'ltm'),
        'post_types' 	=> array( 'post' ),
        'category'  	=> 'ltm-post-blocks',
        'keywords'    => array( __('In house ad block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/in-house-ad-section.min.css',
        'mode' => 'preview',
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
            'title' 		=> __('Signup form block', 'ltm'),
            'path'  		=> 'article',
            'display' => true,
        ),
        'icon'  		=> 'email-alt',
        'description' => __('Signup form block', 'ltm'),
        'post_types' 	=> array( 'post' ),
        'category'  	=> 'ltm-post-blocks',
        'keywords'    => array( __('Signup form block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/signup-form-section.min.css',
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
            'title' 		=> __('Inline podcast block', 'ltm'),
            'path'  		=> 'article',
        ),
        'icon'  		=> 'embed-audio',
        'description' => __('Inline podcast block', 'ltm'),
        'post_types' 	=> array( 'post' ),
        'category'  	=> 'ltm-post-blocks',
        'keywords'    => array( __('Inline podcast block', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'inline-podcast-section.png',
                )
            )
        )
    ),
    // END POST blocks

    // Sidebar blocks
    array(
        'attrs' => array(
            'name'  		=> 'sidebar-editors-picks-section',
            'title' 		=> __('Sidebar editors picks block', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'edit-page',
        'description' => __('Sidebar editors picks block', 'ltm'),
        'category'  	=> 'ltm-sidebar-blocks',
        'keywords'    => array( __('Sidebar editors picks block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/sidebar-editors-picks-section.min.css',
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
            'title' 		=> __('Sidebar form block', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'email-alt2',
        'description' => __('Sidebar form block', 'ltm'),
        'category'  	=> 'ltm-sidebar-blocks',
        'keywords'    => array( __('Sidebar form block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/signup-form-section.min.css',
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
            'title' 		=> __('Sidebar news list block', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'align-pull-left',
        'description' => __('Sidebar news list block', 'ltm'),
        'category'  	=> 'ltm-sidebar-blocks',
        'keywords'    => array( __('Sidebar news list block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/news-list-section.min.css',
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
            'title' 		=> __('Related reading block', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'star-filled',
        'description' => __('Related reading block', 'ltm'),
        'category'  	=> 'ltm-sidebar-blocks',
        'keywords'    => array( __('Related reading block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/related-reading-section.min.css',
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
            'title' 		=> __('Sidebar ad banner block', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'pressthis',
        'description' => __('Sidebar ad banner block', 'ltm'),
        'category'  	=> 'ltm-sidebar-blocks',
        'keywords'    => array( __('Sidebar ad banner block', 'ltm') ),
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'sidebar-ad-banner-section.png',
                )
            )
        )
    ),

    array(
        'attrs' => array(
            'name'  		=> 'sidebar-info-block',
            'title' 		=> __('Sidebar info block', 'ltm'),
            'path'  		=> 'sidebar',
        ),
        'icon'  		=> 'info',
        'description' => __('Sidebar info block', 'ltm'),
        'category'  	=> 'ltm-sidebar-blocks',
        'keywords'    => array( __('Sidebar info block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/sidebar-info-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'sidebar-info-block.png',
                )
            )
        )
    ),
    // END Sidebar blocks

    // START PAGE blocks
    array(
        'attrs' => array(
            'name'  		=> 'begin-sidebar',
            'title' 		=> __('Begin Sidebar', 'ltm'),
            'path'  		=> 'homepage',
            'display'  		=> true,
        ),
        'icon'  		=> 'insert-before',
        'description' => __('Begin Sidebar', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-sidebar-blocks',
        'keywords'    => array( __('Begin Sidebar', 'ltm') ),
        'mode' => 'preview',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'custom-sidebar.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'end-sidebar',
            'title' 		=> __('End Sidebar', 'ltm'),
            'path'  		=> 'homepage',
            'display'  		=> true,
        ),
        'icon'  		=> 'insert-after',
        'description' => __('End Sidebar', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-sidebar-blocks',
        'keywords'    => array( __('End Sidebar', 'ltm') ),
        'mode' => 'preview',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'custom-sidebar.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'news-with-hero-section',
            'title' 		=> __('News with hero block', 'ltm'),
            'path'  		=> 'homepage',
        ),
        'icon'  		=> 'table-row-before',
        'description' => __('News with hero block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('News with hero block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/news-with-hero.min.css',
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
            'title' 		=> __('News list block', 'ltm'),
            'path'  		=> 'homepage',
        ),
        'icon'  		=> 'feedback',
        'description' => __('News list block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('News list block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/news-list-section.min.css',
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
            'name'  		=> 'large-podcasts-section',
            'title' 		=> __('Large podcasts block', 'ltm'),
            'path'  		=> 'homepage',
        ),
        'icon'  		=> 'grid-view',
        'description' => __('Large podcasts block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Large podcasts block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/large-podcasts-section.min.css',
        'styles'  => [
            [
                'name' => 'default',
                'label' => __('Default', 'ltm'),
                'isDefault' => true,
            ],
            [
                'name' => 'type2',
                'label' => __('Type 2', 'ltm'),
                'isDefault' => true,
            ]
        ],
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'large-podcasts-section.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'large-event-section',
            'title' 		=> __('Large event block', 'ltm'),
            'path'  		=> 'homepage',
        ),
        'icon'  		=> 'cover-image',
        'description' => __('Large event block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Large event block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/featured-research-block.min.css',
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
            'title' 		=> __('News plates block', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'schedule',
        'description' => __('News plates block', 'ltm'),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('News plates block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/news-plates-section.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'news-plates-section.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'subscribe-form-block',
            'title' 		=> __('Subscribe form block', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'email-alt',
        'description' => __('Subscribe form block', 'ltm'),
        'post_types' 	=> array( 'page', 'sections-landing' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Subscribe form block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/subscribe-form-block.min.css',
        'styles'  => [
            [
                'name' => 'default',
                'label' => __('Default', 'ltm'),
                'isDefault' => true,
            ],
            [
                'name' => 'type2',
                'label' => __('Type 2', 'ltm'),
                'isDefault' => true,
            ],
            [
                'name' => 'type3',
                'label' => __('Type 3', 'ltm'),
                'isDefault' => true,
            ]
        ],
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'subscribe-form-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'authors-list-block',
            'title' 		=> __('Authors list block', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'admin-users',
        'description' => __('Authors list block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Authors list block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/authors-list-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'authors-list-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'our-team-block',
            'title' 		=> __('Our team block', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'groups',
        'description' => __('Our team block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Our team block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/authors-list-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'our-team-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'image-and-text-block',
            'title' 		=> __('Image and text block', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'align-pull-left',
        'description' => __('Image and text block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Image and text block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/image-and-text-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'image-and-text-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'featured-research-block',
            'title' 		=> __('Featured research block', 'ltm'),
            'path'  		=> 'research',
        ),
        'icon'  		=> 'chart-area',
        'description' => __('Featured research block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Featured research block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/featured-research-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'featured-research-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'partner-porcasts-block',
            'title' 		=> __('Partner porcasts block', 'ltm'),
            'path'  		=> 'podcast',
        ),
        'icon'  		=> 'businessman',
        'description' => __('Partner porcasts block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Partner porcasts block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/partner-porcasts-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'partner-porcasts-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'info-cta-block',
            'title' 		=> __('Info cta block', 'ltm'),
            'path'  		=> 'common',
        ),
        'icon'  		=> 'info',
        'description' => __('Info cta block', 'ltm'),
        'post_types' 	=> array( 'page', 'events' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Info cta block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/info-cta-block.min.css',
        'mode' => 'preview',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'info-cta-block.png',
                )
            )
        )
    ),
    // END PAGE blocks


    // START Section landing
    array(
        'attrs' => array(
            'name'  		=> 'categories-section-block',
            'title' 		=> __('Categories section block', 'ltm'),
            'path'  		=> 'section',
        ),
        'icon'  		=> 'editor-insertmore',
        'description' => __('Categories section block', 'ltm'),
        'post_types' 	=> array( 'sections-landing' ),
        'category'  	=> 'ltm-section-landing-blocks',
        'keywords'    => array( __('Categories section block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/categories-section-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'categories-section-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'news-list-with-hero-section-block',
            'title' 		=> __('News list with hero section block', 'ltm'),
            'path'  		=> 'section',
        ),
        'icon'  		=> 'table-row-before',
        'description' => __('News list with hero section block', 'ltm'),
        'post_types' 	=> array( 'sections-landing' ),
        'category'  	=> 'ltm-section-landing-blocks',
        'keywords'    => array( __('News list with hero section block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/news-with-hero.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'news-list-with-hero-section-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'news-with-sidebar-section-block',
            'title' 		=> __('News with sidebar section block', 'ltm'),
            'path'  		=> 'section',
        ),
        'icon'  		=> 'align-pull-right',
        'description' => __('News with sidebar section block', 'ltm'),
        'post_types' 	=> array( 'sections-landing' ),
        'category'  	=> 'ltm-section-landing-blocks',
        'keywords'    => array( __('News with sidebar section block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/news-with-sidebar-section-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'news-with-sidebar-section-block.png',
                )
            )
        )
    ),
    // END Section landing

    // START Single Research
    array(
        'attrs' => array(
            'name'  		=> 'research-banner-block',
            'title' 		=> __('Research banner block', 'ltm'),
            'path'  		=> 'research',
        ),
        'icon'  		=> 'align-wide',
        'description' => __('Research banner block', 'ltm'),
        'post_types' 	=> array( 'research', 'order-reports' ),
        'category'  	=> 'ltm-research-blocks',
        'keywords'    => array( __('Research banner block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/research-banner-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'research-banner-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'research-preview-block',
            'title' 		=> __('Research preview block', 'ltm'),
            'path'  		=> 'research',
        ),
        'icon'  		=> 'welcome-view-site',
        'description' => __('Research preview block', 'ltm'),
        'post_types' 	=> array( 'research' ),
        'category'  	=> 'ltm-research-blocks',
        'keywords'    => array( __('Research preview block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/image-and-text-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'research-preview-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'research-overview-block',
            'title' 		=> __('Research overview block', 'ltm'),
            'path'  		=> 'research',
        ),
        'icon'  		=> 'welcome-widgets-menus',
        'description' => __('Research overview block', 'ltm'),
        'post_types' 	=> array( 'research' ),
        'category'  	=> 'ltm-research-blocks',
        'keywords'    => array( __('Research overview block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/research-overview-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'research-overview-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'order-preview-block',
            'title' 		=> __('Order preview block', 'ltm'),
            'path'  		=> 'order-report',
        ),
        'icon'  		=> 'welcome-view-site',
        'description' => __('Order preview block', 'ltm'),
        'post_types' 	=> array( 'order-reports' ),
        'category'  	=> 'ltm-order-report-blocks',
        'keywords'    => array( __('Order preview block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/image-and-text-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'order-preview-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'order-form-block',
            'title' 		=> __('Order form block', 'ltm'),
            'path'  		=> 'order-report',
        ),
        'icon'  		=> 'forms',
        'description' => __('Order form block', 'ltm'),
        'post_types' 	=> array( 'order-reports' ),
        'category'  	=> 'ltm-order-report-blocks',
        'keywords'    => array( __('Order form block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/order-form-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'order-form-block.png',
                )
            )
        )
    ),
    // END Single Research
    // START Events
    array(
        'attrs' => array(
            'name'  		=> 'events-list-block',
            'title' 		=> __('Events list block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'tickets-alt',
        'description' => __('Events list block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Events list block', 'ltm') ),
        'enqueue_assets' => function(){
            wp_enqueue_style( 'block-acf-events-list-block', get_template_directory_uri() . '/dist/css/blocks/events-list-block.min.css' );
            wp_enqueue_script( 'block-acf-events-list-block', get_template_directory_uri() . '/dist/js/blocks/load-more-events.min.js', array('jquery'), '', true );
        },
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'events-list-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-preview-block',
            'title' 		=> __('Event preview block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'welcome-view-site',
        'description' => __('Event preview block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event preview block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/event-preview-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-preview-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-description-block',
            'title' 		=> __('Event description block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'media-text',
        'description' => __('Event description block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event description block', 'ltm') ),
        "supports" =>  array(
            "jsx" =>  true,
        ),
        'styles'  => [
            [
                'name' => 'default',
                'label' => __('Default', 'ltm'),
                'isDefault' => true,
            ],
            [
                'name' => 'type2',
                'label' => __('Type 2 (with form)', 'ltm'),
                'isDefault' => true,
            ]
        ],
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/event-description-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-description-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-speakers-block',
            'title' 		=> __('Event speakers block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'megaphone',
        'description' => __('Event speakers block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event speakers block', 'ltm') ),
        'enqueue_style'     => get_template_directory_uri() . '/dist/css/blocks/authors-list-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-speakers-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-sponsors-block',
            'title' 		=> __('Event sponsors block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'money',
        'description' => __('Event sponsors block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event sponsors block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/event-sponsors-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-sponsors-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-agenda-block',
            'title' 		=> __('Event agenda block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'calendar-alt',
        'description' => __('Event agenda block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event agenda block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/event-agenda-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-agenda-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-venue-block',
            'title' 		=> __('Event venue block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'location-alt',
        'description' => __('Event venue block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event venue block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/event-venue-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-venue-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-short-description-block',
            'title' 		=> __('Event short description block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'text-page',
        'description' => __('Event short description block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event short description block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/event-preview-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-short-description-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-gray-icon-block',
            'title' 		=> __('Event gray icon block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'editor-table',
        'description' => __('Event gray icon block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event gray icon block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/event-gray-icon-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-gray-icon-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-partners-block',
            'title' 		=> __('Event partners block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'buddicons-buddypress-logo',
        'description' => __('Event partners block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event partners block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/event-partners-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-partners-block.png',
                )
            )
        )
    ),
    array(
        'attrs' => array(
            'name'  		=> 'event-about-sponsors-block',
            'title' 		=> __('Event about sponsors block', 'ltm'),
            'path'  		=> 'event',
        ),
        'icon'  		=> 'vault',
        'description' => __('Event about sponsors block', 'ltm'),
        'post_types' 	=> array( 'events' ),
        'category'  	=> 'ltm-event-blocks',
        'keywords'    => array( __('Event about sponsors block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/event-about-sponsors-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'event-about-sponsors-block.png',
                )
            )
        )
    ),
    // END Events

    //START PAGE Full Width
    array(
        'attrs' => array(
            'name'  		=> 'page-hero-block',
            'title' 		=> __('Page hero block', 'ltm'),
            'path'  		=> 'page',
        ),
        'icon'  		=> 'laptop',
        'description' => __('Page hero block', 'ltm'),
        'post_types' 	=> array( 'page' ),
        'category'  	=> 'ltm-page-blocks',
        'keywords'    => array( __('Page hero block', 'ltm') ),
        'enqueue_style'=> get_template_directory_uri() . '/dist/css/blocks/page-hero-block.min.css',
        'example'  	=> array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    'image' => 'page-hero-block.png',
                )
            )
        )
    ),
    //END PAGE Full Width
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

    acf_add_options_sub_page(array(
        'page_title' => 'Resources Settings',
        'menu_title' => 'Resources Settings',
        'parent_slug' => 'edit.php?post_type=resources',
    ));

    acf_add_options_sub_page(array(
        'page_title'  => __('DFP Ad Slots'),
        'menu_title'  => __('DFP Ad Slots'),
        'parent_slug' => 'edit.php?post_type=in-house-ads',
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

function ltm_allowed_post_type_blocks( $allowed_block_types, $editor_context ) {
    if ( $editor_context->name !== 'core/edit-widgets' ) {
        $disallowed_blocks = array(
            'acf/sidebar-editors-picks-section',
            'acf/sidebar-form-section',
            'acf/sidebar-news-list-section',
            'acf/related-reading-section',
            'acf/sidebar-ad-banner-section',
            'acf/sidebar-info-block',
        );

        // Get all registered blocks if $allowed_block_types is not already set.
        if ( ! is_array( $allowed_block_types ) || empty( $allowed_block_types ) ) {
            $registered_blocks   = WP_Block_Type_Registry::get_instance()->get_all_registered();
            $allowed_block_types = array_keys( $registered_blocks );
        }

        // Create a new array for the allowed blocks.
        $filtered_blocks = array();

        // Loop through each block in the allowed blocks list.
        foreach ( $allowed_block_types as $block ) {
            // Check if the block is not in the disallowed blocks list.
            if ( ! in_array( $block, $disallowed_blocks, true ) ) {
                // If it's not disallowed, add it to the filtered list.
                $filtered_blocks[] = $block;
            }
        }
        // Return the filtered list of allowed blocks
        return $filtered_blocks;
    }

    return $allowed_block_types;
}

add_filter( 'allowed_block_types_all', 'ltm_allowed_post_type_blocks', 10, 2 );

add_filter( 'acf/load_field/name=ad_size_dynamic', 'acf_load_ad_size_choises' );
function acf_load_ad_size_choises( $field ) {
    $dfpAdsSizes = get_field('dfp_ad_sizes', 'option');
    if( !empty($dfpAdsSizes) ) {
        $choices = [];
        foreach ($dfpAdsSizes as $size) {
            $choices[$size['width'] . 'x' . $size['height'] ] = $size['width'] . 'x' . $size['height'];
        }

        $field['choices'] = $choices;
    }
    return $field;
}

add_filter( 'acf/load_field/name=dynamic_ad_banner', 'acf_load_ad_canner_choises' );
function acf_load_ad_canner_choises( $field ) {
    $dfpAdsSlots = get_field('dfp_ad_slots', 'option');
    if( empty($dfpAdsSlots['slot']) ) {
        return $field;
    }

    $choices = [];
    foreach ($dfpAdsSlots['slot'] as $slot) {
        $choices[$slot['spot_id']] = $slot['name'];
    }

    $field['choices'] = $choices;
    return $field;
}