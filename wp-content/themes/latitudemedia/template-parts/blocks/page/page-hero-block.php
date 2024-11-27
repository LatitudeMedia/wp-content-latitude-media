<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Page hero block', 'ltm') . '</h3>';
}
// Set defaults Page hero block. 
// 'css/blocks/page-hero-block': './src/assets/scss/blocks/page-hero-block.scss',
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
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
          "class" => 'content-block advertise-podcasts-featured-section',
          "id" => 'page-hero-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>>
    <img class="bg-img" src="client/assets/images/advertise_podcasts_featured.jpg" alt="img">
    <div class="container">
        <div class="advertise-podcasts-featured-section-wrapper">
            <div class="content-block">
                <a href="#" class="logo">
                    <img src="client/assets/images/advertise_logo.svg" alt="Advertise podcasts">
                </a>
                <h1 class="title">Engage with clean energy and climate tech professionals through podcast advertising</h1>
                <a href="#" class="learn-more pink">Learn more</a>
            </div>
        </div>
    </div>
</div>