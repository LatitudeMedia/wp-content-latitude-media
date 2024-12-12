<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Recap video block', 'ltm') . '</h3>';
}
// Set defaults Recap video block. 

$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'title'     => false,
        'video'     => false,
        'display'   => false,
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
          "id" => $options['blockAttributes']['anchor'] ?: '',
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="recap-video-section-wrapper">
            <h2><?php echo $title; ?></h2>
            <div class="recap-iframe-folder">
                <?php
                if( !empty($video) )  {
                    echo $video;
                }
                ?>
            </div>
        </div>
    </div>
</div>

