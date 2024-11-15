<?php
// Set defaults Authors list block.
$options = wp_parse_args(
    $args,
    [
        'authors_type' => [
            "value" => "latitude-media-staff",
            "label" => "Latitude Media Staff",
        ],
        'title'             => '',
        'custom'            => [],
        'authors'           => [],
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

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

                            if( $socialsData ) {
                                $socialWrap = '<ul class="socials">%s</ul>';
                                $socialContent = '';
                                foreach ($socialsData as $key => $item) {
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