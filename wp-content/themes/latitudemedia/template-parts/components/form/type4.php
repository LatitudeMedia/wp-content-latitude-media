<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Subscribe form block TYPE 4', 'ltm') . '</h3>';
}
// Set defaults Image and text.

$options = wp_parse_args(
    $args,
    [
        'title'         => 'Learn how to engage with our podcast listeners',
        'form_code'     => false,
        'description'  => 'Learn more about podcast advertising opportunities with Latitude Media.',
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

$blockAttrs = wp_kses_data(
    get_block_wrapper_attributes(
        [
            "class" => 'content-block wide-form-section',
            "id" => $blockAttributes['anchor'] ?: '',
        ]
    )
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="wide-form-section-wrapper">
            <?php
                do_action('section_title', $title, '<h2>%1$s</h2>');

                if( !empty( $description ) ) {
                    printf( '<p>%1$s</p>', $description );
                }

                echo $form_code;
            ?>
        </div>
    </div>
</div>