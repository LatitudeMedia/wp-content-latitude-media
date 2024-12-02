<?php
// Set defaults Styled button block.
$options = wp_parse_args(
    get_fields() ?: [],
    [
        'button' => [],
        'blockAttributes' => $block,
    ]
);

extract($options);

if( empty($button) ) {
    $button = [
      'title' => 'Select link',
      'url' => '/',
    ];
}

$customClasses = $blockAttributes['className'] ?? [];
?>

<?php do_action('button_unit', $button, null, 'cta-button ' . $customClasses); ?>
