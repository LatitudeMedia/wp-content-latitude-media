<?php

/**
 * ACF blocks Class
 */
class ACFBlocks {

    public $blocks = array();

    public function __construct( $blocks )
    {
        $this->blocks = $blocks;

        if ( function_exists( 'acf_register_block_type' ) ) {
            add_action( 'acf/init', array( $this, 'registerBlocks' ) );
            add_action( 'block_categories', array( $this, 'custom_block_categories' ), 10, 2 );
        }
    }

    public function registerBlocks() {
        foreach ( $this->blocks as $block ) {
            $attrs = $block['attrs'];
            unset( $block['attrs'] );

            $options = wp_parse_args(
                $block,
                array(
                    'name'            => $attrs['name'],
                    'title'           => __( $attrs['title'], 'ltm' ),
                    'description'     => __( $attrs['title'], 'ltm' ),
                    'render_callback' => array( $this, 'render_blocks_callback' ),
                    'path'            => $attrs['path'],
                    'category'        => 'ltm-common-blocks',
                    'icon'            => 'admin-comments',
                    'mode'            => 'edit',
                    'display' => $attrs['display'] ?? NULL,
                    'post_acf' => $attrs['post_acf'] ?? NULL,
                    'keywords'        => array_merge( explode( '-', $attrs['name'] ), array( $attrs['name'] ) ),
                    "supports" =>  array(
                        "anchor" =>  true,
                        "spacing" => array(
                            "margin" => true,
                            "padding" => true,
                        ),
                        "color" => true,
                        "baseColor" => true
                    ),
                    'example'  	=> array(
                        'attributes' => array(
                            'mode' => 'preview',
                            'data' => array(
                                'image' => $attrs['name'] . '.png',
                            )
                        )
                    )
                )
            );

            acf_register_block_type( $options );
        }
    }

    public function render_blocks_callback( $attributes, $content = '', $is_preview = false, $post_id = 0 ) {
        $nameParts = explode('/', $attributes['name']);
        $blockName = array_pop($nameParts);
        $acfName = preg_replace('/-/', '_', $blockName);
        $displayBlock = true;

        if ( $attributes['post_acf'] && ! $sectionData = get_field( $acfName, $post_id ) ) {
            $displayBlock = false;

        }

        $sectionData['display'] = get_field('display');
        if ( (!isset($sectionData['display']) || !$sectionData['display']) && !is_admin() && (!isset($attributes['display']) || !$attributes['display']) ) {
            $displayBlock = false;
        }

        if($displayBlock) {
            if( !isset($attributes['anchor']) ) {
                $attributes['anchor'] = '';
            }

            get_template_part(
                'template-parts/blocks/' . implode('/', array($attributes['path'], $blockName)),
                '',
                array(
                    'post_id' => $post_id,
                    'blockAttributes'   => $attributes,
                )
            );
        } elseif( $is_preview && isset($attributes['data']['image']) ) {
            echo '<img width="100%" src="' . get_template_directory_uri() . '/src/images/blocks-preview/' . $attributes['data']['image'] . '">';
        } else {
            return;
        }
    }

    public function custom_block_categories( $categories ) {
        return array_merge(
            [
                [
                    'slug'  => 'ltm-common-blocks',
                    'title' => __( 'Latitude Media Common Blocks', 'ltm' ),
                ],
                [
                    'slug'  => 'ltm-page-blocks',
                    'title' => __( 'Latitude Media Page Blocks', 'ltm' ),
                ],
                [
                    'slug'  => 'ltm-section-landing-blocks',
                    'title' => __( 'Latitude Media Section Landing Blocks', 'ltm' ),
                ]
            ],
            $categories
        );
    }
}
