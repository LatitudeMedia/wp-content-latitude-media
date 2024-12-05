<?php
// Set defaults Styled button block.
$options = wp_parse_args(
    $args,
    [
        'button' => [],
        'blockAttributes' => [],
    ]
);

extract($options);

if( empty($button) ) {
    $button = [
      'title' => 'Select link',
      'url' => '/',
    ];
}

?>

<?php do_action('button_unit', $button, null, 'cta-button ' . ($blockAttributes['className'] ?? '')); ?>
