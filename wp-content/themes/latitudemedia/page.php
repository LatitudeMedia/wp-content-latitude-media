<?php
/**
 * The main template file
 *
 */

get_header();
?>

    <article>
        <div class="container-narrow">
            <?php
            $hide_title = get_field('hide_title', get_the_ID());
            if( !$hide_title ) {
                printf('<h1>%s</h1>', get_the_title() );
            }
            ?>
            <?php the_content(); ?>
        </div>
    </article>
<?php get_footer();
