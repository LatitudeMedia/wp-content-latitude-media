<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Inline podcast block', 'ltm') . '</h3>';
}
// Set defaults Inline podcast block.
 
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'post_id' => null,
        'display' => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

$embed_code = get_field('podcast_embed_code', $post_id);
if( empty($embed_code) ) {
    return;
}

$links = [];
$apple_link     = get_field('apple_episode_link', $post_id);
$spotify_link   = get_field('spotify_episode_link', $post_id);
if( !empty($apple_link) ) {
    $links[] = [
            'icon' => 'icon_apple_podcast.svg',
            'link' => $apple_link,
    ];
}

if( !empty($spotify_link) ) {
    $links[] = [
            'icon' => 'icon_spotify.svg',
            'link' => $spotify_link,
    ];
}

?>
<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block',
                "id" => 'inline-podcast-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>

    <?php echo $embed_code; ?>
    <?php if( !empty($links) ) : ?>
    <div class="podcast-info">
        <div class="row">
            <span class="label"><?php _e('Listen to the episode on:', 'tlm'); ?></span>
            <ul class="socials">
                <?php
                foreach ($links as $link) {
                    printf('<li><a href="%s"><img src="%s" alt="icon" class="icon"></a></li>', $link['link'], get_stylesheet_directory_uri() . '/src/images/' . $link['icon']);
                }
                ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

</div>