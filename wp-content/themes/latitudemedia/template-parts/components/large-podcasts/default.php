<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Large podcasts block DEFAULT', 'ltm') . '</h3>';
}

// Set defaults Large podcasts block.
$options = wp_parse_args(
    $args,
    [
        'title'             => 'Podcasts',
        'number_of_items'   => 3,
        'columns'           => [],
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($columns) ) {
    return;
}

$blockAttrs = wp_kses_data(
    get_block_wrapper_attributes(
        [
            "class" => 'content-block podcasts-large-blue-section',
            "id" => 'three-podcasts-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
        ]
    )
);

$podcastPosts = get_published_posts_by_ids($columns, ['post_type' => 'podcasts']);
if( !$podcastPosts->have_posts() ) {
    return;
}
?>

<div
    <?php echo $blockAttrs; ?>
>
    <div class="container">
        <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
        <div class="podcasts-large-blue-section-wrapper">
            <?php while ($podcastPosts->have_posts()) :
                $podcastPosts->the_post();
                $basePodcastId = get_the_ID();
                $items = get_podcast_episodes($basePodcastId, ['posts_per_page'    => $number_of_items ?? 3,]);
                if( !$items->have_posts() ) {
                    continue;
                }
                ?>
                <div class="col">
                    <div class="posts-list-section">
                        <ul class="posts">
                            <?php
                            $postItemTemplate = get_wrap_rows_from_template('<li>
                                <div class="content-folder">
                                    [title]                                   
                                </div>
                                <div class="image-folder blue">
                                        [thumb]
                                    </div>
                            </li>');
                            $rows = $postItemTemplate['rows'];
                            $wrap = $postItemTemplate['wrap'];
                            get_template_part(
                                'template-parts/components/post',
                                'item',
                                array(
                                    'post_id'  => $basePodcastId,
                                    'settings' => array(
                                        'title' => array(
                                            'wrap' => '<span class="title">%1$s</span>',
                                            'link' => false
                                        ),
                                        'thumb'   => array(
                                            'size'       => 'news-with-hero',
                                            'link'       => true,
                                        ),
                                    ),
                                    'rows'          => $rows,
                                    'wrap'          => $wrap,
                                )
                            );

                            $postItemTemplate = get_wrap_rows_from_template('<li>
                                <div class="content-folder">
                                    [title]
                                    <div class="info">
                                        [time]
                                        [date]
                                    </div>
                                </div>
                            </li>');
                            $rows = $postItemTemplate['rows'];
                            $wrap = $postItemTemplate['wrap'];
                            while( $items->have_posts() ) {
                                $items->the_post();

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
                                        ),
                                        'rows'          => $rows,
                                        'wrap'          => $wrap,
                                    )
                                );
                                wp_reset_postdata();
                            }
                            ?>
                        </ul>
                        <?php do_action('button_unit', ['url' => get_the_permalink($basePodcastId), 'title' => 'Browse episodes'], null, 'strict-button'); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>