<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Listeners info block', 'ltm') . '</h3>';
}
// Set defaults Listeners info block.
$options = wp_parse_args(
    $args,
    [
        'title'     => 'What listeners are saying',
        'list'      => [],
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
          "class" => 'content-block listeners-section',
          "id" => $blockAttributes['anchor'] ?: '',
      ]
  )
);

if ( empty($list) ) {
    return;
}

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="listeners-section-wrapper">
            <h2><?php _e($title); ?></h2>
            <ul>
                <?php
                    foreach ($list as $listItem) {
                        printf('<li><div class="percent">%s</div><p>%s</p></li>', $listItem['number'], $listItem['copy']);
                    }
                ?>
            </ul>
        </div>
    </div>
</div>