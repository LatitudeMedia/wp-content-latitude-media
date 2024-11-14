<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Research partner block', 'ltm') . '</h3>';
}
// Set defaults Research partner block. 

$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'title'         => '',
        'logo'          => null,
        'description'   => '',
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
          "class" => 'content-block logo-description-block blue',
          "id" => 'research-partner-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
        <div class="logo-description-block-wrapper">
            <div class="logo-image">
                <?php
                   if( !empty($logo) ) {
                        do_action('thumbnail_formatting', null, ['link' => false, 'image_id' => $logo['ID']]);
                    }
                ?>
            </div>
            <?php
                if( !empty($description) ) {
                    printf('<div class="description">%s</div>', $description);
                }
            ?>
        </div>
    </div>
</div>