<?php
/**
 * The main template file
 *
 */
global $wp_query;
$current_page = intval(max(1, $_GET['current_page'] ?? 1));
get_header();

get_template_part('template-parts/podcast/landing', 'overview');

$featuredEpisod = get_podcast_episodes(get_the_ID(), ['posts_per_page' => 1]);
$featuredEpisodId = $featuredEpisod->post->ID;
if($featuredEpisodId) {
    get_template_part('template-parts/podcast/featured', 'podcast', ['podcast_id' => $featuredEpisodId]);
    wp_reset_postdata();
}

$args = [
    'paged'         => $current_page,
    'post__not_in'  => [$featuredEpisodId]
];
$episodes = get_podcast_episodes(get_the_ID(), $args);
$wp_query = $episodes;

$sidebar = get_field('sidebar_widget', get_the_ID()) ?: 'podcast-default-sidebar';
?>
    <div class="right-sidebar-layout">
        <div class="container">
            <div class="right-sidebar-layout-wrapper">
                <div class="main-column load-more-container">
                    <div class="posts-list-section load-more-posts">
                        <?php
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

                        get_template_part('template-parts/category/articles', 'list',
                            [
                                'forced'        => true,
                                'pagination'    => false,
                                'display'       => true,
                                'rows'          => $postItemTemplate['rows'],
                                'wrap'          => $postItemTemplate['wrap']
                            ]
                        );

                        do_action('paginator', $wp_query, true, 'current_page');

                        ?>
                    </div>
                </div>
                <div class="sidebar">
                    <?php dynamic_sidebar( $sidebar );?>
                </div>
            </div>
        </div>
    </div>

<?php get_footer();

