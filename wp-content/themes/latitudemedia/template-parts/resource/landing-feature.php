<?php
// Set defaults footer socials.
$options = wp_parse_args(
    $args,
    [
        'resource_id' => get_field('featured_resource', 'options'),
    ]
);

if( empty($options['resource_id']) ) {
    return;
}

$queryArgs = [
    'post_type'        => 'resources',
    'posts_per_page'   => 1,
];
$featuredResource = \LatitudeMedia\Manage_Data()->curated_query($queryArgs, [$options['resource_id']]);

if(!$featuredResource->have_posts()) {
    return;
}
?>

<div class="resource-hero-section">
    <div class="container">
        <div class="resource-hero-section-wrapper">
            <?php while( $featuredResource->have_posts() ) : $featuredResource->the_post(); ?>
            <?php
                \LatitudeMedia\Page_Data()->addItems([get_the_ID()]);
                $postItemTemplate = get_wrap_rows_from_template('
                                <div class="image-folder">
                                    [thumb]
                                </div>
                                <div class="content-folder">
                                    [resource-tag]
                                    <h2>[title]</h2>
                                    [excerpt]
                                </div>
                            ');
                $rows = $postItemTemplate['rows'];
                $wrap = $postItemTemplate['wrap'];
                get_template_part(
                    'template-parts/components/post',
                    'item',
                    array(
                        'post_id'  => get_the_ID(),
                        'settings' => array(
                            'thumb'   => array(
                                'size'       => 'news-with-hero',
                                'link'       => true,
                                'link_class' => '',
                                'alt_image'  => false,
                                'type'       => true,
                            ),
                        ),
                        'rows'          => $rows,
                        'wrap'          => $wrap,
                    )
                );
            ?>
            <?php endwhile; ?>
        </div>
    </div>
</div>