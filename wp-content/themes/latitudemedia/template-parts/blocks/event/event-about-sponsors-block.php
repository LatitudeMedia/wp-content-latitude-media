<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event about sponsors block', 'ltm') . '</h3>';
}
// Set defaults Event about sponsors block.
$options = wp_parse_args(
    $args,
    [
        'title' => 'About our sponsors',
        'sponsors' => [],
        'display' => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($sponsors) ) {
    return;
}

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block about-sponsors-section green',
          "id" => 'event-about-sponsors-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="bordered-title"><?php echo $title; ?></div>
        <div class="about-sponsors-section-wrapper">
            <ul>
                <?php foreach ($sponsors as $sponsor) : ?>
                <li>
                    <div class="logo-wrapper">
                        <?php
                        if ( !empty($sponsor['logo']) ) {
                            $imageHtml = thumbnail_formatting(null, ['image_id' => $sponsor['logo'], 'size' => 'image-text-type2', 'link' => false], false);
                            printf('<a href="#">%s</a>', $imageHtml);
                        }
                        ?>
                    </div>
                    <div class="content-wrapper">
                        <?php echo $sponsor['description']; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>