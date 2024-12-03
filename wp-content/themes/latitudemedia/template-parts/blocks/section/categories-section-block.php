<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Categories section block', 'ltm') . '</h3>';
}
// Set defaults Categories section block.
$options = wp_parse_args(
    array_merge($args),
    [
        'display'           => false,
        'post_id'           => null,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

$section = get_field('section_type', $post_id);
if(!$section) {
    return;
}

$categories = get_section_cats($section->term_id, 'all');
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block section-top-nav-section',
                "id" => 'categories-section-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <div class="container">
        <div class="section-top-nav-section-wrapper">
            <div class="section-name"><?php echo get_the_title($post_id); ?></div>
            <div class="section-categories">
                <?php if($categories) : ?>
                    <ul>
                        <?php
                        foreach ($categories as $category) {
                            printf('<li><a href="%s">%s</a></li>', get_term_link($category->term_id, 'category'), $category->name);
                        }
                        ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>