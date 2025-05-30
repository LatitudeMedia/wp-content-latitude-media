<?php
// Set defaults footer socials.

$options = wp_parse_args(
    $args,
    [
        'dynamic_pagination' => true,
        'pagination_base' => 'page',
        'layout_wrapper' => 'container-narrow',
    ]
);

$paginationClass = ($options['dynamic_pagination'] && isset($options['pagination']) && $options['pagination']) ? 'load-more-container' : '';
?>

<div class="topics-archive-section">
    <div class="<?php echo $options['layout_wrapper']; ?>">
        <div class="topics-archive-section-wrapper <?php echo $paginationClass; ?>">
            <div class="posts-list-section load-more-posts">
                <?php
                get_template_part('template-parts/category/articles', 'list',
                    $options
                );
                ?>
            </div>
        </div>
    </div>
</div>