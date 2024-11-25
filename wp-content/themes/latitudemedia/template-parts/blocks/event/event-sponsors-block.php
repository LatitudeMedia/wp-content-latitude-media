<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event sponsors block', 'ltm') . '</h3>';
}
// Set defaults Event sponsors block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'display' => false,
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
          "class" => 'content-block event-sponsors-section green',
          "id" => 'event-sponsors-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="bordered-title">Sponsors</div>
        <div class="event-sponsors-section-wrapper">
            <div class="sponsors-row">
                <h5>Sponsors</h5>
                <ul>
                    <li>
                        <a href="#">
                            <img alt="sponsor" src="client/assets/images/sponsor1.jpg">
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img alt="sponsor" src="client/assets/images/sponsor2.jpg">
                        </a>
                    </li>
                </ul>
            </div>
            <div class="sponsors-row">
                <h5>Industry and Media Partners</h5>
                <ul>
                    <li>
                        <a href="#">
                            <img alt="sponsor" src="client/assets/images/sponsor3.jpg">
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img alt="sponsor" src="client/assets/images/sponsor4.jpg">
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img alt="sponsor" src="client/assets/images/sponsor5.jpg">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
