<?php
/**
 * The main template file
 *
 */

get_header();

?>

    <div class="single-post-heading-section">
        <div class="container-narrow">
            <div class="single-post-heading-section-wrapper">
                <ul class="tags-list"><li><span>From the industry</span></li></ul>
                <h1><?php the_title(); ?></h1>
                <?php do_action('print_article_excerpt', get_the_ID()); ?>
                <div class="info">
                    <?php
                        do_action('print_industry_news_company', get_the_ID());
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
                    <article>
                        <?php
                            the_content();
                        ?>
                    </article>
                    <div class="post-share-block">
                        <span class="label"><?php _e('Share:', 'ltm')?></span>
                        <?php get_template_part('template-parts/article/social-sharing','icons'); ?>
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