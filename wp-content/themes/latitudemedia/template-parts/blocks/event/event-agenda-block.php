<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event agenda block', 'ltm') . '</h3>';
}
// Set defaults Event agenda block.
$options = wp_parse_args(
    $args,
    [
        'title'     => false,
        'schedule'  => [],
        'display'   => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty( $schedule ) ) {
    return;
}

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block agenda-section green',
          "id" => 'event-agenda-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?> >
    <div class="container-narrow">
        <div class="bordered-title green"><?php _e($title)?></div>
        <div class="agenda-section-wrapper">
            <ul>
                <?php
                    foreach ($schedule as $item) {
                        $dataHtml = '';
                        $imageHtml = '';
                        $speakersListHtml = '';
                        if( !empty($item['image']) ) {
                            $imageHtml = thumbnail_formatting(null, ['image_id' => $item['image'], 'link' => false, 'img_attr' => ['class' => 'agenda-thumbnail'] ], false);
                        }

                        if( !empty($item['time']) ) {
                            $dataHtml = sprintf('<div class="time">%s</div>', $item['time']);
                        }
                        if( !empty($item['title']) ) {
                            $dataHtml .= sprintf('<h5>%s</h5>', $item['title']);
                        }
                        if( !empty($item['description']) ) {
                            $dataHtml .= sprintf('<p>%s</p>', $item['description']);
                        }
                        if( !empty($item['speakers']) ) {
                            $speakersPosts = get_published_posts_by_ids($item['speakers'], ['post_type' => 'speakers']);
                            $speakersListHtml = '';
                            while($speakersPosts->have_posts()) {
                                $speakersPosts->the_post();
                                $speakersListHtml .= sprintf('<li><span class="name">%s</span></li>', get_the_title());
                            }
                        }

                        if( $speakersListHtml ) {
                            $dataHtml .= sprintf('<ul class="speakers">%s</ul>', $speakersListHtml);
                        }

                        printf('<li>%s<div class="agenda-data">%s</div></li>', $imageHtml, $dataHtml);
                    }
                ?>
            </ul>
        </div>
    </div>
</div>