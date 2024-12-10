<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Subscribe form block DEFAULT', 'ltm') . '</h3>';
}
// Set defaults Image and text.

$options = wp_parse_args(
    $args,
    [
        'title'         => 'Get Latitude Media in your inbox',
        'form_code'     => false,
        'description'  => 'Subscribe to Latitude\'s free newsletters today to receive the latest news on the energy transition:',
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

$blockAttrs = wp_kses_data(
    get_block_wrapper_attributes(
        [
            "class" => 'content-block subscribe-form-section',
            "id" => $blockAttributes['anchor'] ?: '',
        ]
    )
);

?>

<div
    <?php echo $blockAttrs; ?>
>
    <div class="container">
        <div class="subscribe-form-section-wrapper">
            <?php do_action('section_title', $title, '<h2>%1$s</h2>'); ?>
            <div class="flex-wrapper">
                <div class="text-side">
                    <p>
                        <?php _e($description); ?>
                    </p>
                </div>
                <div class="form-side">
                    <div class="form-block pink">
                        <div class="form-block-wrapper">
                            <?php echo $form_code; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>