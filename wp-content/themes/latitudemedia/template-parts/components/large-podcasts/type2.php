<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Large podcasts block DEFAULT', 'ltm') . '</h3>';
}
// Set defaults Large podcasts block.

$options = wp_parse_args(
    $args,
    [
        'title'             => 'Podcasts',
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
            "class" => 'content-block podcasts-hero-section',
            "id" => 'three-podcasts-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
        ]
    )
);

$podcastPosts = get_published_posts_by_ids($columns, ['post_type' => 'podcasts']);
if( !$podcastPosts->have_posts() ) {
    return;
}

$postItemTemplate = get_wrap_rows_from_template('
<li>
    <div class="image-folder">
        [thumb]                                   
    </div>
    <div class="content-folder">
        [title]
        [excerpt]
        <div class="listen-block">
            [podcast-listening]
        </div>
    </div>
</li>
');
?>
<div
    <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="podcasts-hero-section-wrapper">
            <div class="eyebrow pink">
                <?php do_action('section_title', $title, '<div class="eyebrow-label">%1$s</div>'); ?>
            </div>
            <ul>
                <?php
                    while( $podcastPosts->have_posts() ) {
                        $podcastPosts->the_post();

                        get_template_part(
                            'template-parts/components/post',
                            'item',
                            array(
                                'post_id'  => get_the_ID(),
                                'settings' => array(
                                    'thumb'   => array(
                                        'size'       => 'news-with-hero',
                                        'link'       => true,
                                    ),
                                ),
                                'rows'          => $postItemTemplate['rows'],
                                'wrap'          => $postItemTemplate['wrap'],
                            )
                        );
                    }
                ?>
            </ul>
        </div>
    </div>
</div>