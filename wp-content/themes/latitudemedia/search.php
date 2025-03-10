<?php

get_header();

global $wp_query;

$postItemTemplate = get_wrap_rows_from_template('<li>
                        <div class="image-folder">
                            [thumb]
                        </div>
                        <div class="content-folder">
                            <h4>[title]</h4>
                            <div class="info">
                                [date]
                                <span></span>
                                [excerpt]
                            </div>
                        </div>
                </li>');

get_template_part('template-parts/components/titles/topic', 'title',
    ['title' => 'Search results']
);

?>

<div class="topics-archive-section">
    <div class="container-narrow">
        <div class="topics-archive-section-wrapper load-more-container">
            <div class="posts-list-section load-more-posts">
                <?php if($wp_query->have_posts()) : ?>

                <ul class="posts">
                    <?php
                    while($wp_query->have_posts()) {
                        $wp_query->the_post();
                        if(get_post_type() === 'in-house-ads') continue;
                        get_template_part(
                            'template-parts/components/post',
                            'item',
                            array(
                                'post_id'  => get_the_ID(),
                                'settings' => array(
                                    'thumb'   => array(
                                        'size'       => 'articles-list',
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
                                'rows'     => $postItemTemplate['rows'],
                                'wrap'     => $postItemTemplate['wrap'],
                            )
                        );
                        wp_reset_postdata();
                    }
                    ?>
                </ul>

                <?php
                    endif;
                    do_action('paginator', $wp_query, true, 'page');
                ?>
            </div>
        </div>
    </div>
</div>

<?php
    get_footer();
?>
