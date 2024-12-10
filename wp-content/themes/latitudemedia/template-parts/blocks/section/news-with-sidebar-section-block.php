<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('News with sidebar section block', 'ltm') . '</h3>';
}
// Set defaults News with sidebar section block.
$options = wp_parse_args(
    $args,
    [
        'post_id'           => null,
        'sidebar_widget'    => null,
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);


if(!$display && !is_admin()) {
    return;
}

$section = get_field('section_type', $post_id);
if(!$section) {
    return;
}

?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block section-topics-section',
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>
    <div class="container">
        <div class="section-topics-section-wrapper">
            <?php get_template_part('template-parts/blocks/homepage/begin','sidebar'); ?>
            <div class="topics-archive-section-wrapper">
                <div class="posts-list-section">
                    <?php
                    $categories = get_section_cats($section->term_id, 'ids');
                    $args = [
                        'number_of_items'   => 3,
                        'display'           => true,
                        'page_data'         => true,
                        'custom_args' => [
                            'cat' => $categories
                        ]
                    ];

                    get_template_part('template-parts/category/articles', 'list',
                        $args
                    );

                    echo do_blocks('<!-- wp:acf/subscribe-form-block {"name":"acf/subscribe-form-block","data":{"title":"Get Latitude Media in your inbox","_title":"field_672ca97f40920","form_code":"\u003cstyle\u003e\r\n  fieldset {\r\n max-width: 100% !important;\r\n }\r\n .hbspt-form {\r\n    width: 100%;\r\n }\r\n    #industry-bfa1a268-143c-441f-830d-f6ab3f7535b4 input, select {\r\n    width: 100%;\r\n        border: 1px solid #C6168D;\r\n        margin-bottom: 1.5em;\r\n        color: #0F1E42 !important;\r\n        vertical-align: middle;\r\n        background-color: #fff;\r\n        padding: 8px 12px;\r\n        font-size: 14px;\r\n        margin-top: 0em !important;\r\n        line-height: 1.42857;\r\n    }\r\n    \r\n    .input input {\r\n    width: 100% !important; \r\n        border: 1px solid #C6168D !important;\r\n        margin-bottom: 1.5em !important;\r\n        color: #0F1E42 !important;\r\n        vertical-align: middle !important;\r\n        background-color: #fff !important;\r\n        padding: 8px 12px !important;\r\n        font-size: 14px !important;\r\n        \r\n        line-height: 1.42857 !important;\r\n    }\r\n   select {\r\n          margin-top: 1.5em;\r\n\r\n   }\r\n   label {\r\n  font-weight: 400;\r\n   }\r\n\r\n  .form-columns-1{\r\n  \tmargin-bottom: 1rem;\r\n  }\r\n  .form-columns-2{\r\n    grid-column-gap: 1rem;\r\n    display: flex;\r\n  }\r\n    .hs-button {\r\n        border: 1px solid #C6168D;\r\n        background-color: #C6168D;\r\n        color: #fff !important;\r\n        letter-spacing: 4px;\r\n        text-transform: uppercase;\r\n        padding: 1.25em 2em;\r\n        font-family: Inter, sans-serif;\r\n        font-weight: 800;\r\n        transition: box-shadow .3s, transform .3s;\r\n        cursor: pointer;\r\n    }\r\n    \r\n    .hs-button:hover {\r\n        background-color: #fff;\r\n        color: #C6168D !important;\r\n    }\r\n    \r\n    .hs-error-msgs, .submitted-message {\r\n        list-style: none;\r\n        padding-left: 0px;\r\n        color: #fff !important;\r\n    }\r\n    .hs_error_rollup {\r\n\t\t\t\tdisplay: none;\r\n\t\t}\r\n    \r\n   .inputs-list {\r\n    list-style-type: none;\r\n  }\r\n   .hs-form-booleancheckbox {\r\n    align-items: center;\r\n  }\r\n  .hs-input[type=checkbox] {\r\n  width: 1rem !important;\r\n  height: 1rem !important;\r\n  margin-right: 0.5rem !important;\r\n  min-height: auto !important;\r\n  margin-bottom: 0px !important;\r\n   vertical-align: middle !important;\r\n   text-align: left !important;\r\n}\r\n\r\n\r\n\t.hs-form-field {\r\n  \tmargin-bottom: 10px;\r\n  }\r\n  .hs-field-desc {\r\n  margin-bottom: .5rem;\r\n  }\r\n  \r\n  .actions {\r\n  text-align: center !important;\r\n  }\r\n  \r\n\u003c/style\u003e\r\n\r\n\u003cdiv id=\u0022hubspotTarget1\u0022\u003e\u003c/div\u003e\r\n\r\n\u003cscript charset=\u0022utf-8\u0022 type=\u0022text/javascript\u0022 src=\u0022//js.hsforms.net/forms/embed/v2.js\u0022\u003e\u003c/script\u003e\r\n\u003cscript\u003e\r\n  hbspt.forms.create({\r\n    region: \u0022na1\u0022,\r\n    portalId: \u002244409563\u0022,\r\n    formId: \u0022bfa1a268-143c-441f-830d-f6ab3f7535b4\u0022\r\n  });\r\n\u003c/script\u003e","_form_code":"field_672ca9a740921","description":"Subscribe to Latitude\'s free newsletters today to receive the latest news on the energy transition:","_description":"field_672ca9b740922","display":"1","_display":"field_672ca9403c777"},"mode":"edit"} /-->');

                    $args['number_of_items'] = 5;
                    get_template_part('template-parts/category/articles', 'list',
                        $args
                    );
                    ?>
                    <a href="<?php echo get_term_link($section->term_id, 'section'); ?>" class="cta-button pink"><span><?php _e('See more', 'ltm'); ?></span></a>
                </div>
            </div>
            <?php get_template_part('template-parts/blocks/homepage/end','sidebar', array('sidebar_widget' => $sidebar_widget)); ?>
        </div>
    </div>
</div>