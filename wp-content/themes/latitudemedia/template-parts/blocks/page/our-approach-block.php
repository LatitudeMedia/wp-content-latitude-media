<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Our approach block', 'ltm') . '</h3>';
}
// Set defaults Our approach block.
$options = wp_parse_args(
    $args,
    [
        'title'     => 'OUR APPROACH',
        'copy'      => '',
        'type'      => 'text',
        'options'   => [],
        'columns'   => 3,
        'display'   => false,
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
          "class" => 'content-block approach-section orange approach-section-type-' . $type,
          "id" => $blockAttributes['anchor'] ?: '',
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="bordered-title"><?php _e($title); ?></div>
        <div class="approach-section-wrapper">
            <div class="caption">
                <?php _e($copy); ?>
            </div>
            <?php if($options) : ?>
                <ul class="numbered-features cols-<?php echo $columns ?? 3; ?>">
                    <?php
                        foreach ($options as $key => $option) {
                            if( $type === 'images' ) {
                                $imageHtml = thumbnail_formatting(null, ['image_id' => $option['image']['ID'], 'size' => 'image-text-type7', 'link' => false], false);
                                printf('<li>%s</li>', $imageHtml);
                            }
                            else {
                                printf('<li><div class="number"><span>%s</span></div><div class="description"><div class="title">%s</div><p>%s</p></div></li>', ($key+1), $option['title'], $option['description']);
                            }
                        }
                    ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>