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

$processed_days = [];
foreach ($days as $day_index => $day) {
  $processed_day = $day;
  $processed_day['agenda_items'] = [];

  if (!empty($day['agenda_items'])) {
    foreach ($day['agenda_items'] as $item_index => $item) {
      $processed_item = $item;
      $processed_item['speakers_data'] = [];
      if (!empty($item['speakers'])) {
        $speaker_ids = [];
        foreach ($item['speakers'] as $speaker) {
          if (is_object($speaker) && isset($speaker->ID)) {
            $speaker_ids[] = $speaker->ID;
          } elseif (is_numeric($speaker)) {
            $speaker_ids[] = $speaker;
          }
        }
        if (!empty($speaker_ids)) {
          $speakersPosts = get_published_posts_by_ids($speaker_ids, ['post_type' => 'speakers']);
          if ($speakersPosts->have_posts()) {
            while ($speakersPosts->have_posts()) {
              $speakersPosts->the_post();
              $speaker_id = get_the_ID();
              $processed_item['speakers_data'][] = [
                'id' => $speaker_id,
                'name' => get_the_title(),
                'job_title' => get_field('job_title', $speaker_id),
                'company' => get_field('company', $speaker_id),
                'image' => has_post_thumbnail($speaker_id) ? get_the_post_thumbnail($speaker_id, 'thumbnail', ['class' => 'event-agenda-v2-speaker-image']) : '',
              ];
            }
            wp_reset_postdata();
          }
        }
      }

      $processed_day['agenda_items'][] = $processed_item;
    }
  }

  $processed_days[] = $processed_day;
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

    <?php if (count($processed_days) > 1) : ?>
      <div class="event-agenda-v2-dropdown-wrapper">
        <div class="selector-container">
          <select class="event-agenda-v2-day-selector" id="agenda-day-selector-<?php echo esc_attr($block_id); ?>">
            <?php foreach ($processed_days as $index => $day) : ?>
              <option value="<?php echo esc_attr($block_id . '-day-' . $index); ?>" <?php echo $index === 0 ? 'selected' : ''; ?>>
                <?php echo esc_html($day['day_title'] ?: 'Day ' . ($index + 1)); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>
    <?php endif; ?>

    <div class="event-agenda-v2-days">
      <?php foreach ($processed_days as $index => $day) : ?>
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
                    <?php if (!empty($item['speakers_data'])) : ?>
                      <div class="event-agenda-v2-item-speakers">
                        <?php foreach ($item['speakers_data'] as $speaker) : ?>
                          <div class="event-agenda-v2-speaker">
                            <?php if ($speaker['image']) : ?>
                              <div class="event-agenda-v2-speaker-image-wrapper">
                                <?php echo $speaker['image']; ?>
                              </div>
                            <?php endif; ?>
                            <div class="event-agenda-v2-speaker-info">
                              <?php if ($speaker['name']) : ?>
                                <div class="event-agenda-v2-speaker-name"><?php echo esc_html($speaker['name']); ?></div>
                              <?php endif; ?>
                              <?php if ($speaker['job_title']) : ?>
                                <div class="event-agenda-v2-speaker-job-title"><?php echo esc_html($speaker['job_title']); ?></div>
                              <?php endif; ?>
                              <?php if ($speaker['company']) : ?>
                                <div class="event-agenda-v2-speaker-company"><?php echo esc_html($speaker['company']); ?></div>
                              <?php endif; ?>
                            </div>
                          </div>
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