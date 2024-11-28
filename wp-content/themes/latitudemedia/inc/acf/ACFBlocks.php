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
            add_action( 'block_categories_all', array( $this, 'custom_block_categories' ), 10, 2 );
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

        $sectionData = get_fields();
        if(isset($attributes['display']) && !$attributes['display']) {
            $displayBlock = true;
        }
        elseif ( (isset($sectionData['display']) && !$sectionData['display']) && ! is_admin() ) {
            $displayBlock = false;
        }

        if( $is_preview && isset($attributes['data']['image']) ) {
            $blockType = ltm_get_block_style($attributes['className'] ?? '');
            if( $blockType !== 'default' ) {
                $attributes['data']['image'] = str_replace(".png", "-{$blockType}.png", $attributes['data']['image'] );
            }
            echo '<img width="100%" src="' . get_template_directory_uri() . '/src/images/blocks-preview/' . $attributes['data']['image'] . '">';
        } elseif($displayBlock) {
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
                    'slug'  => 'ltm-post-blocks',
                    'title' => __( 'Latitude Media Post Blocks', 'ltm' ),
                ],
                [
                    'slug'  => 'ltm-sidebar-blocks',
                    'title' => __( 'Latitude Media Sidebar Blocks', 'ltm' ),
                ],
                [
                    'slug'  => 'ltm-section-landing-blocks',
                    'title' => __( 'Latitude Media Section Landing Blocks', 'ltm' ),
                ],
                [
                    'slug'  => 'ltm-research-blocks',
                    'title' => __( 'Latitude Media Research Blocks', 'ltm' ),
                ],
                [
                    'slug'  => 'ltm-order-report-blocks',
                    'title' => __( 'Latitude Media Order Report Blocks', 'ltm' ),
                ],
                [
                    'slug'  => 'ltm-event-blocks',
                    'title' => __( 'Latitude Media Event Blocks', 'ltm' ),
                ],
                [
                    'slug'  => 'ltm-page-custom-blocks',
                    'title' => __( 'Latitude Page Custom Blocks', 'ltm' ),
                ]
            ],
            $categories
        );
    }
}
