<?php
// Set defaults Subscribe form block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'title'         => 'Get Latitude Media in your inbox',
        'form_code'     => false,
        'description'  => 'Subscribe to Latitude\'s free newsletters today to receive the latest news on the energy transition:',
        'display'       => false,
        'blockAttributes' => [],
    ]
);

$blockType = ltm_get_block_style($options['blockAttributes']['className'] ?? []);
if(!$options['display'] && !is_admin()) {
    return;
}

if(empty($options['form_code'])) {
    return;
}

get_template_part(
    'template-parts/components/form/' . $blockType ?: 'default', '', $options
);
