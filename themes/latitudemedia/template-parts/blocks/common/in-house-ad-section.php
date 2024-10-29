<?php

// Set defaults In house ad block.
//                array(
//                    'attrs' => array(
//                        'name'  		=> 'in-house-ad-section',
//                        'title' 		=> __('In house ad section', 'ltm'),
//                        'path'  		=> 'common',
//                    ),
//                    'icon'  		=> 'table-col-before',
//                    'description' => __('In house ad section block', 'ltm'),
//                    'post_types' 	=> array( 'page' ),
//                    'category'  	=> 'ltm-page-blocks',
//                    'keywords'    => array( __('In house ad section', 'ltm') ),
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
                "class" => 'content-section',
                "id" => 'in-house-ad-section' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/src/images/in_house_ad.png" alt="In house ad">
</div>
