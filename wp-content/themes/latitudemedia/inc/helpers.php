<?php
/**
 * A variety of helper functions.
 *
 */

function is_sponsored( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $is_sponsored = get_field('sponsored', $post_id);

    return $is_sponsored ?? false;
}

function get_wrap_rows_from_template($template) {
    if(!$template) {
        return [];
    }
    $found = preg_match_all("/\[([^\]]*)\]/", $template, $matches);
    return [
        'rows' => $found ? $matches[1] : [],
        'wrap' => preg_replace("/\[(.*?)\]/", "%s", $template)
    ];
}

function ltm_get_news_type($post_id = null) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $news_type = get_field('news_type', $post_id);
    if( empty($news_type) ) {
        return '';
    }

    return $news_type['label'];
}

/**
 * @param $dateString
 * @param string $inputFormat
 * @param string $format
 * @return int|string
 */
function date_to_format($dateString, $inputFormat = 'Y-m-d H:i:s', $format = 'Y-m-d H:i:s') {
    $date = DateTime::createFromFormat($inputFormat, $dateString);

    if ($date) {
        return $date->format($format);
    } else {
        return 0;
    }
}

function get_event_start_date($event_id, $format = 'F d Y') {
    if ( ! $event_id ) {
        $event_id = get_the_ID();
    }

    $start_date = get_field('start_date', $event_id);
    if( empty($start_date) ) {
        return '';
    }

    return date_to_format($start_date, 'm/d/Y g:i a', $format);
}

function get_event__end_date($event_id, $format = 'F d Y') {
    if ( ! $event_id ) {
        $event_id = get_the_ID();
    }

    $start_date = get_field('end_date', $event_id);
    if( empty($start_date) ) {
        return '';
    }

    return date_to_format($start_date, 'm/d/Y g:i a', $format);
}


function get_research_date($research_id, $format = 'F Y') {
    if ( ! $research_id ) {
        $research_id = get_the_ID();
    }

    $date = get_field('date', $research_id);
    if( empty($date) ) {
        return '';
    }

    return date_to_format($date, 'm/d/Y', $format);
}

function get_section_cats($section_id, $fields = 'ids') {
    if(!$section_id) {
        return [];
    }

    $section_args = array(
        'hide_empty' => false,
        'meta_query' => array(
            array(
                'key'       => 'section',
                'value'     => $section_id,
                'compare'   => '='
            )
        ),
        'taxonomy'  => 'category',
        'fields'  => $fields,
    );

    $cats = get_terms( $section_args );

    return $cats;
}

/**
 * @param $research_id
 * @return array
 */
function get_research_data($research_id) {
    if(!$research_id) {
        return [];
    }

    return [
        'banner'                => get_field('banner', $research_id),
        'hubspot_payment_link'  => get_field('hubspot_payment_link', $research_id),
        'full_price'            => get_field('full_price', $research_id),
        'purchase_form_code'    => get_field('purchase_form_code', $research_id),
        'download_form_code'    => get_field('download_form_code', $research_id),
        'order_page'            => get_field('order_page', $research_id),
    ];
}

/**
 * @param $order_id
 * @return array
 */
function get_order_report_data($orderId) {
    if(!$orderId) {
        return [];
    }

    $researchData = [];
    $researchId = get_field('research_item', $orderId);
    if( !empty($researchId) ) {
        $researchData = get_research_data($researchId);
        $researchData['research_id'] = $researchId;
    }
    return $researchData;
}

/**
 * @param $order_id
 * @return array
 */
function get_resource_data($resourceId) {
    if(!$resourceId) {
        return [];
    }

    return [
        'form_code'             => get_field('form_code', $resourceId),
        'sponsor'               => get_field('sponsor', $resourceId),
        'resource_type'         => get_field('resource_type', $resourceId),
    ];
}

/**
 * @param null $author
 * @return array
 */
function ltm_get_author_data($author = null) {
    if(!$author) {
        return [];
    }

    $author = get_term($author);
    return [
        'logo'      => get_field('logo', 'author_' . $author->term_id, true),
        'job_title' => get_field('job_title', 'author_' . $author->term_id, true),
        'bio'       => get_field('bio', 'author_' . $author->term_id, true)
    ];
}

/**
 * @param null $member
 * @return array
 */
function ltm_get_team_member_data($member = null) {
    if(!$member) {
        return [];
    }

    $member = get_post($member);
    return [
        'job_title'     => get_field('job_title', $member->ID, true),
        'linkedin'      => get_field('linkedin', $member->ID, true),
        'x_twitter'     => get_field('x_twitter', $member->ID, true),
        'other_socials' => get_field('other_socials', $member->ID, true)
    ];
}

/**
 * @param null $author
 * @return array
 */
function ltm_get_author_socials($author = null) {
    if(!$author) {
        return [];
    }

    $author = get_term($author);
    $socials = [];
    if( $social = get_field('linkedin', 'author_' . $author->term_id, true) ) {
        $socials['linkedin'] = $social;
    }
    if( $social = get_field('x_twitter', 'author_' . $author->term_id, true) ) {
        $socials['x_twitter'] = $social;
    }
    if( $social = get_field('other_social', 'author_' . $author->term_id, true) ) {
        $socials['other_social'] = $social;
    }

    return $socials;
}

/**
 * @param string $classes
 * @return mixed|string
 */
function ltm_get_block_style($classes = '') {
    if( empty($classes) ) {
        return 'default';
    }

    $classes = explode(' ', $classes);
    $classStyle = array_filter($classes, function ($class) {
        return str_contains($class, 'is-style-');
    });

    if( count($classStyle) ) {
        return end(explode('-', $classStyle[0]));
    }

    return 'default';
}

/**
 * @param null $post_id
 * @param array $args
 * @return bool
 */
function get_published_post_by_id($post_id = null, $args = []) {
    if( empty($post_id) ) {
        return false;
    }

    $result = get_published_posts_by_ids([$post_id], $args);
    if($result->found_posts > 0) {
        return $result->posts[0];
    }

    return false;
}

/**
 * @param array $post_ids
 * @param array $args
 * @return WP_Query
 */
function get_published_posts_by_ids($post_ids = [], $args = []) {
    if( empty($post_ids) ) {
        return new WP_Query();
    }

    $defaultArgs = [
        'post_type'        => 'post',
        'posts_per_page'   => -1
    ];
    $queryArgs = wp_parse_args($args, $defaultArgs);
    $result = \LatitudeMedia\Manage_Data()->curated_query($queryArgs, $post_ids);

    return $result;
}

/**
 * @param null $podcast_id
 * @param array $args
 * @return WP_Query
 */
function get_podcast_episodes($podcast_id = null, $args = []) {
    if( empty($podcast_id) ) {
        return new WP_Query();
    }

    $defaultArgs = [
        'post_type'        => 'post',
        'meta_query'        => array(
            array(
                'key'       => 'podcast',
                'value'     => $podcast_id,
                'compare'   => '=',
            ),
        ),
    ];
    $queryArgs = wp_parse_args($args, $defaultArgs);

    return \LatitudeMedia\Manage_Data()->curated_query($queryArgs);
}
