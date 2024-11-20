<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Partner porcasts block', 'ltm') . '</h3>';
}
// Set defaults Partner porcasts block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'title'         => false,
        'description'   => false,
        'podcasts'      => false,
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}


$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block',
          "id" => 'partner-porcasts-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

$podcastPosts = get_published_posts_by_ids($podcasts, ['post_type' => 'podcasts']);
$postItemTemplate = get_wrap_rows_from_template('
<li>
    <div class="image-folder orange">
        [thumb]                                   
    </div>
    <div class="content-folder">
        [title]
        [podcast-organization]
        [excerpt]
        <div class="listen-block">
            [podcast-listening]
        </div>
    </div>
</li>
');
?>

<div <?php echo $blockAttrs; ?>
>
    <div class="three-podcasts-section">
        <div class="container-narrow">
            <div class="three-podcasts-section-wrapper">
                <div class="bordered-title orange"><?php _e($title); ?></div>
                <h3 class="heading"><?php _e($description); ?></h3>
                <ul>
                    <?php
                        while ($podcastPosts->have_posts()) {
                            $podcastPosts->the_post();

                            get_template_part(
                                'template-parts/components/post',
                                'item',
                                array(
                                    'post_id'  => get_the_ID(),
                                    'settings' => array(
                                        'thumb'   => array(
                                            'size'       => 'news-with-hero',
                                            'link'       => true,
                                        ),
                                    ),
                                    'rows'          => $postItemTemplate['rows'],
                                    'wrap'          => $postItemTemplate['wrap'],
                                )
                            );

                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

