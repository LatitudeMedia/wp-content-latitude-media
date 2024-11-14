<?php
// Set defaults Featured-articles block.
global $wp_query;

$postItemTemplate = get_wrap_rows_from_template('<li>
            <div class="image-folder">
                [thumb]
            </div>
            <div class="content-folder">
                [tags-list]
                <h4>[title]</h4>
                [excerpt]
                <div class="info">
                    [author]
                    <span></span>
                    [date]
                </div>
            </div>
        </li>');
$options = wp_parse_args(
    array_merge($postItemTemplate,
        $args
    ),
    [
        'items'         => [],
        'number_of_items' => get_option('posts_per_page'),
        'custom_args'   => [],
        'page_data'     => false,
        'forced'        => false,
        'pagination'    => false,
        'display'       => false,
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

$exclude = \LatitudeMedia\Page_Data()->getItems();

if($forced && !$items) {
    $result = $wp_query;
} else {
    $queryArgs = [
        'post_type' => "post",
        'posts_per_page' => (int)$number_of_items,
        'post__not_in' => $exclude
    ];

    $queryArgs = array_merge($queryArgs, $custom_args);

    $result = \LatitudeMedia\Manage_Data()->curated_query($queryArgs, array_diff((!empty($items) ? $items : []), $exclude));
}

if(!$result->have_posts()) {
    return;
}
?>

<ul class="posts">
    <?php
        while($result->have_posts()) {
            $result->the_post();

            \LatitudeMedia\Page_Data()->addItems([get_the_ID()]);
            get_template_part(
                'template-parts/components/post',
                'item',
                array(
                    'post_id'  => get_the_ID(),
                    'settings' => array(
                        'thumb'   => array(
                            'size'       => 'article-related',
                            'link'       => true,
                            'alt_image'  => false,
                        ),
                        'author' => array(
                            'link_class' => 'author'
                        ),
                        'date' => array(
                            'format' => 'M j, Y'
                        ),
                    ),
                    'rows'     => $rows,
                    'wrap'     => $wrap,
                )
            );
            wp_reset_postdata();
        }
    ?>
</ul>

<?php
    if($pagination) {
        do_action('paginator', $result, true);
    }
?>