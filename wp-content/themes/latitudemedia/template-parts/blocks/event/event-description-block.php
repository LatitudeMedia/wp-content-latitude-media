<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event description block', 'ltm') . '</h3>';
}
// Set defaults Event description block.
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
            <h2>The state of play for AI in the power sector</h2>
            <div class="bordered-title green">About</div>
            <article>
                <p>In 2024, the impacts of AI on the power sector are inescapable. Across the country, utilities,
                    IPPs, and transmission operators are evaluating, piloting, and deploying AI-based solutions to
                    address critical challenges across their networks. At the same time, they’re seeing
                    unprecedented load growth driven by AI-specific data centers.</p>
                <p>At Transition-AI 2024, Latitude Media is convening a broad range of experts for thoughtful
                    discussions, case studies, panel sessions, and analyst presentations around three urgent
                    themes:</p>
                <p><strong>Reliability</strong> – From grid modeling and planning to automated asset inspection,
                    vegetation management, wildfire detection, and anomaly detection, AI solutions offer new levels
                    of visibility into asset management and are setting the stage for true predictive maintenance.
                </p>
                <p><strong>Customer analytics and experience</strong> – Utilities are leveraging AI in limited ways
                    to address their customers today, from segmentation, onboarding, and program management to
                    enhanced customer service offerings. The advent of AMI 2.0, smart EV charging demands, and the
                    opportunity to use advanced chatbots to augment customer service present many questions to
                    utilities looking to enhance customer-side operations and service today.</p>
                <p><strong>Load growth</strong> – The rapid rise of AI is driving power demand far beyond initial
                    forecasts, presenting serious challenges to utilities and system operators. At the same time, AI
                    is improving resource planning and forecasting capabilities to generate more efficient operation
                    within the power sector.</p>
                <p>For inquiries about media passes, government discounts, or speaker applications, contact us at <a
                            href="mailto:events@latitudemedia.com">events@latitudemedia.com</a>.</p>
            </article>
        </div>
    </div>
</div>