<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Sample campaign block', 'ltm') . '</h3>';
}
// Set defaults Sample campaign block.
$options = wp_parse_args(
    $args,
    [
        'title'     => false,
        'items'     => false,
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
          "class" => 'content-block topics-archive-section campaigns-section',
          "id" => $blockAttributes['anchor'] ?: '',
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="bordered-title orange">
            <?php _e($title); ?>
        </div>
        <div class="topics-archive-section-wrapper">
            <div class="posts-list-section">
                <ul class="posts">
                    <?php foreach ($items as $item) : ?>
                        <li>
                            <?php if (!empty($item['image'])) : ?>
                            <div class="image-folder orange">
                                <div>
                                    <?php do_action('thumbnail_formatting', null, ['image_id' => $item['image'], 'size' => 'articles-list', 'link' => false]); ?>
                                </div>
                            </div>
                            <?php endif ?>
                            <div class="content-folder">
                                <?php
                                    echo $item['content'];
                                    do_action('button_unit', $item['button'], null, 'cta-button orange');
                                ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>