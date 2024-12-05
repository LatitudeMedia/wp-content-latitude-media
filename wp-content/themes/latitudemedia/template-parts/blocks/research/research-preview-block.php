<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Research preview block', 'ltm') . '</h3>';
}
// Set defaults Research preview block.
$options = wp_parse_args(
    $args,
    [
        'display' => false,
        'post_id' => null,
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
          "class" => 'content-block image-text-section report-image-text-block blue',
          "id" => 'research-preview-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);

$subtitle = get_field('subtitle', $post_id);
$date = get_research_date($post_id['ID'])
?>
<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="image-text-section-wrapper">
            <div class="image-folder">
                <a href="#">
                    <?php
                    if( has_post_thumbnail($post_id) ) {
                        do_action('thumbnail_formatting', $post_id, ['link' => false, 'size' => 'image-text-type7']);
                    }
                    ?>
                </a>
            </div>
            <div class="content-folder">
                <?php
                    printf('<h1>%s</h1>', get_the_title($post_id) );
                    if( !empty($subtitle) ) {
                        printf('<p class="description">%s</p>', $subtitle);
                    }
                    if( !empty($date) ) {
                        printf('<p class="date">%s</p>', $date);
                    }
                ?>
            </div>
        </div>
    </div>
</div>