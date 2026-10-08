<?php
/**
 * Event about sponsors block (acf/event-about-sponsors-block).
 *
 * Usage note (audited 2026-09-30): this block is in use on exactly one post --
 * the "Transition-AI: New York" event (post ID 2142, /events/transition-ai-new-york/).
 * It appears in no other post, reusable block, template, or revision. Because of
 * that single use it was deliberately left in the theme rather than migrated to
 * ltm-core. Re-check usage before changing or removing it.
 * 
 * TODO: we will most likely remove this block once that post is archived
 */
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
          "id" => $blockAttributes['anchor'] ?: '',
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
                            $imageHtml = thumbnail_formatting(null, ['image_id' => $sponsor['logo'], 'size' => 'image-text-default', 'link' => false], false);
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