<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Subscribe form block', 'ltm') . '</h3>';
}
// Set defaults Subscribe form block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'title'         => 'Get Latitude Media in your inbox',
        'form_code'     => false,
        'left_content'  => 'Subscribe to Latitude\'s free newsletters today to receive the latest news on the energy transition:',
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if(empty($form_code)) {
    return;
}
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block subscribe-form-section',
                "id" => 'subscribe-form-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <div class="container">
        <div class="subscribe-form-section-wrapper">
            <?php do_action('section_title', $title, '<h2>%1$s</h2>'); ?>
            <div class="flex-wrapper">
                <div class="text-side">
                    <p>
                        <?php _e($left_content); ?>
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
