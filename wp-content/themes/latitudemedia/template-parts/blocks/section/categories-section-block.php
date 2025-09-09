<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Categories section block', 'ltm') . '</h3>';
}
// Set defaults Categories section block.
$options = wp_parse_args(
    $args,
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
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>
    <div class="container">
        <div class="section-top-nav-section-wrapper">
            <div class="section-breadcrumb">
                <ul>
                    <?php
                    if($post_id) {
                        printf('<li><span>%1$s</span>/</li>',  $section->name);
                        printf('<li><a href="#">All in %1$s</a></li>',  $section->name);
                    }
                    ?>
                </ul>
            </div>
            <?php
            if($post_id) {
                printf('<div class="section-name">%s</div>', $section->name);
            }
            ?>
            <div class="section-categories">
                <?php if($categories) : ?>
                    <span class="title">TAGS:</span>
                    <div class="selected-option">All in <?php echo $section->name; ?></div>
                    <ul>
                        <?php
                        if($post_id) {
                            printf('<li><a href="%s">All in %s</a></li>', get_the_permalink($post_id), $section->name);
                        }

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