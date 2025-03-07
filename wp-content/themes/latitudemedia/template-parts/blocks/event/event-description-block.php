<?php
// Set defaults Event description block.
$options = wp_parse_args(
    $args,
    [
        'blockAttributes' => [],
    ]
);

$blockType = ltm_get_block_style($options['blockAttributes']['className'] ?? []);
get_template_part(
    'template-parts/components/event-description/' . $blockType ?: 'default', '', $options
);