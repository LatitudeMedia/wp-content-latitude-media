<?php
/**
 * Template Name: Research landing template
 *
 */

get_header('intelligence');
?>

    <article>
        <div class="container-narrow">
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
        </div>
    </article>
<?php get_footer();
