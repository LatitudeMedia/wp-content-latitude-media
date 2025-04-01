<?php


/**
 * Creates the DFP targeting key/value pair for a single targeting variable.
 *
 * @param string $key
 * @param string $source
 * @param array $values
 * @return array
 */
function get_targeting_value( $key, $source, $values = null ) {
    $targeting_value = null;
    $queried_object = get_queried_object();

    switch ( $source ) {
        case 'other':
            if ( null !== $values ) {
                $targeting_value = $values;
            }
            break;
        case 'author':
            if ( is_singular() ) {
                $targeting_value = get_the_author_meta( apply_filters( 'ad_layers_dfp_author_targeting_field', 'display_name' ), $queried_object->post_author );
            } else if ( is_author() ) {
                $targeting_value = $queried_object->display_name;
            }
            break;
        case 'post_type':
            if ( is_singular() ) {
                $targeting_value = get_post_type();
            } else if ( is_post_type_archive() ) {
                $targeting_value = $queried_object->name;
            }
            break;
        default:
            if ( taxonomy_exists( $source ) ) {
                if ( is_singular() ) {
                    $terms = get_the_terms( get_the_ID(), $source );
                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                        $value = wp_list_pluck( $terms, apply_filters( 'ad_layers_dfp_term_targeting_field', 'slug' ) );
                    } else {
                        $value = array();
                    }
                    $targeting_value = $value;
                } else if ( is_tax() || is_category() || is_tag() ) {
                    $targeting_value = $queried_object->slug;
                }
            }
            break;
    }

    return apply_filters( 'ad_dfp_custom_targeting_value', $targeting_value, $key, $source, $values );
}

/**
 *  Creates a key => value array of targeting variables from custom
 *  values.
 *
 * @param $custom_targeting
 * @return array
 */
function get_targeting_array_from_custom_values( $custom_targeting )
{
    $targeting_values = array();
    foreach ( (array) $custom_targeting as $custom_target ) {
        $values = ( isset( $custom_target['values'] ) ) ? $custom_target['values'] : null;
        $targeting_value = get_targeting_value( $custom_target['custom_variable'], $custom_target['source'], $values );
        if ( ! empty( $targeting_value ) ) {
            $targeting_values[ $custom_target['custom_variable'] ] = $targeting_value;
        }
    }

    return $targeting_values;
}

/**
 * Gets the DFP targeting JS for a single key/value pair.
 *
 * @param string $key
 * @param mixed $value
 * @return string
 */
function get_targeting_value_js( $key, $value ) {
    return sprintf(
        '.setTargeting(%s,%s)',
        wp_json_encode( $key ),
        wp_json_encode( $value )
    );
}

/**
 * Creates the DFP targeting Javascript from an array of custom values.
 *
 * @param array $custom_targeting
 * @return string
 */
function get_targeting_js_from_array( $custom_targeting ) {
    $targeting_values = '';
    $targeting_array = get_targeting_array_from_custom_values( $custom_targeting );

    foreach ( $targeting_array as $key => $values ) {
        $targeting_values .= get_targeting_value_js( $key, $values );
    }

    return $targeting_values;
}


/**
 * @param $bannerField
 * @param $post_id
 * @param $options
 * @return array|mixed
 */
function get_article_banner($bannerField = '', $post_id = null, $options = false) {
    if ( ! $bannerField ) {
        return [];
    }

    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $articleBanner = get_field($bannerField, $post_id);
    if ( empty($articleBanner['dynamic_ad_banner']) && $options) {
        $banner = get_field($bannerField, 'options');
        $articleBanner['dynamic_ad_banner'] = $banner['dynamic_ad_banner'];
        $articleBanner['screen_type'] = $banner['screen_type'] ?? null;
    }

    return $articleBanner;
}


/**
 * @param $bannerField
 * @param $category_id
 * @param $options
 * @return array|mixed
 */
function get_category_banner($bannerField = '', $category_id = null, $options = false) {
    if ( ! $bannerField || !$category_id ) {
        return [];
    }

    $topicBanner = get_field($bannerField, 'category_' . $category_id, true);

    if ( empty($topicBanner['dynamic_ad_banner']) && $options) {
        $banner = get_field($bannerField, 'options');
        $topicBanner['dynamic_ad_banner'] = $banner['dynamic_ad_banner'];
        $topicBanner['screen_type'] = $banner['screen_type'];
    }

    return $topicBanner;
}

