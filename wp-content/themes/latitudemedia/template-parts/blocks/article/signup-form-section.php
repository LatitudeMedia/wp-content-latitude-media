<?php
if (is_admin()) {
    echo '<div style="border: 1px solid #c7c7c7">';
    echo '<h3 style="text-align: center;">' . __('Signup form block', 'ltm') . '</h3>';
    echo '<small style="text-align: center">Newsletter signup form, display only on mobile view.
<br>Display condition based on settings in sidebar <b>News options -> Exclude Related Reading & Signup</b></small>';
    echo '<div style="text-align: center"><img src="'. get_stylesheet_directory_uri() . '/src/images/blocks-preview/' . $args['blockAttributes']['example']['attributes']['data']['image'] .'"></div>';
    echo '</div>';
    return;
}
// Set defaults Signup form block.
$options = wp_parse_args(
    array_merge(
        $args,
        get_field('post_in_content_form', 'options') ?? []
    ),
    [
        'post_id' => null,
        'blockAttributes' => [],
    ]
);

extract($options);

$display = !get_field('exclude_related_reading_signup', $post_id);
if(!$display && !is_admin()) {
    return;
}

if( !wp_is_mobile() && !is_admin() ) {
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
                "class" => 'content-block form-block',
                "id" => 'signup-form-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
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

