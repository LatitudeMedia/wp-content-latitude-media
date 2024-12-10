<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Sidebar editors picks block', 'ltm') . '</h3>';
}
// Set defaults Sidebar editors picks block.

$postItemTemplate = get_wrap_rows_from_template('<li>
            <ul class="tags-list"><li><span>[article-type]</span></li></ul>
            [title]
            <div class="info">
                [author]<span></span>[date]
            </div>
        </li>');

$options = wp_parse_args(
    array_merge($postItemTemplate,
        $args
    ),
    [
        'title'             => 'Editor’s picks',
        'number_of_items'   => 4,
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
                "class" => 'sidebar-block editors-picks-block',
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>
    <div class="editors-picks-block-wrapper">
        <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
        <ul class="posts">
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
                            'author' => array(
                                'link_class' => 'author'
                            ),
                            'date' => array(
                                'format' => 'M j, Y'
                            ),
                            'article-type' => array(
                                    'wrap' => '%s'
                            )
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

