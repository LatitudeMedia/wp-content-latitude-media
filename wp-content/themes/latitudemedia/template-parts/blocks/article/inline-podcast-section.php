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
                "id" => 'inline-podcast-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
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
                'links' => [
                    'apple'     => $apple_link,
                    'spotify'   => $spotify_link,
                ]
            ]);
            ?>
        </div>
    </div>
    <?php endif; ?>

</div>