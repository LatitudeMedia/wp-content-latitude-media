<?php
function get_f_name($name) {
    $name_arr = explode(' ', $name);
    if(count($name_arr) > 1) {
        return $name_arr[0];
    }

    return $name;
}

function get_l_name($name, $l_name = '') {
    $name_arr = explode(' ', $name);
    if(count($name_arr) > 1) {
        return $name_arr[1];
    }

    return $l_name;
}
function is_published($publish_date = null) {
    if( !empty($publish_date) ) {
        return 'publish';
    }

    return 'draft';
}

function no_index($noindex = false) {
    if( $noindex ) {
        return 'noindex';
    }

    return '';
}


function no_follow($noindex = false) {
    if( $noindex ) {
        return 'nofollow';
    }

    return '';
}


function get_news_type($type) {
    return strtolower($type);
}


function get_event_type($type) {
    return implode('-', explode(' ', strtolower($type)));
}

function build_article_content($body1,$body2,$body3,$body4,$quote_content,$quote_name,$inhouse_ad,$exclude_sign_up,$inline_podcast,$podcast_embed,$news_type) {
    $content = '';
    if($news_type === 'Podcast' && !$inline_podcast && !empty($podcast_embed)) {
        $content .= '<!-- wp:acf/inline-podcast-section {"name":"acf/inline-podcast-section","data":{"field_6709140c48444":"1"},"mode":"edit"} /-->';
    }

    $content .= $body1;

    if($inline_podcast && !empty($podcast_embed)){
        $content .= '<!-- wp:acf/inline-podcast-section {"name":"acf/inline-podcast-section","data":{"field_6709140c48444":"1"},"mode":"edit"} /-->';
    }
    $content .= $body2;

    if( !empty($quote_content) ) {
        $content .= '<!-- wp:acf/spotlight-quote-section {"name":"acf/spotlight-quote-section","data":{"copy":"' . $quote_content . '","_copy":"field_6706990eeed8e","name":"' . $quote_name . '","_name":"field_67069930eed8f","display":"1","_display":"field_67069833ec942"},"mode":"edit"} /-->';
    }

    $content .= $body3;

    if(!empty($inhouse_ad)) {
        $args = array(
            'name'           => $inhouse_ad,
            'post_type'      => 'in-house-ads',
            'post_status'    => 'any',
            'posts_per_page' => 1
        );
        $ad_posts = get_posts($args);
        $inhouse_ad_id = $ad_posts ? $ad_posts[0]->ID : 0;
        if($inhouse_ad_id) {
            $content .= '<!-- wp:acf/in-house-ad-section {"name":"acf/in-house-ad-section","data":{"in_house_ad":' . $inhouse_ad_id . ',"_in_house_ad":"field_6707c6e5ddb83","display":"1","_display":"field_6707c695b5480"},"mode":"edit"} /-->';
        }
    }

    if(!$exclude_sign_up) {
        $content .= '<!-- wp:acf/signup-form-section {"name":"acf/signup-form-section","data":{"display":"1","_display":"field_6707ca1167942"},"mode":"edit"} /-->';
    }
    $content .= $body4;

    return $content;
}

function import_date_to_format($dateString, $format = 'Y-m-d H:i:s') {
    $cleanDateString = preg_replace('/\(.*\)/', '', $dateString);
    $cleanDateString = trim($cleanDateString);
    $date = DateTime::createFromFormat('D M d Y H:i:s \G\M\TP', $cleanDateString);

    if ($date) {
        return $date->format($format);
    } else {
        return 0;
    }
}
?>