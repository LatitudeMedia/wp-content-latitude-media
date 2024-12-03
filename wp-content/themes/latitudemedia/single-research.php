<?php get_header('intelligence'); ?>

<?php the_content(); ?>
<?php
$postItemTemplate = get_wrap_rows_from_template('<li>
            <div class="image-folder">
                [thumb]
            </div>
            <div class="content-folder">
                [article-type]
                <p>[title]</p>
                [author]
            </div>
        </li>');
get_template_part(
    'template-parts/blocks/common/news-plates',
    'section',
    array(
        'title'             => 'Related news',
        'type'              => '',
        'number_of_items'   => 3,
        'items'             => '',
        'display'           => true,
        'rows'              => $postItemTemplate['rows'],
        'wrap'              => $postItemTemplate['wrap'],
    )
);
?>

<?php get_footer();