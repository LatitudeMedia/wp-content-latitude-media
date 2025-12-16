<?php

if (is_admin()) {
  echo '<h3 style="text-align: center;">' . __('Event agenda V2 block', 'ltm') . '</h3>';
}
// Set defaults Event agenda V2 block.
$options = wp_parse_args(
  $args,
  [
    'title'  => 'Agenda',
    'days'  => [],
    'display'   => false,
    'blockAttributes' => [],
  ]
);

extract($options);

if (!$display && !is_admin()) {
  return;
}

if (empty($days)) {
  return;
}

$block_id = $blockAttributes['anchor'] ?: 'agenda-' . uniqid();
$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
    [
      "class" => 'content-block event-agenda-v2-section',
      "id" => $block_id,
    ]
  )
);
?>

<div <?php echo $blockAttrs; ?>>
  <div class="container-narrow">
    <div class="bordered-title green"><?php echo esc_html($title); ?></div>

    <?php if (count($days) > 1) : ?>
      <div class="event-agenda-v2-dropdown-wrapper">
        <div class="selector-container">
          <select class="event-agenda-v2-day-selector" id="agenda-day-selector-<?php echo esc_attr($block_id); ?>">
            <?php foreach ($days as $index => $day) : ?>
              <option value="<?php echo esc_attr($block_id . '-day-' . $index); ?>" <?php echo $index === 0 ? 'selected' : ''; ?>>
                <?php echo esc_html($day['day_title'] ?: 'Day ' . ($index + 1)); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>
    <?php endif; ?>

    <div class="event-agenda-v2-days">
      <?php foreach ($days as $index => $day) : ?>
        <?php
        $day_id = 'day-' . $index;
        $day_title = $day['day_title'] ?? '';
        $agenda_items = $day['agenda_items'] ?? [];
        ?>
        <div class="event-agenda-v2-day" id="<?php echo esc_attr($block_id . '-' . $day_id); ?>" data-day-index="<?php echo esc_attr($index); ?>" <?php echo $index === 0 ? '' : 'style="display: none;"'; ?>>

          <?php if (!empty($agenda_items)) : ?>
            <div class="event-agenda-v2-agenda-items">
              <?php foreach ($agenda_items as $item) : ?>
                <div class="event-agenda-v2-item">
                  <?php if (!empty($item['time'])) : ?>
                    <div class="event-agenda-v2-item-time"><?php echo esc_html($item['time']); ?></div>
                  <?php endif; ?>
                  <div class="event-agenda-v2-item-content">
                    <?php if (!empty($item['title'])) : ?>
                      <div class="event-agenda-v2-item-title"><?php echo esc_html($item['title']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item['description'])) : ?>
                      <div class="event-agenda-v2-item-description"><?php echo wp_kses_post($item['description']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item['speakers'])) : ?>
                      <div class="event-agenda-v2-item-speakers">
                        <?php foreach ($item['speakers'] as $speaker) : ?>
                          <span class="event-agenda-v2-speaker"><?php echo esc_html($speaker->post_title ?? $speaker); ?></span>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>