<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event partners block', 'ltm') . '</h3>';
}
// Set defaults Event partners block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'display'   => false,
        'title'     => '',
        'logos'     => [],
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($logos) ) {
    return;
}

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block partners-logos-section',
          "id" => 'event-partners-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="partners-logos-section-wrapper">
            <h2><?php echo $title; ?></h2>
            <ul class="logos">
                <?php
                    foreach ($logos as $logo) {
                        if( empty($logo['partner_logo']) ) {
                            continue;
                        }
                        $imageHtml = thumbnail_formatting(null, ['image_id' => $logo['partner_logo'], 'size' => 'event-sponsors-list', 'link' => false, 'img_attr' => ['class' => 'logo'] ], false);
                        printf('<li><a href="#">%s</a></li>', $imageHtml);
                    }
                ?>
            </ul>
        </div>
    </div>
</div>