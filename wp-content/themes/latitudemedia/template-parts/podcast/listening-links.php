<?php
// Set defaults footer socials.
$options = wp_parse_args(
    $args,
    [
        'title' => 'Listen to the episode on:',
        'links' => [],
    ]
);

extract($options);

if( empty($links) ) {
    return;
}

$icons = [
    'apple'     => 'icon_apple_podcast.svg',
    'spotify'   => 'icon_spotify.svg',
    'rss'       => 'icon_rss.svg',
];

?>

<span class="label"><?php _e($title, 'tlm'); ?></span>
<ul class="socials">
    <?php
    foreach ($links as $key => $link) {
        if( empty($link) ) continue;
        printf('<li><a href="%s"><img src="%s" alt="icon" class="icon"></a></li>', $link, get_stylesheet_directory_uri() . '/src/images/' . $icons[$key] ?? '');
    }
    ?>
</ul>