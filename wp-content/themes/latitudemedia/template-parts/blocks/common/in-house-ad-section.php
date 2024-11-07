<?php

// Set defaults In house ad block.
//                array(
//                    'attrs' => array(
//                        'name'  		=> 'in-house-ad-section',
//                        'title' 		=> __('In house ad block', 'ltm'),
//                        'path'  		=> 'common',
//                    ),
//                    'icon'  		=> 'table-col-before',
//                    'description' => __('In house ad section block', 'ltm'),
//                    'post_types' 	=> array( 'page' ),
//                    'category'  	=> 'ltm-page-blocks',
//                    'keywords'    => array( __('In house ad block', 'ltm') ),
//                    'example'  	=> array(
//                        'attributes' => array(
//                            'mode' => 'preview',
//                            'data' => array(
//                                'image' => 'in-house-ad-section.png',
//                            )
//                        )
//                    )
//                ),

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

?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block',
                "id" => 'in-house-ad-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/src/images/in_house_ad.png" alt="In house ad">
    <div class="news-rec-banner-section" style="background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/src/images/banner_rec_bg.png);">
        <div class="overlay"></div>
        <div class="news-rec-banner-section-wrapper">
            <div class="eyebrow green">
                <div class="eyebrow-label">Infographic</div></div>
            <h5>Transition-AI 2024 | Washington DC | December 3</h5>
            <p>Join industry experts for a one-day conference on the impacts of AI on the power sector across three themes: reliability, customer experience, and load growth.</p>
            <a href="#" class="cta-button green">Register</a>
        </div>
    </div>
</div>
