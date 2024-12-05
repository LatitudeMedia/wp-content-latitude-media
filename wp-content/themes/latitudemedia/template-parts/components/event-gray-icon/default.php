
<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event gray icon block DEFAULT', 'ltm') . '</h3>';
}
// Set defaults Event gray icon block.
$options = wp_parse_args(
    $args,
    [
        'logo' => null,
        'content' => '',
        'display' => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

$blockAttrs = wp_kses_data(
    get_block_wrapper_attributes(
        [
            "class" => 'content-block grey-icon-block single-grey-icon-block',
            "id" => 'event-gray-icon-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
        ]
    )
);



?>
<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="grey-icon-block-wrapper">
            <?php
                if( !empty($logo) ) {
                    do_action('thumbnail_formatting', null, ['image_id' => $logo, 'link' => false, 'img_attr' => ['class' => 'icon'] ]);
                }

                echo $content;
            ?>
        </div>
    </div>
</div>