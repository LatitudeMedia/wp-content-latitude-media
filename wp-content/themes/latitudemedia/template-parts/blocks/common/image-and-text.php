<?php
// Set defaults Image and text.
$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'blockAttributes' => [],
    ]
);

$blockType = ltm_get_block_style($options['blockAttributes']['className'] ?? []);

get_template_part(
    'template-parts/components/image-and-text/' . $blockType ?: 'default', '', $options
);

?>
