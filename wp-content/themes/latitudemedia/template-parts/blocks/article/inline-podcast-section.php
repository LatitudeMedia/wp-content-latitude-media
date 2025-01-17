<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Inline podcast block', 'ltm') . '</h3>';
}
// Set defaults Inline podcast block.
 
$options = wp_parse_args(
    $args,
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
$links = [
    'apple'     => get_field('apple_episode_link', $post_id),
    'spotify'   => get_field('spotify_episode_link', $post_id),
];
$links = array_filter($links);

if( empty($embed_code) ) {
    return;
}

?>
<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block',
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>

    <?php echo $embed_code; ?>
    <?php if( !empty($links) ) : ?>
    <div class="podcast-info">
        <div class="row">
            <?php
            get_template_part('template-parts/podcast/listening', 'links', [
                'title' => 'Listen to the episode on:',
                'links' => $links
            ]);
            ?>
        </div>
    </div>
    <?php endif; ?>

</div>