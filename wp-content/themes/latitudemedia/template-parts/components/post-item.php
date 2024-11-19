<?php
// Set defaults leads block.
$default_settings = [
    'thumb' => [
        'image_id'      => null,
        'size'          => '',
        'mobile_size'   => null,
        'link'          => false,
        'link_class'    => '',
        'img_attr'      => [],
        'default'       => true,
    ],
    'title'     => [
        'link' => true,
    ],
    'tags-list' => [],
    'excerpt'   => [],
    'author'    => [
        'wrap' => '%1$s'
    ],
    'date' => [
        'wrap' => '<div class="date">%1$s</div>',
        'format' => 'F j, Y',
    ],
];
$options = wp_parse_args(
    $args,
    [
        'post_id' => null,
        'rows' => [
            'thumb',
            'title',
            'tags-list',
            'date',
            'author',
            'excerpt',
            'read_more',
        ],
        'settings' => $default_settings,
        'wrap' => false
    ]
);

if(!$args['settings']) {
    $options['settings'] = $default_settings;
} else {
    $options['settings'] = wp_parse_args($options['settings'], $default_settings);
}

extract($options);

$post = $post_id ? get_post($post_id) : get_post();
$resultRows = [];

if(!$post) return;

foreach ( $rows as $row ) {
    if($wrap) ob_start();
    switch($row) {
        case 'thumb': do_action('thumbnail_formatting', $post_id, $settings[$row]); break;
        case 'title': do_action('print_article_title', $post_id, $settings[$row]); break;
        case 'tags-list': do_action('print_article_tags_list', $post_id); break;
        case 'article-type': do_action('print_article_type', $post_id); break;
        case 'date': do_action('print_article_date', $post_id, $settings[$row]); break;
        case 'author': do_action('print_article_authors', $post_id, $settings[$row]); break;
        case 'excerpt': do_action('print_article_excerpt', $post_id); break;
        case 'time': do_action('print_podcast_time', $post_id); break;
        case 'resource-tag': do_action('print_resource_tag', $post_id); break;
//        case 'read_more': do_action('print_article_read_more', $post_id); break;
    }
    if($wrap) $resultRows[] = ob_get_clean();
}

if($wrap) {
    printf($wrap, ...$resultRows);
}

?>



