<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('News with hero section', 'ltm') . '</h3>';
}
// Set defaults News with hero block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'number_of_items' => 3,
        'type' => '',
        'category' => '',
        'tag' => '',
        'post_type' => '',
        'custom' => [],
        'page_data' => false,
        'display' => false,
        'blockAttributes' => [],
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
        'post_type' => $post_type,
        'custom'    => $custom,
        'exclude'   => true,
        'number_of_items'  => $number_of_items,
    ],
    [
        'post_type'         => 'post',
        'posts_per_page'    => 3,
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
                "class" => 'content-section posts-hero-section',
                "id" => 'news-with-hero-section' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>>
    <ul class="posts">
        <?php
        while($items->have_posts() ) {
            $items->the_post();

            \LatitudeMedia\Page_Data()->addItems([get_the_ID()]);

            $postItemTemplate = get_wrap_rows_from_template('<li>
                        <div class="image-folder">[thumb]</div>
                        <div class="content-folder">
                            [tags-list]
                            <h6>[title]</h6>
                            <div class="info">
                                [author]<span></span>[date]
                            </div>
                        </div>
                    </li>');

            $rows = $postItemTemplate['rows'];
            $wrap = $postItemTemplate['wrap'];
            if($items->current_post === 0) {
                $postItemTemplate = get_wrap_rows_from_template('<li class="prime-post">
                            <div class="content-folder">
                                [tags-list]
                                <h1>[title]</h1>
                                [excerpt]
                                <div class="info">
                                    [author]<span></span>[date]
                                </div>
                            </div>
                            <div class="image-folder">[thumb]</div>
                        </li>');

                $rows = $postItemTemplate['rows'];
                $wrap = $postItemTemplate['wrap'];
            }

            get_template_part(
                'template-parts/components/post',
                'item',
                array(
                    'post_id'  => get_the_ID(),
                    'rows'     => $rows,
                    'settings' => array(
                        'thumb'   => array(
                            'size'       => 'news-with-hero',
                            'link'       => true,
                            'link_class' => '',
                            'alt_image'  => false,
                            'type'       => true,
                        ),
                        'author' => array(
                            'link_class' => 'author'
                        ),
                        'date' => array(
                            'format' => 'M j, Y'
                        ),
                    ),
                    'wrap'     => $wrap,
                )
            );
            wp_reset_postdata();
        }
        ?>
    </ul>
</div>