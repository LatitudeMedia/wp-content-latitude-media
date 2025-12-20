<?php

if (is_admin()) {
  echo '<h3 style="text-align: center;">' . __('Event contact us block', 'ltm') . '</h3>';
}
// Set defaults Event contact us block.
$options = wp_parse_args(
  $args,
  [
    'title'     => 'Contact us',
    'contact_links'  => [],
    'display'   => false,
    'blockAttributes' => [],
  ]
);

extract($options);


if (!$display && !is_admin()) {
  return;
}

if (empty($contact_links) && empty($title)) {
  return;
}

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
    [
      "class" => 'content-block contact-us-section',
      "id" => $blockAttributes['anchor'] ?: '',
    ]
  )
);
?>

<div <?php echo $blockAttrs; ?>>
  <div class="container-narrow">
    <?php if ($title) : ?>
      <div class="contact-us-section-wrapper">
        <div class="bordered-title green"><?php echo $title; ?></div>
      </div>
    <?php endif; ?>
    <div class="contact-us-cards-wrapper">
      <?php foreach ($contact_links as $card) : ?>
        <div class="contact-us-card">
          <?php if ($card['title']) : ?>
            <div class="card-title">
              <h2><?php echo $card['title']; ?></h2>
            </div>
          <?php endif; ?>
          <?php if ($card['description']) : ?>
            <div class="card-content">
              <?php echo $card['description']; ?>
            </div>
          <?php endif; ?>
          <?php if ($card['cta_link']) : ?>
            <div class="card-button">
              <a href="<?php echo $card['cta_link']['url']; ?>" class="cta-button green" target="<?php echo $card['cta_link']['target']; ?>">
                <?php echo $card['cta_link']['title']; ?>
              </a>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>