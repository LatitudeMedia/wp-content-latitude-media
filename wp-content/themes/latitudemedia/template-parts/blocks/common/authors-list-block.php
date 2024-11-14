<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Authors list block', 'ltm') . '</h3>';
}
// Set defaults Authors list block. 

$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
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
);
if( $authors_type['value'] !== 'custom' ) {
    $authors_args['meta_query'] = array(
        array(
            'key'       => 'author_type',
            'value'     => $authors_type['value'],
            'compare'   => '='
        )
    );
} else {
    $authors_args['include'] = $custom;
    $authors_type['label']  = $title;
}
$authors = get_terms( $authors_args );

if( empty($authors) ) {
    return;
}

$socialLogos = [
        'linkedin' => 'icon_vector_linkedin.svg',
        'x_twitter' => 'icon_vector_X.svg',
        'other_social' => 'icon_world.svg',
];
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
            <h2><?php _e($authors_type['label'])?></h2>
            <ul class="team">
                <?php foreach ($authors as $author) :
                    $authorData = [
                        'logo' => get_field('logo', 'author_' . $author->term_id, true),
                        'job_title' => get_field('job_title', 'author_' . $author->term_id, true)
                    ];

                    $socials = [];
                    if( $social = get_field('linkedin', 'author_' . $author->term_id, true) ) {
                        $socials['linkedin'] = $social;
                    }
                    if( $social = get_field('x_twitter', 'author_' . $author->term_id, true) ) {
                        $socials['x_twitter'] = $social;
                    }
                    if( $social = get_field('other_social', 'author_' . $author->term_id, true) ) {
                        $socials['other_social'] = $social;
                    }
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

                                if( $socials ) {
                                    $socialWrap = '<ul class="socials">%s</ul>';
                                    $socialContent = '';
                                    foreach ($socials as $key => $item) {
                                        $socialContent .= sprintf('<li><a href="%s" target="_blank"><img src="%s/src/images/%s" alt="icon" class="icon"></a></li>', $item, get_template_directory_uri(), $socialLogos[$key] ?? '');
                                    }

                                    printf($socialWrap, $socialContent);
                                }
                            ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>