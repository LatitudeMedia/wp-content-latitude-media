<?php

/**
 * Template Name: Top podcasts template
 */
get_header();

$page_id = get_the_ID();

$featured_podcast_id   = get_field('featured_podcast', $page_id);
$featured_published    = $featured_podcast_id ? get_published_post_by_id($featured_podcast_id, ['post_type' => 'post']) : false;
$featured_effective_id = $featured_published ? $featured_published->ID : 0;
$featured_podcast_exists = $featured_effective_id;

$podcast_ids = [];
foreach (get_field('podcast_list', $page_id) as $row) {
    if (! empty($row['podcast'])) {
        $podcast_ids[] = (int) $row['podcast'];
    }
}
if ($featured_effective_id) {
    $podcast_ids = array_values(array_diff(array_unique($podcast_ids), [$featured_effective_id]));
}

$podcast_rank = 1;
$podcast_title_prefix = [];
if ($featured_podcast_exists) {
    $podcast_title_prefix[$featured_effective_id] = $podcast_rank++ . '. ';
}
foreach ($podcast_ids as $pid) {
    $podcast_title_prefix[$pid] = $podcast_rank++ . '. ';
}
$podcast_title_filter = null;
if ($podcast_title_prefix) {
    $podcast_title_filter = function ($title, $post_id = 0) use ($podcast_title_prefix) {
        $id = (int) $post_id;
        return ($id && isset($podcast_title_prefix[$id])) ? $podcast_title_prefix[$id] . $title : $title;
    };
    add_filter('the_title', $podcast_title_filter, 10, 2);
}

$sidebar = get_field('sidebar_widget', $page_id) ?: 'podcast-default-sidebar';
?>
<div class="podcast-category-hero-section right-image-folder">
    <div class="container">
        <div class="podcast-category-hero-section-wrapper">
            <div class="content-folder">
                <h1><?php the_title(); ?></h1>
                <?php if (has_excerpt()) : ?>
                    <p><strong><?php the_excerpt(); ?></strong></p>
                <?php endif; ?>
                <?php the_content(); ?>
            </div>
            <div class="image-folder">
                <?php
                do_action('thumbnail_formatting', $page_id, ['size' => 'podcast-landing-overview', 'link' => false]);
                ?>
            </div>
        </div>
    </div>
</div>
<?php
if ($featured_podcast_exists) {
    get_template_part(
        'template-parts/podcast/featured',
        'podcast',
        ['podcast_id' => $featured_effective_id]
    );
}
?>
<div class="right-sidebar-layout">
    <div class="container">
        <div class="right-sidebar-layout-wrapper">
            <div class="main-column load-more-container">
                <div class="posts-list-section load-more-posts">
                    <?php
                    if (! empty($podcast_ids)) {
                        $postItemTemplate = get_wrap_rows_from_template('<li>
                            <div class="image-folder">
                                [thumb]
                            </div>
                            <div class="content-folder">
                                [tags-list]
                                [title]
                                [excerpt]
                                <div class="info">
                                    <div class="post-date">
                                        [time]
                                        [date]
                                    </div>
                                </div>
                            </div>
                        </li>');

                        get_template_part(
                            'template-parts/category/articles',
                            'list',
                            [
                                'forced'            => true,
                                'pagination'        => false,
                                'display'           => true,
                                'items'             => $podcast_ids,
                                'number_of_items'   => count($podcast_ids),
                                'custom_args'       => [
                                    'post_type'      => 'post',
                                    'posts_per_page' => count($podcast_ids),
                                ],
                                'rows'              => $postItemTemplate['rows'],
                                'wrap'              => $postItemTemplate['wrap'],
                            ]
                        );
                    }
                    if ($podcast_title_filter) {
                        remove_filter('the_title', $podcast_title_filter, 10);
                    }
                    ?>
                </div>
            </div>
            <div class="sidebar">
                <?php dynamic_sidebar($sidebar); ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer();
