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