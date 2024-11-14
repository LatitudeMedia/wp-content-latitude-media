<?php
/**
 * The main template file
 *
 */

get_header();

?>

    <article>
        <div class="container-narrow">
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
        </div>
    </article>
<?php get_footer();
