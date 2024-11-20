<?php
// Set defaults Large podcasts block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'title'             => 'Podcasts',
        'number_of_items'   => 3,
        'columns'           => [],
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

$blockType = ltm_get_block_style($options['blockAttributes']['className'] ?? []);
if(!$options['display'] && !is_admin()) {
    return;
}

get_template_part(
    'template-parts/components/large-podcasts/' . $blockType ?: 'default', '', $options
);

?>