<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event description block', 'ltm') . '</h3>';
}
// Set defaults Event description block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'title'     => 'About',
        'content'   => '',
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
          "class" => 'content-block event-text-section',
          "id" => 'event-description-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="event-text-section-wrapper">
            <div class="bordered-title green"><?php _e($title); ?></div>
            <article>
                <?php echo $content; ?>
            </article>
        </div>
    </div>
</div>