<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Subscribe form block TYPE 2', 'ltm') . '</h3>';
}
// Set defaults Logo and text.

$options = wp_parse_args(
    $args,
    [
        'title'         => 'Get Latitude Media in your inbox',
        'form_code'     => false,
        'left_content'  => 'Subscribe to Latitude\'s free newsletters today to receive the latest news on the energy transition:',
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

$blockAttrs = wp_kses_data(
    get_block_wrapper_attributes(
        [
            "class" => 'content-block wide-form-section subscribe-form type-3',
            "id" => 'subscribe-form-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
        ]
    )
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container">
        <div class="wide-form-section-wrapper">
            <?php do_action('section_title', $title, '<h2>%1$s</h2>'); ?>
            <?php echo $form_code; ?>
        </div>
    </div>
</div>