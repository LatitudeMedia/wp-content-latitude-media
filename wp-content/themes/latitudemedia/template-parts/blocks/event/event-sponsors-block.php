<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event sponsors block', 'ltm') . '</h3>';
}
// Set defaults Event sponsors block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'title'             => 'Sponsors',
        'sponsors_category' => [],
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($sponsors_category) ) {
    return;
}

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block event-sponsors-section green',
          "id" => 'event-sponsors-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);
?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="bordered-title"><?php _e($title); ?></div>
        <div class="event-sponsors-section-wrapper">

            <?php
            foreach ($sponsors_category as $category) {
                if( empty($category['sponsors']) ) {
                    continue;
                }
                $sponsorsPosts = get_published_posts_by_ids($category['sponsors'], ['post_type' => 'sponsors']);

                $listHtml = '';
                while($sponsorsPosts->have_posts()) {
                    $sponsorsPosts->the_post();
                    $sponsorUrl = get_field('website_link', get_the_ID()) ?: '#';
                    $img = thumbnail_formatting(get_the_ID(), ['size' => 'event-sponsors-list', 'link' => false], false);
                    $listHtml .= sprintf('<li><a href="%s" target="_blank">%s</a></li>', $sponsorUrl, $img);
                }

                if( !empty($category['title']) ) {
                    $titleHtml = sprintf('<h5>%s</h5>', $category['title']);
                }
                if($listHtml) {
                    printf('<div class="sponsors-row">%s<ul>%s</ul></div>', $titleHtml ?? '', $listHtml);
                }
            }
            ?>
        </div>
    </div>
</div>
