
<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event gray icon block TYPE 2', 'ltm') . '</h3>';
}
// Set defaults Event gray icon block.
$options = wp_parse_args(
    $args,
    [
        'logo' => null,
        'content' => '',
        'logo_2' => null,
        'content_2' => '',
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
            "class" => 'content-block half-wide-section',
            "id" => $blockAttributes['anchor'] ?: '',
        ]
    )
);
?>
<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="half-wide-section-wrapper">
            <div class="half left">
                <div class="grey-icon-block">
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
            <div class="half right">
                <div class="grey-icon-block">
                    <div class="grey-icon-block-wrapper">
                        <?php
                        if( !empty($logo_2) ) {
                            do_action('thumbnail_formatting', null, ['image_id' => $logo_2, 'link' => false, 'img_attr' => ['class' => 'icon'] ]);
                        }

                        echo $content_2;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
