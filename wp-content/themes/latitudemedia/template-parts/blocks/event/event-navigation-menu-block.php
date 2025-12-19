<?php

if (is_admin()) {
  echo '<h3 style="text-align: center;">' . __('Event navigation menu block', 'ltm') . '</h3>';
}
// Set defaults Event navigation menu block.
$options = wp_parse_args(
  $args,
  [
    'navigation_links'  => [],
    'button_one'  => [],
    'button_two'  => [],
    'display'   => false,
    'blockAttributes' => [],
  ]
);

extract($options);

$admin_logged_in = false;

if (is_user_logged_in()) {
  $admin_logged_in = true;
}

if (!$display && !is_admin()) {
  return;
}

if (empty($navigation_links)) {
  return;
}

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
    [
      "class" => 'content-block navigation-menu-section' . ($admin_logged_in ? ' admin' : ''),
      "id" => $blockAttributes['anchor'] ?: '',
    ]
  )
);
?>

<div <?php echo $blockAttrs; ?>>
  <div class="container-narrow">
    <div class="navigation-menu-wrapper">
      <div class="navigation-menu-links">
        <?php foreach ($navigation_links as $link) : ?>
          <a href="#<?php echo $link['anchor']; ?>"><?php echo $link['title']; ?></a>
        <?php endforeach; ?>
      </div>
      <div class="buttons-container">
        <?php if ($button_one) : ?>
          <a href="<?php echo $button_one['url']; ?>" class="nav-button" target="<?php echo $button_one['target']; ?>"><?php echo $button_one['title']; ?></a>
        <?php endif; ?>
        <?php if ($button_two) : ?>
          <a href="<?php echo $button_two['url']; ?>" class="nav-button" target="<?php echo $button_two['target']; ?>"><?php echo $button_two['title']; ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>