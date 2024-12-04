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

$customClasses = [];
if( !empty($blockAttributes['className']) ) {
    $customClasses = $blockAttributes['className'];
}

?>

<?php do_action('button_unit', $button, null, 'cta-button ' . implode(' ', $customClasses)); ?>
