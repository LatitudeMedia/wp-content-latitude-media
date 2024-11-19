<?php
// Set defaults footer socials.
$options = wp_parse_args(
    $args,
    [
        'title' => '',
        'links' => [],
    ]
);

extract($options);

if( empty($links) ) {
    return;
}

$icons = [
    'linkedin'      => 'icon_vector_linkedin.svg',
    'x_twitter'     => 'icon_vector_X.svg',
    'instagram'     => 'icon_vector_instagram.svg',
    'facebook'      => 'icon_vector_facebook.svg',
    'other_social'  => 'icon_world.svg',
];

?>

<?php do_action('section_title', $title, '<span class="label">%1$s</span>'); ?>
<ul class="socials">
    <?php
        foreach ($links as $key => $item) {
            printf('<li><a href="%s" target="_blank"><img src="%s/src/images/%s" alt="icon" class="icon"></a></li>', $item, get_template_directory_uri(), $icons[$key] ?? '');
        }
    ?>
</ul>
