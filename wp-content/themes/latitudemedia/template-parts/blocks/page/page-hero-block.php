<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Page hero block', 'ltm') . '</h3>';
}
// Set defaults Page hero block.
$options = wp_parse_args(
    $args,
    [
        'background'=> null,
        'logo'      => null,
        'title'     => false,
        'link'      => [],
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
          "class" => 'content-block advertise-podcasts-featured-section',
          "id" => 'page-hero-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>>
    <?php
        if ( !empty($background) ) {
            do_action('thumbnail_formatting', null, ['image_id' => $background, 'link' => false, 'img_attr' => ['class' => 'bg-img'] ]);
        }
    ?>
    <div class="container">
        <div class="advertise-podcasts-featured-section-wrapper">
            <div class="content-block">
                <a href="#" class="logo">
                    <?php
                        if (!empty($logo)) {
                            do_action('thumbnail_formatting', null, ['image_id' => $logo, 'link' => false]);
                        }
                    ?>
                </a>
                <h1 class="title"><?php echo $title; ?></h1>
                <?php
                    do_action('button_unit', $link, null, 'learn-more pink');
                ?>
            </div>
        </div>
    </div>
</div>