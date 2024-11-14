<?php get_header();

global $wp_query;
$term = get_queried_object();
$current_page = max(1, $wp_query->get('paged'));
$section = get_field('section', 'category_' . $term->term_id, true);

$args = [
    'display'       => true,
    'forced'        => true,
    'pagination'    => true,
];

if(is_tax('section')) {
    $section_args = array(
        'hide_empty' => false, // also retrieve terms which are not used yet
        'meta_query' => array(
            array(
                'key'       => 'section',
                'value'     => $term->term_id,
                'compare'   => '='
            )
        ),
        'taxonomy'  => 'category',
        'fields'  => 'ids',
    );
    $cats = get_terms( $section_args );

    $args = [
        'display' => true,
        'custom_args' => [
                'cat' => $cats
        ],
        'pagination'    => true,
    ];
}
?>

<div class="topics-title-block">
    <div class="container">
        <div class="topics-title-block-wrapper">
            <?php if($section) : ?>
               <a href="<?php echo get_term_link($section,'section')?>" class="parent-category-link"><?php echo esc_html__($section->name) ?></a>
            <?php endif; ?>
            <h1 class="topics-title"><?php echo esc_html__($term->name) ?></h1>
        </div>
    </div>
</div>

<?php
get_template_part('template-parts/components/articles-list', 'block',
    $args
);

get_footer();

?>
