<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Partner podcasts block', 'ltm') . '</h3>';
}
// Set defaults Partner podcasts block.
$options = wp_parse_args(
    $args,
    [
        'title'         => false,
        'description'   => false,
        'podcasts'      => [],
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if ( empty($podcasts) ) {
    return;
}

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block',
          "id" => $blockAttributes['anchor'] ?: '',
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="three-podcasts-section">
        <div class="container-narrow">
            <div class="three-podcasts-section-wrapper">
                <div class="bordered-title orange"><?php _e($title); ?></div>
                <h3 class="heading"><?php _e($description); ?></h3>
                <ul>
                    <?php foreach ($podcasts as $podcast) : ?>
                        <li>
                            <div class="image-folder orange">
                                <?php
                                if( !empty($podcast['image']) ) {
                                    do_action('thumbnail_formatting', null, ['link' => false, 'size' => 'author-archive-hero', 'image_id' => $podcast['image']['ID']]);
                                }
                                ?>
                            </div>
                            <div class="content-folder">
                                <?php
                                    if(!empty($podcast['title'])) {
                                        printf('<div class="title">%s</div>', $podcast['title']);
                                    }
                                    if(!empty($podcast['company'])) {
                                        printf('<div class="company">%s</div>', $podcast['company']);
                                    }
                                    if(!empty($podcast['description'])) {
                                        printf('<p>%s</p>', $podcast['description']);
                                    }
                                ?>
                                <div class="listen-block">
                                    <?php
                                    get_template_part('template-parts/podcast/listening', 'links', [
                                        'title' => 'listen on:',
                                        'links' => [
                                            'apple'     => $podcast['apple_link'],
                                            'spotify'   => $podcast['spotify_link'],
                                        ]
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

