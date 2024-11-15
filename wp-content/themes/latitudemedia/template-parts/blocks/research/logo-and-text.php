<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Logo and text', 'ltm') . '</h3>';
}
// Set defaults Logo and text.

$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'blockAttributes' => [],
    ]
);

$blockType = ltm_get_block_style($options['blockAttributes']['className'] ?? []);

get_template_part(
    'template-parts/components/logo-and-text/' . $blockType ?: 'default'
);

?>

