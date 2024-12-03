<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('News list block', 'ltm') . '</h3>';
}
// Set defaults News list block.
$postItemTemplate = get_wrap_rows_from_template('<li>
                        <div class="image-folder">[thumb]</div>
                        <div class="content-folder">
                            [tags-list]
                            [title]
                            [excerpt]
                            <div class="info">
                                [author]<span></span>[date]
                            </div>
                        </div>
                    </li>');

$options = wp_parse_args(
    array_merge($postItemTemplate,
        $args
    ),
    [
        'title'             => 'Latest news',
        'number_of_items'   => 8,
        'type'              => '',
        'category'          => '',
        'tag'               => '',
        'post_type'         => '',
        'custom'            => [],
        'page_data'         => false,
        'more'              => [],
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
        'post_type' => $post_type,
        'custom'    => $custom,
        'exclude'   => true,
        'number_of_items'  => $number_of_items,
    ],
    [
        'post_type'         => 'post',
        'posts_per_page'    => 8,
    ]
);
$items = \LatitudeMedia\Manage_Data()->curated_query();

if( !$items->have_posts() ) {
    return;
}

?>
<div class="topics-archive-section-wrapper">
    <div
        <?php
        echo wp_kses_data(
            get_block_wrapper_attributes(
                [
                    "class" => 'content-block posts-list-section',
                    "id" => 'news-list-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
                ]
            )
        );

        ?>
    >
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
        <?php do_action('button_unit', $more, null, 'see-all'); ?>
    </div>
</div>