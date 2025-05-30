<?php
/**
 * The main template file
 *
 */

get_header();
\LatitudeMedia\Page_Data()->addItems([get_the_ID()]);
?>

    <div class="single-post-heading-section">
        <div class="container-narrow">
            <div class="single-post-heading-section-wrapper">
                <?php do_action('print_article_tags_list', get_the_ID()); ?>
                <h1><?php the_title(); ?></h1>
                <?php do_action('print_article_excerpt', get_the_ID()); ?>
                <div class="info">
                    <?php
                        do_action('print_article_authors', get_the_ID(), ['link_class' => 'author']);
                    ?>
                    <span></span>
                    <?php do_action('print_article_date', get_the_ID()); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="right-sidebar-layout pink">
        <div class="container-narrow">
            <div class="right-sidebar-layout-wrapper">
                <div class="main-column">
                    <?php if( !is_news_type('podcast') ) : ?>
                    <div class="post-hero-image-block">
                        <div class="img-holder">
                            <?php
                                echo thumbnail_formatting(get_the_ID(), ['link' => false, '', 'size' => 'large'], false);
                            ?>
                        </div>
                        <?php
                        if($thumbId = get_post_thumbnail_id()) {
                            printf('<div class="credit">%s</div>', wp_get_attachment_caption($thumbId));
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                    <article>
                        <?php
                            the_content();
                        ?>
                    </article>
                    <div class="post-share-block">
                        <span class="label"><?php _e('Share:', 'ltm')?></span>
                        <?php get_template_part('template-parts/article/social-sharing','icons'); ?>
                    </div>
                    <div class="post-author-block">
    					<?php echo do_shortcode('[publishpress_authors_box layout="boxed" 
                             show_title="false" show_avatar="true" show_bio="true" 
                             avatar_shape="circle" image_size="100"]'
                            );
    					?>
					</div>
                </div>
                <div class="sidebar">
                    <?php
                        dynamic_sidebar( 'article-sidebar' );
                    ?>
                </div>
            </div>
        </div>
    </div>
   <?php echo  do_blocks('<!-- wp:acf/news-plates-section {"name":"acf/news-plates-section","data":{"title":"More from Latitude Media","_title":"field_67165a117d211","type":"","_type":"field_6203e8678b566","number_of_items":"3","_number_of_items":"field_6203e9388b56a","items":"","_items":"field_67165a1d7d212","display":"1","_display":"field_671658983a217"},"mode":"edit"} /-->'); ?>
<?php get_footer();
