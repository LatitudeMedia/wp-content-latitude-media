<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Authors list block', 'ltm') . '</h3>';
}
// Set defaults Authors list block.

$options = wp_parse_args(
    array_merge($args),
    [
        'authors_type' => [
                "value" => "latitude-media-staff",
                "label" => "Latitude Media Staff",
        ],
        'title'             => '',
        'custom'            => [],
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if(!$authors_type) {
    return;
}

$authors_args = array(
    'hide_empty' => false,
    'taxonomy'  => 'author',
    'order'         => 'ASC',
    'orderby'       => 'meta_value',
    'meta_key'      => 'last_name',
);
if( $options['authors_type']['value'] !== 'custom' ) {
    $authors_args['meta_query'] = array(
        array(
            'key'       => 'author_type',
            'value'     => $authors_type['value'],
            'compare'   => '='
        )
    );
} else {
    $authors_args['include'] = $custom;
    $options['authors_type']['label']  = $title;
}
$authors = get_terms( $authors_args );

if( empty($authors) ) {
    return;
}

?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block our-team-section',
                "id" => 'authors-list-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <div class="container-narrow">
        <div class="our-team-section-wrapper">
            <?php do_action('section_title', $authors_type['label'], '<h2>%1$s</h2>'); ?>
            <ul class="team">
                <?php foreach ($authors as $author) :
                    $authorData = ltm_get_author_data($author);
                    $socialsData = ltm_get_author_socials($author);
                    ?>
                    <li>
                        <div class="image-folder">
                            <a href="<?php echo get_term_link($author,'author')?>">
                                <?php
                                if( !empty($authorData['logo']) ) {
                                    do_action('thumbnail_formatting', ['link' => false], ['image_id' => $authorData['logo']['ID']]);
                                }
                                else {
                                    printf('<img src="%s/src/images/latitude_author_default.png" alt="member">', get_template_directory_uri());
                                }
                                ?>
                            </a>
                        </div>
                        <div class="content-folder">
                            <a href="<?php echo get_term_link($author,'author')?>" class="name"><?php _e($author->name); ?></a>
                            <?php
                            if( !empty($authorData['job_title']) ) {
                                printf('<div class="occupation">%s</div>', $authorData['job_title']);
                            }

                            get_template_part('template-parts/components/social', 'links', [
                                'links' => $socialsData
                            ]);
                            ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
