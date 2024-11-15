<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Authors list block', 'ltm') . '</h3>';
}
// Set defaults Authors list block.

$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'authors_type' => [
                "value" => "latitude-media-staff",
                "label" => "Latitude Media Staff",
        ],
        'title'             => '',
        'custom'            => [],
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if(!$authors_type) {
    return;
}

$authors_args = array(
    'hide_empty' => false,
    'taxonomy'  => 'author',
);
if( $options['authors_type']['value'] !== 'custom' ) {
    $authors_args['meta_query'] = array(
        array(
            'key'       => 'author_type',
            'value'     => $authors_type['value'],
            'compare'   => '='
        )
    );
} else {
    $authors_args['include'] = $custom;
    $options['authors_type']['label']  = $title;
}
$options['authors'] = get_terms( $authors_args );

if( empty($options['authors']) ) {
    return;
}

$blockType = ltm_get_block_style($options['blockAttributes']['className'] ?? []);
get_template_part(
    'template-parts/components/authors-list-block/' . $blockType ?: 'default', '',
    $options
);

?>