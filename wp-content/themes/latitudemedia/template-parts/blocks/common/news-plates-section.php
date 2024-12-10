<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('News plates block', 'ltm') . '</h3>';
}
// Set defaults News plates block.
$postItemTemplate = get_wrap_rows_from_template('<li>
            <div class="image-folder">
                [thumb]
            </div>
            <div class="content-folder">
                [article-type]
                [title]
                [author]
            </div>
        </li>');

$options = wp_parse_args(
    array_merge($postItemTemplate,
        $args
    ),
    [
        'title'             => 'Related Reading',
        'number_of_items'   => 3,
        'type'              => '',
        'category'          => '',
        'tag'               => '',
        'news_type'         => '',
        'post_type'         => '',
        'custom'            => [],
        'page_data'         => false,
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

$base_color = "#C6168D";
$shadow_color  = "#F9E8F4";
if( !empty($options['blockAttributes']['data']['base_color']) ) {
    $base_color = $options['blockAttributes']['data']['base_color'];
}
if( !empty($options['blockAttributes']['data']['shadow_color']) ) {
    $shadow_color  = $options['blockAttributes']['data']['shadow_color'];
}

if(!$display && !is_admin()) {
    return;
}

\LatitudeMedia\Manage_Data()->setManualSearchArgs(
    [
        'type'      => $type,
        'category'  => $category,
        'tag'       => $tag,
        'news_type' => $news_type,
        'post_type' => $post_type,
        'custom'    => $custom,
        'exclude'   => true,
        'number_of_items'  => $number_of_items,
    ],
    [
        'post_type'         => 'post',
        'posts_per_page'    => 4,
    ]
);
$items = \LatitudeMedia\Manage_Data()->curated_query();

if( !$items->have_posts() ) {
    return;
}
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "style" => "--custom-block-base-color: {$base_color}; --custom-block-shadow-color: {$shadow_color};",
                "class" => 'content-block related-news-section',
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>
    <div class="container-narrow">
        <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
        <div class="related-news-section-wrapper">
            <ul>
                <?php
                while($items->have_posts() ) {
                    $items->the_post();

                    \LatitudeMedia\Page_Data()->addItems([get_the_ID()]);

                    get_template_part(
                        'template-parts/components/post',
                        'item',
                        array(
                            'post_id'  => get_the_ID(),
                            'settings' => array(
                                'thumb'   => array(
                                    'size'       => 'article-related-news',
                                    'link'       => true,
                                ),
                                'author' => array(
                                    'link_class' => 'author'
                                ),
                                'date' => array(
                                    'format' => 'M j, Y'
                                ),
                            ),
                            'rows'          => $rows,
                            'wrap'          => $wrap,
                        )
                    );
                    wp_reset_postdata();
                }
                ?>
            </ul>
        </div>
    </div>
</div>