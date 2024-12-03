<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Our approach block', 'ltm') . '</h3>';
}
// Set defaults Our approach block.
$options = wp_parse_args(
    array_merge($args),
    [
        'title'     => 'OUR APPROACH',
        'copy'      => '',
        'options'   => [],
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
          "class" => 'content-block approach-section orange',
          "id" => 'our-approach-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="bordered-title"><?php _e($title); ?></div>
        <div class="approach-section-wrapper">
            <div class="caption">
                <?php _e($copy); ?>
            </div>
            <ul class="numbered-features">
                <?php
                    foreach ($options as $key => $option) {
                        printf('<li><div class="number"><span>%s</span></div><div class="description"><div class="title">%s</div><p>%s</p></div></li>', ($key+1), $option['title'], $option['description']);
                    }
                ?>
            </ul>
        </div>
    </div>
</div>