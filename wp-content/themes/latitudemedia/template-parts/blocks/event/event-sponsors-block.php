<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event sponsors block', 'ltm') . '</h3>';
}
// Set defaults Event sponsors block.
$options = wp_parse_args(
    $args,
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
          "id" => $blockAttributes['anchor'] ?: '',
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
                    $modalId = uniqid();

                    $sponsorUrl = get_field('website_link', get_the_ID()) ?: '#';
                    $img = thumbnail_formatting(get_the_ID(), ['size' => 'event-sponsors-list', 'link' => false], false);
                    $sponsorHtml = sprintf('<div class="image-folder green"><a href="#%1$s" class="js-modal-open">%2$s</a></div>
                        <div class="content-folder">
                            <a class="more-link js-modal-open" href="%3$s" target="_blank">Read more</a>
                        </div>', $modalId, $img, $sponsorUrl);

                    $sponsorModalHtml = sprintf('<div id="%1$s" class="modal-content green">
                        <div class="modal-folder">
                            <a class="cross js-modal-close"></a>
                            <div class="bio">
                                %2$s
                                
                                <div class="info">
                                    <h3>%3$s</h3>
                                </div>
                            </div>
                            <div class="description">
                                %4$s
                            </div>
                            <a href="%5$s" class="strict-button green" target="_blank">Visit Website</a>
                            <a class="strict-button green js-modal-close">Close</a>
                        </div>
                    </div>', $modalId, $img, get_the_title(), get_the_content(), $sponsorUrl);

                    $listHtml .= sprintf('<li>%1$s %2$s</li>', $sponsorHtml, $sponsorModalHtml);
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
