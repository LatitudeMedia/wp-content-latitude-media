<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Image and text block', 'ltm') . '</h3>';
}
// Set defaults Image and text block.
$blockFields = (get_fields() ?? []) ?: [];
$options = wp_parse_args(
    array_merge($args, $blockFields),
    [
        'title'         => '',
        'image'         => null,
        'content'       => '',
        'base_color'        => '#C6168D',
        'shadow_color'      => '#F9E8F4',
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "style" => '--custom-block-base-color: ' . $base_color . '; --custom-block-shadow-color: ' . $shadow_color,
                "class" => 'content-block image-text-section',
                "id" => 'image-and-text-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <div class="container">
        <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
        <div class="image-text-section-wrapper">
            <div class="image-folder">
                <a href="#">
                    <?php
                        if( !empty($image) ) {
                            do_action('thumbnail_formatting', ['link' => false], ['image_id' => $image['ID']]);
                        }
                    ?>
                </a>
            </div>
            <div class="content-folder">
                <?php
                    echo $content;
                ?>
            </div>
        </div>
    </div>
</div>