function get_site_header_banner()
{
    if( is_singular('post'))
    {
        return array_merge(get_article_banner('article_top_banner', get_the_ID(), true));
    }
    if( is_category() )
    {
        $term = get_queried_object();
        return get_category_banner('topic_top_banner', $term->term_id, true);
    }

    return [];
}

function get_article_in_content_banner($type = '1')
{
    return array_merge(get_article_banner("article_in_content_banner_{$type}", get_the_ID(), true));
}

function get_ad_targeting()
{
    // Include our four taxonomies for targeting.
    if ( is_singular() ) {
        $taxonomies = array(
            'tags'       => 'post_tag',
            'categories' => 'category',
        );

        foreach ( $taxonomies as $key => $taxonomy ) {
            $terms = get_the_terms( get_the_ID(), $taxonomy );

            if ( ! empty( $terms ) && is_array( $terms ) ) {
                $$key = wp_list_pluck( $terms, 'slug' );
            }

            if ( ! empty( $$key ) ) {
                $custom_targeting[] = array(
                    'custom_variable' => $key,
                    'source'          => 'other',
                    'values'          => array_filter( $$key ),
                );
            }
        }
    } // end is_singular


    // Append Post ID to targeting.
    if ( is_singular() ) {
        $custom_targeting[] = array(
            'custom_variable' => 'aid',
            'source'          => 'other',
            'values'          => array(
                get_the_ID(),
            ),
        );

        // Add author
        $custom_targeting[] = array(
            'custom_variable' => 'author',
            'source'          => 'other',
            'values'          => array(
                get_the_author(),
            ),
        );
    }// end is_singular.

    // ALL pages:
    // Enviorment targeting.
    $custom_targeting[] = array(
        'custom_variable' => 'environment',
        'source'          => 'other',
        'values'          => array(
            WP_ENVIRONMENT_TYPE ?? 'production',
        ),
    );

    return $custom_targeting;
}

if ( ! function_exists( 'print_ad_banner' ) ) :
    function print_ad_banner( $banner, $args = array() ): void
    {
        if( empty($banner['dynamic_ad_banner']) ) {
            return;
        }

        if( isset($banner['display']) && !$banner['display'] )
        {
            return;
        }

        $defaults = [
            'wrap' => '<div class="banner-ad-block ' . ($args['class'] ?? '') . '"><div class="container"><div class="banner-ad-block-wrapper">%s</div></div></div>',
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args);

        get_template_part(
            'template-parts/components/ad',
            'banner',
            array(
                'banner_id'  => $banner['dynamic_ad_banner'],
                'screen_type'=> $banner['screen_type'] ?? null,
                'wrap'       => $wrap,
            )
        );
    }
    add_action('print_ad_banner', 'print_ad_banner', 10, 2);
endif;

if ( ! function_exists( 'display_content_apply_incontent_ads' ) ) :
    function display_content_apply_incontent_ads( $args = array())
    {
        $content = get_the_content();
        $content = apply_filters( 'the_content', $content );
        $content = str_replace( ']]>', ']]&gt;', $content );
        $parts = explode("</p>", $content);

        $lastParagraph = 0;
        $wordCount = 0;
        $bannerSettings = get_field('article_in_content_banner_1', 'options');
        $afterWordsNumber = $bannerSettings['after_words_number'] ?? 2000;
        foreach ($parts as &$part) {
            $textContent = strip_tags($part);
            $wordCount += str_word_count($textContent);
            $lastParagraph++;

            if ($wordCount >= $afterWordsNumber || $lastParagraph >= count($parts)) {
                ob_start();
                do_action('print_ad_banner', get_article_in_content_banner(), ['wrap' => '<div class="banner-ad-block in-content-banner"><div class="banner-ad-block-wrapper">%s</div></div>']);
                $banner = ob_get_clean();
                $part .= "<div>$banner</div>";
                break;
            }
        }
        echo force_balance_tags(implode("</p>", $parts));
    }
    add_action('display_content_apply_incontent_ads', 'display_content_apply_incontent_ads', 10, 1);
endif;