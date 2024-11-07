<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Sidebar form block', 'ltm') . '</h3>';
}
// Set defaults Sidebar form block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'title'             => false,
        'form_code'         => false,
        'display'           => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($form_code) ) {
    return;
}
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'sidebar-block form-block',
                "id" => 'sidebar-form-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <div class="form-block-wrapper">
        <?php do_action('section_title', $title, '<div class="form-title">%1$s</div>'); ?>
        <?php
            echo $form_code;
        ?>
    </div>
</div>