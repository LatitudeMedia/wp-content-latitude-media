<?php
// Set defaults footer socials.
$options = wp_parse_args(
    $args,
    [
        'podcast_id' => null,
    ]
);

if( empty($options['podcast_id']) ) {
    return;
}

$podcast = get_published_post_by_id($options['podcast_id'], ['post_type' => 'post']);

if( !$podcast ) {
    return;
}

?>

<div class="podcast-category-hero-section left-image-folder bordered">
    <div class="container">
        <div class="podcast-category-hero-section-wrapper">
            <?php
            $postItemTemplate = get_wrap_rows_from_template('
                                <div class="image-folder">
                                    [thumb]                                   
                                </div>
                                <div class="content-folder">
                                    [tags-list]
                                    <h2>[title]</h2>
                                    [excerpt]
                                    <div class="info">
                                        <div class="post-date">
                                            [time]
                                            [date]
                                        </div>
                                    </div>
                                </div>
                            ');
            $rows = $postItemTemplate['rows'];
            $wrap = $postItemTemplate['wrap'];
            get_template_part(
                'template-parts/components/post',
                'item',
                array(
                    'post_id'  => $podcast->ID,
                    'settings' => array(
                        'thumb'   => array(
                            'size'       => 'podcast-landing-overview',
                            'link'       => true,
                        ),
                        'date' => array(
                            'format' => 'M j, Y'
                        ),
                    ),
                    'rows'          => $rows,
                    'wrap'          => $wrap,
                )
            );
            ?>
        </div>
    </div>
</div>
