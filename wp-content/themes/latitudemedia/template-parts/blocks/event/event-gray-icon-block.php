<?php
// Set defaults Logo and text.
$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'type'            => 'default',
        'blockAttributes' => [],
    ]
);

$blockType = ltm_get_block_style($options['blockAttributes']['className'] ?? []);

get_template_part(
    'template-parts/components/event-gray-icon/' . $options['type'], $options
);