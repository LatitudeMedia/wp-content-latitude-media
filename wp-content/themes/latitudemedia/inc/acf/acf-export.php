<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    acf_add_local_field_group(array(
        'key' => 'group_62f5045de4964',
        'title' => 'Sidebar areas',
        'fields' => array(
            array(
                'key' => 'field_62f50467029bb',
                'label' => 'Sidebar areas',
                'name' => 'sidebar_areas',
                'type' => 'repeater',
                'collapsed' => '',
                'min' => 0,
                'max' => 0,
                'layout' => 'table',
                'button_label' => '',
                'sub_fields' => array(
                    array(
                        'key' => 'field_62f5046d029bc',
                        'label' => 'Name',
                        'name' => 'name',
                        'type' => 'text',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options',
                ),
            ),
        ),
        'style' => 'default',
        'label_placement' => 'left',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_671902f207246',
        'title' => 'Aarticle options',
        'fields' => array(
            array(
                'key' => 'field_671902f284829',
                'label' => 'Post in content form',
                'name' => 'post_in_content_form',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_6719030e8482a',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_671903148482b',
                        'label' => 'Form code',
                        'name' => 'form_code',
                        'type' => 'textarea',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_66fbe0fc57909',
        'title' => 'Author options',
        'fields' => array(
            array(
                'key' => 'field_66fbe0fc3ae2d',
                'label' => 'Author type',
                'name' => 'author_type',
                'type' => 'select',
                'choices' => array(
                    'contributor' => 'Contributor',
                    'podcast-host' => 'Podcast Host',
                    'latitude-media-staff' => 'Latitude Media Staff',
                ),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 1,
            ),
            array(
                'key' => 'field_66fbe53bed04e',
                'label' => 'Linkedin',
                'name' => 'linkedin',
                'type' => 'text',
            ),
            array(
                'key' => 'field_66fbe547ed04f',
                'label' => 'X (Twitter)',
                'name' => 'x_twitter',
                'type' => 'text',
            ),
            array(
                'key' => 'field_66fbe5c1ed050',
                'label' => 'Other Social',
                'name' => 'other_social',
                'type' => 'text',
            ),
            array(
                'key' => 'field_66fbe5cded051',
                'label' => 'Research Author',
                'name' => 'research_author',
                'type' => 'true_false',
                'ui' => 1,
            ),
            array(
                'key' => 'field_66fd24eac1d37',
                'label' => 'LinkOut',
                'name' => 'linkout',
                'type' => 'text',
            ),
            array(
                'key' => 'field_66fd25d0c1d38',
                'label' => 'Logo',
                'name' => 'logo',
                'type' => 'image',
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_67375ad57f5ca',
                'label' => 'Bio',
                'name' => 'bio',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'author',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_66fbb9a1b68b5',
        'title' => 'Category options',
        'fields' => array(
            array(
                'key' => 'field_66fbb9a2b9752',
                'label' => 'Abbreviated Name',
                'name' => 'abbreviated_name',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_6728cdfa19e53',
                'label' => 'Section',
                'name' => 'section',
                'type' => 'taxonomy',
                'taxonomy' => 'section',
                'return_format' => 'object',
                'field_type' => 'select',
                'allow_null' => 1,
                'required'  => true
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'category',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6710e10a5a0fa',
        'title' => 'End sidebar widget',
        'fields' => array(
            array(
                'key' => 'field_6710e10a80a0a',
                'label' => 'Sidebar widget',
                'name' => 'sidebar_widget',
                'type' => 'select',
                'choices' => array(),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/end-sidebar',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    // start_date
    // end_date
    // event_typ
    // event_series
    // location
    // link
    // cover_image
    // background_image
    // past_event
    acf_add_local_field_group(array(
        'key' => 'group_6744b701b9197',
        'title' => 'General event options',
        'fields' => array(
            array(
                'key' => 'field_6713ad31e1567',
                'label' => 'Start date',
                'name' => 'start_date',
                'type' => 'date_time_picker',
                'display_format' => 'm/d/Y g:i a',
                'return_format' => 'm/d/Y g:i a',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_6713ad5ee1568',
                'label' => 'End date',
                'name' => 'end_date',
                'type' => 'date_time_picker',
                'display_format' => 'm/d/Y g:i a',
                'return_format' => 'm/d/Y g:i a',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_6713ade8e156a',
                'label' => 'Event type',
                'name' => 'event_type',
                'type' => 'select',
                'choices' => array(
                    'live' => 'Live',
                    'webinar' => 'Webinar',
                    'virtual' => 'Virtual',
                    'conference' => 'Conference',
                ),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_6713ad88e1569',
                'label' => 'Event series',
                'name' => 'event_series',
                'type' => 'select',
                'choices' => array(
                    'frontier-forum' => 'Frontier Forum',
                    'transition-ai' => 'Transition AI',
                    'webinar' => 'Webinar',
                ),
                'default_value' => false,
                'return_format' => 'array',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_6713ae1fe156b',
                'label' => 'Location',
                'name' => 'location',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6713ae62e156d',
                'label' => 'Link',
                'name' => 'link',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6713ae6fe156e',
                'label' => 'Cover image',
                'name' => 'cover_image',
                'type' => 'image',
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_6713ae7ae156f',
                'label' => 'Background image',
                'name' => 'background_image',
                'type' => 'image',
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_6713ae8de1570',
                'label' => 'Form text',
                'name' => 'form_text',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6713aea8e1571',
                'label' => 'Form code / Registration CTA',
                'name' => 'form_code__registration_cta',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_6713ad28e1566',
                'label' => 'Past event',
                'name' => 'past_event',
                'type' => 'true_false',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_6713b049e1582',
                'label' => 'Gated?',
                'name' => 'gated',
                'type' => 'true_false',
                'instructions' => '(RECAP) This will cause the page not show throughout the site.',
                'default_value' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'events',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'side',
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6713acf86c81d',
        'title' => 'Event options',
        'fields' => array(
            array(
                'key' => 'field_6713acf8e1564',
                'label' => 'Title tag',
                'name' => 'title_tag',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6713ad18e1565',
                'label' => 'Featured',
                'name' => 'featured',
                'type' => 'true_false',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_6713aedde1572',
                'label' => 'About',
                'name' => 'about',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_6713aeffe1573',
                'label' => 'Quote',
                'name' => 'quote',
                'type' => 'text',
                'instructions' => 'H3 for the heading',
            ),
            array(
                'key' => 'field_6713af27e1574',
                'label' => 'Who should attend/watch',
                'name' => 'who_should_attendwatch',
                'type' => 'text',
                'instructions' => 'H3 for the heading and bullet points',
            ),
            array(
                'key' => 'field_6713af47e1575',
                'label' => 'Event themes',
                'name' => 'event_themes',
                'type' => 'text',
                'instructions' => 'H3 for the heading and bullet points',
            ),
            array(
                'key' => 'field_6713af5ce1576',
                'label' => 'Event overview',
                'name' => 'event_overview',
                'type' => 'text',
                'instructions' => 'H3 for the heading and bullet points',
            ),
            array(
                'key' => 'field_6713af6fe1577',
                'label' => 'Speakers',
                'name' => 'speakers',
                'type' => 'relationship',
                'instructions' => 'Add content about them in Speaker collection',
                'post_type' => array(
                    0 => 'speakers',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                    1 => 'post_type',
                    2 => 'taxonomy',
                ),
                'return_format' => 'id',
                'elements' => '',
            ),
            array(
                'key' => 'field_6713af9ee1578',
                'label' => 'Sponsor heading 1',
                'name' => 'sponsor_heading_1',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6713afb2e1579',
                'label' => 'Sponsors 1',
                'name' => 'sponsors_1',
                'type' => 'relationship',
                'post_type' => array(
                    0 => 'sponsors',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                    1 => 'post_type',
                    2 => 'taxonomy',
                ),
                'return_format' => 'id',
                'elements' => '',
            ),
            array(
                'key' => 'field_6713afcce157a',
                'label' => 'Sponsor heading 2',
                'name' => 'sponsor_heading_2',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6713afe8e157c',
                'label' => 'Sponsors 2',
                'name' => 'sponsors_2',
                'type' => 'relationship',
                'post_type' => array(
                    0 => 'sponsors',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                    1 => 'post_type',
                    2 => 'taxonomy',
                ),
                'return_format' => 'id',
                'elements' => '',
            ),
            array(
                'key' => 'field_6713aff6e157d',
                'label' => 'Sponsor heading 3',
                'name' => 'sponsor_heading_3',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6713affae157e',
                'label' => 'Sponsors 3',
                'name' => 'sponsors_3',
                'type' => 'relationship',
                'post_type' => array(
                    0 => 'sponsors',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                    1 => 'post_type',
                    2 => 'taxonomy',
                ),
                'return_format' => 'id',
                'elements' => '',
            ),
            array(
                'key' => 'field_6713b004e157f',
                'label' => 'Event Images',
                'name' => 'event_images',
                'type' => 'gallery',
                'return_format' => 'array',
                'library' => 'all',
                'insert' => 'append',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_6713b020e1580',
                'label' => 'Event Details',
                'name' => 'event_details',
                'type' => 'wysiwyg',
                'instructions' => 'H2 for heading. Block after speakers',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_6713b03ae1581',
                'label' => 'Map Iframe',
                'name' => 'map_iframe',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_6713b05de1583',
                'label' => 'Recap hero',
                'name' => 'recap_hero',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6713b067e1584',
                'label' => 'Recap video intro',
                'name' => 'recap_video_intro',
                'type' => 'text',
                'instructions' => 'H2 text before the video (center aligned)',
            ),
            array(
                'key' => 'field_6713b078e1585',
                'label' => 'Recap video link',
                'name' => 'recap_video_link',
                'type' => 'oembed',
                'width' => '',
                'height' => '',
            ),
            array(
                'key' => 'field_6713b090e1586',
                'label' => 'Recap text',
                'name' => 'recap_text',
                'type' => 'wysiwyg',
                'instructions' => 'Body copy after video',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'events',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_670fc2101a5c5',
        'title' => 'Footer menu logos',
        'fields' => array(
            array(
                'key' => 'field_670fc21000772',
                'label' => 'Logo',
                'name' => 'logo',
                'type' => 'image',
                'return_format' => 'url',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'nav_menu_item',
                    'operator' => '==',
                    'value' => 'location/footer-social',
                ),
            ),
            array(
                array(
                    'param' => 'nav_menu_item',
                    'operator' => '==',
                    'value' => 'location/footer-logos',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6707c6e00ca82',
        'title' => 'In house ad block',
        'fields' => array(
            array(
                'key' => 'field_6707c695b547f',
                'label' => 'In house ad block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6707c6e5ddb83',
                'label' => 'In House ad',
                'name' => 'in_house_ad',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'in-house-ads',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'return_format' => 'id',
                'allow_null' => 0,
                'bidirectional' => 0,
                'ui' => 1,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_6707c695b5480',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/in-house-ad-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67067bc674c72',
        'title' => 'Industry News options',
        'fields' => array(
            array(
                'key' => 'field_67067bc62deff',
                'label' => 'Company Name',
                'name' => 'company_name',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67067bdb2df00',
                'label' => 'Date',
                'name' => 'date',
                'type' => 'date_picker',
                'display_format' => 'm/d/Y',
                'return_format' => 'm/d/Y',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_67067c282df01',
                'label' => 'Company Link',
                'name' => 'company_link',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67067c322df02',
                'label' => 'Title Tag',
                'name' => 'title_tag',
                'type' => 'text',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'industry-news',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6709144252dd0',
        'title' => 'Inline podcast block',
        'fields' => array(
            array(
                'key' => 'field_6709140c48443',
                'label' => 'Inline podcast block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6709140c48444',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/inline-podcast-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67151f3e5e139',
        'title' => 'Large event block',
        'fields' => array(
            array(
                'key' => 'field_67151ee95e194',
                'label' => 'Large event block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67588a7de031e',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'default_value' => 'Events',
                'allow_in_bindings' => 0,
            ),
            array(
                'key' => 'field_67151f4d8d544',
                'label' => 'Event',
                'name' => 'event',
                'type' => 'post_object',
                'required' => 1,
                'post_type' => array(
                    0 => 'events',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'return_format' => 'object',
                'allow_null' => 0,
                'bidirectional' => 0,
                'ui' => 1,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_675975313b2dc',
                'label' => 'Button copy',
                'name' => 'button_copy',
                'type' => 'text',
                'default_value' => 'Register now',
            ),
            array(
                'key' => 'field_67154c1fe8658',
                'label' => 'Base color',
                'name' => 'base_color',
                'type' => 'color_picker',
                'default_value' => '#C6168D',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
            array(
                'key' => 'field_67154fb8b93b6',
                'label' => 'Shadow color',
                'name' => 'shadow_color',
                'type' => 'color_picker',
                'default_value' => '#F9E8F4',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
            array(
                'key' => 'field_67151ee95e195',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/large-event-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67113ef8de51b',
        'title' => 'News list block',
        'fields' => array(
            array(
                'key' => 'field_67113e6c896d5',
                'label' => 'News list block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67113f3b95d7f',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67113f4395d80',
                'label' => 'Items',
                'name' => 'items',
                'type' => 'clone',
                'clone' => array(
                    0 => 'group_6203e845d6408',
                ),
                'display' => 'seamless',
                'layout' => 'block',
                'prefix_label' => 0,
                'prefix_name' => 0,
            ),
            array(
                'key' => 'field_67117472b6377',
                'label' => 'More',
                'name' => 'more',
                'type' => 'link',
                'return_format' => 'array',
            ),
            array(
                'key' => 'field_67113e6c896d6',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/news-list-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_66e432bd622bf',
        'title' => 'News options',
        'fields' => array(
            array(
                'key' => 'field_670d30ec995d3',
                'label' => 'News Type',
                'name' => 'news_type',
                'type' => 'select',
                'required' => 1,
                'choices' => array(
                    'analysis' => 'Analysis',
                    'explainer' => 'Explainer',
                    'news' => 'News',
                    'feature' => 'Feature',
                    'interview' => 'Interview',
                    'opinion' => 'Opinion',
                    'newsletter' => 'Newsletter',
                    'commentary' => 'Commentary',
                    'podcast' => 'Podcast',
                ),
                'default_value' => 'news',
                'return_format' => 'array',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_670d339600a6a',
                'label' => 'Podcast',
                'name' => 'podcast',
                'type' => 'post_object',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_670d30ec995d3',
                            'operator' => '==',
                            'value' => 'podcast',
                        ),
                    ),
                ),
                'post_type' => array(
                    0 => 'podcasts',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'return_format' => 'object',
                'allow_null' => 1,
                'bidirectional' => 0,
                'ui' => 1,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_670d33dd00a6b',
                'label' => 'Podcast Time',
                'name' => 'podcast_time',
                'type' => 'number',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_670d30ec995d3',
                            'operator' => '==',
                            'value' => 'podcast',
                        ),
                    ),
                ),
                'step' => '',
            ),
            array(
                'key' => 'field_670d341000a6e',
                'label' => 'Exclude Related Reading & Signup',
                'name' => 'exclude_related_reading_signup',
                'type' => 'true_false',
                'ui' => 1,
            ),
            array(
                'key' => 'field_670d347d00a6f',
                'label' => 'Sponsored',
                'name' => 'sponsored',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'side',
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67165a0a4a577',
        'title' => 'News plates block',
        'fields' => array(
            array(
                'key' => 'field_671658983a216',
                'label' => 'News plates block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67165a117d211',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67165a1d7d212',
                'label' => 'Items',
                'name' => 'items',
                'type' => 'clone',
                'clone' => array(
                    0 => 'group_6203e845d6408',
                ),
                'display' => 'seamless',
                'layout' => 'block',
                'prefix_label' => 0,
                'prefix_name' => 0,
            ),
            array(
                'key' => 'field_671658983a217',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/news-plates-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_670d431b98653',
        'title' => 'News podcast options',
        'fields' => array(
            array(
                'key' => 'field_670d431c52a68',
                'label' => 'Podcast embed code',
                'name' => 'podcast_embed_code',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_670d43e152a69',
                'label' => 'Apple Episode Link',
                'name' => 'apple_episode_link',
                'type' => 'text',
            ),
            array(
                'key' => 'field_670d43ec52a6a',
                'label' => 'Spotify Episode Link',
                'name' => 'spotify_episode_link',
                'type' => 'text',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6710ef848d80b',
        'title' => 'News with hero block',
        'fields' => array(
            array(
                'key' => 'field_6710ec24e68e1',
                'label' => 'News with hero block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6710efaf2d652',
                'label' => 'Items',
                'name' => 'items',
                'type' => 'clone',
                'clone' => array(
                    0 => 'group_6203e845d6408',
                ),
                'display' => 'seamless',
                'layout' => 'block',
                'prefix_label' => 0,
                'prefix_name' => 0,
            ),
            array(
                'key' => 'field_672c9bda19dd4',
                'label' => 'Style',
                'name' => 'style',
                'type' => 'select',
                'required' => 1,
                'choices' => array(
                    'hero_right_two' => 'Hero right + two columns',
                    'hero_left_three' => 'Hero left + three columns',
                ),
                'default_value' => 'hero_right_two',
                'return_format' => 'value',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_6710ec24e68e2',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/news-with-hero-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67067acd75e4c',
        'title' => 'Order report options',
        'fields' => array(
            array(
                'key' => 'field_67067acdc3ecf',
                'label' => 'Research Item',
                'name' => 'research_item',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'research',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'return_format' => 'object',
                'allow_null' => 0,
                'bidirectional' => 0,
                'ui' => 1,
                'bidirectional_target' => array(),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'order-reports',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67052ac923566',
        'title' => 'Podcast options',
        'fields' => array(
            array(
                'key' => 'field_67052ac9a2089',
                'label' => 'Audio Logo',
                'name' => 'audio_logo',
                'type' => 'image',
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_67052af4a208a',
                'label' => 'Title Tag',
                'name' => 'title_tag',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67052c23a208b',
                'label' => 'Organization',
                'name' => 'organization',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67052c2ca208d',
                'label' => 'Lead Gen Text',
                'name' => 'lead_gen_text',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67052c31a208e',
                'label' => 'Apple Podcast',
                'name' => 'apple_podcast',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67052c37a208f',
                'label' => 'Spotify',
                'name' => 'spotify',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67052c3ea2090',
                'label' => 'RSS Feed',
                'name' => 'rss_feed',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67052c44a2091',
                'label' => 'X (Twitter)',
                'name' => 'x_twitter',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67052c52a2094',
                'label' => 'LinkedIn',
                'name' => 'linkedin',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67052c57a2095',
                'label' => 'Instagram',
                'name' => 'instagram',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67052c5ca2096',
                'label' => 'Facebook',
                'name' => 'facebook',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67053a41a2097',
                'label' => 'Sample Episodes',
                'name' => 'sample_episodes',
                'type' => 'relationship',
                'post_type' => array(
                    0 => 'post',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                    1 => 'taxonomy',
                ),
                'return_format' => 'object',
                'elements' => '',
            ),
            array(
                'key' => 'field_67053a82a2098',
                'label' => 'Sample Episodes Text',
                'name' => 'sample_episodes_text',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_67053ae3a2099',
                'label' => 'Reviews',
                'name' => 'reviews',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_67053af1a209a',
                        'label' => 'Content',
                        'name' => 'content',
                        'type' => 'wysiwyg',
                        'tabs' => 'all',
                        'toolbar' => 'full',
                        'media_upload' => 1,
                        'delay' => 0,
                        'parent_repeater' => 'field_67053ae3a2099',
                    ),
                ),
            ),
            array(
                'key' => 'field_673d0e9170f0c',
                'label' => 'Sidebar widget',
                'name' => 'sidebar_widget',
                'type' => 'select',
                'choices' => array(),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'podcasts',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6203e845d6408',
        'title' => 'Posts source',
        'fields' => array(
            array(
                'key' => 'field_6203e8678b566',
                'label' => 'Content Type',
                'name' => 'type',
                'type' => 'select',
                'choices' => array(
                    'custom' => 'Custom',
                    'category' => 'Category',
                    'tag' => 'Tag',
                    'type' => 'News Type',
                    'post_type' => 'Post Type',
                ),
                'default_value' => false,
                'allow_null' => 1,
                'ui' => 1,
                'ajax' => 1,
                'return_format' => 'value',
                'allow_custom' => 0,
                'search_placeholder' => '',
            ),
            array(
                'key' => 'field_6203e8a88b567',
                'label' => 'Custom',
                'name' => 'custom',
                'type' => 'relationship',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_6203e8678b566',
                            'operator' => '==',
                            'value' => 'custom',
                        ),
                    ),
                ),
                'post_type' => '',
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                    1 => 'post_type',
                    2 => 'taxonomy',
                ),
                'elements' => array(
                    0 => 'featured_image',
                ),
                'return_format' => 'id',
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_6203e8dc8b568',
                'label' => 'Category',
                'name' => 'category',
                'type' => 'taxonomy',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_6203e8678b566',
                            'operator' => '==',
                            'value' => 'category',
                        ),
                    ),
                ),
                'taxonomy' => 'category',
                'field_type' => 'select',
                'allow_null' => 0,
                'return_format' => 'id',
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_6203e91b8b569',
                'label' => 'Tag',
                'name' => 'tag',
                'type' => 'taxonomy',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_6203e8678b566',
                            'operator' => '==',
                            'value' => 'tag',
                        ),
                    ),
                ),
                'taxonomy' => 'post_tag',
                'field_type' => 'select',
                'allow_null' => 0,
                'return_format' => 'id',
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_620664767b7d0',
                'label' => 'News Type',
                'name' => 'news_type',
                'type' => 'select',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_6203e8678b566',
                            'operator' => '==',
                            'value' => 'type',
                        ),
                    ),
                ),
                'choices' => array(
                    'analysis' => 'Analysis',
                    'explainer' => 'Explainer',
                    'news' => 'News',
                    'feature' => 'Feature',
                    'interview' => 'Interview',
                    'opinion' => 'Opinion',
                    'newsletter' => 'Newsletter',
                    'commentary' => 'Commentary',
                    'podcast' => 'Podcast',
                ),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_6751b0136e346',
                'label' => 'Post type',
                'name' => 'post_type',
                'type' => 'select',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_6203e8678b566',
                            'operator' => '==',
                            'value' => 'post_type',
                        ),
                    ),
                ),
                'choices' => array(
                    'post' => 'Post',
                    'industry-news' => 'Industry News',
                    'resources' => 'Resources',
                    'research' => 'Researches',
                    'events' => 'Events',
                    'podcasts' => 'Podcasts',
                ),
                'default_value' => false,
                'return_format' => 'value',
            ),
            array(
                'key' => 'field_6203e9388b56a',
                'label' => 'Number of items',
                'name' => 'number_of_items',
                'type' => 'number',
                'step' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
        ),
        'style' => 'default',
        'active' => false,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_671624272bba5',
        'title' => 'Related reading block',
        'fields' => array(
            array(
                'key' => 'field_671623d97cb15',
                'label' => 'Related reading block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6716242a27aaf',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'default_value' => 'Related readings',
            ),
            array(
                'key' => 'field_6716242f27ab0',
                'label' => 'Items',
                'name' => 'items',
                'type' => 'clone',
                'clone' => array(
                    0 => 'group_6203e845d6408',
                ),
                'display' => 'seamless',
                'layout' => 'block',
                'prefix_label' => 0,
                'prefix_name' => 0,
            ),
            array(
                'key' => 'field_671623d97cb16',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/related-reading-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67051f4fe33b0',
        'title' => 'Research options',
        'fields' => array(
            array(
                'key' => 'field_67051f6b5e832',
                'label' => 'Subtitle',
                'name' => 'subtitle',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67051f6f5e833',
                'label' => 'Banner',
                'name' => 'banner',
                'type' => 'image',
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_6705263b0ad0a',
                'label' => 'Date',
                'name' => 'date',
                'type' => 'date_picker',
                'display_format' => 'm/d/Y',
                'return_format' => 'm/d/Y',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_670526da0ad0b',
                'label' => 'Download form code',
                'name' => 'download_form_code',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_670527060ad0c',
                'label' => 'Full price',
                'name' => 'full_price',
                'type' => 'text',
            ),
            array(
                'key' => 'field_670527140ad0d',
                'label' => 'Purchase Form Code',
                'name' => 'purchase_form_code',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_670527420ad0e',
                'label' => 'Order Page',
                'name' => 'order_page',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'order-reports',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'return_format' => 'object',
                'allow_null' => 0,
                'bidirectional' => 0,
                'ui' => 1,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_670527c00ad0f',
                'label' => 'HubSpot Payment Link',
                'name' => 'hubspot_payment_link',
                'type' => 'text',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'research',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_670511b565454',
        'title' => 'Resource options',
        'fields' => array(
            array(
                'key' => 'field_670511b519bce',
                'label' => 'Form code',
                'name' => 'form_code',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_670511d919bcf',
                'label' => 'Sponsor',
                'name' => 'sponsor',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'sponsors',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'return_format' => 'id',
                'allow_null' => 0,
                'bidirectional' => 0,
                'ui' => 1,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_673b67455264a',
                'label' => 'Resource type',
                'name' => 'resource_type',
                'type' => 'text',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'resources',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6712436d7624c',
        'title' => 'Sidebar editors picks block',
        'fields' => array(
            array(
                'key' => 'field_67124217e5cb1',
                'label' => 'Sidebar editors picks block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_671246ab2f068',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67124af32f069',
                'label' => 'Items',
                'name' => 'items',
                'type' => 'clone',
                'clone' => array(
                    0 => 'group_6203e845d6408',
                ),
                'display' => 'seamless',
                'layout' => 'block',
                'prefix_label' => 0,
                'prefix_name' => 0,
            ),
            array(
                'key' => 'field_67124217e5cb2',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/sidebar-editors-picks-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6712436e13ec8',
        'title' => 'Sidebar form block',
        'fields' => array(
            array(
                'key' => 'field_671242f157f41',
                'label' => 'Sidebar form block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67124adad8e73',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6712468a7aebd',
                'label' => 'Form code',
                'name' => 'form_code',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_671242f157f42',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/sidebar-form-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6712436eb2a11',
        'title' => 'Sidebar news list block',
        'fields' => array(
            array(
                'key' => 'field_6712430b5f1d5',
                'label' => 'Sidebar news list block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_671243a42e29a',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_671243b02e29b',
                'label' => 'Items',
                'name' => 'items',
                'type' => 'clone',
                'clone' => array(
                    0 => 'group_6203e845d6408',
                ),
                'display' => 'seamless',
                'layout' => 'block',
                'prefix_label' => 0,
                'prefix_name' => 0,
            ),
            array(
                'key' => 'field_671295457d8bb',
                'label' => 'Ad banner',
                'name' => 'ad_banner',
                'type' => 'clone',
                'clone' => array(
                    0 => 'group_67475993cb90c',
                ),
                'display' => 'seamless',
                'layout' => 'block',
                'prefix_label' => 0,
                'prefix_name' => 0,
            ),
            array(
                'key' => 'field_671243c22e29c',
                'label' => 'More',
                'name' => 'more',
                'type' => 'link',
                'return_format' => 'array',
            ),
            array(
                'key' => 'field_6712430b5f1d6',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/sidebar-news-list-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6718f8b8a64e0',
        'title' => 'Signup form block',
        'fields' => array(
            array(
                'key' => 'field_6707ca1167941',
                'label' => 'Signup form block',
                'name' => '',
                'type' => 'message',
                'message' => 'Newsletter signup form, display only on mobile view.
Display condition based on settings in sidebar <b>News options -> Exclude Related Reading & Signup</b>',
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/signup-form-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67050ea568064',
        'title' => 'Speaker options',
        'fields' => array(
            array(
                'key' => 'field_67050ea565c46',
                'label' => 'Job Title',
                'name' => 'job_title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67050ec665c47',
                'label' => 'Company',
                'name' => 'company',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67050ecd65c48',
                'label' => 'Linkedin Link',
                'name' => 'linkedin_link',
                'type' => 'text',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'speakers',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67050dbc6adca',
        'title' => 'Sponsor options',
        'fields' => array(
            array(
                'key' => 'field_67050dbc8dbc7',
                'label' => 'Website Link',
                'name' => 'website_link',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67050dd48dbc8',
                'label' => 'Sponsorship Level',
                'name' => 'sponsorship_level',
                'type' => 'text',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'sponsors',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_670698f9d8892',
        'title' => 'Spotlight quote block',
        'fields' => array(
            array(
                'key' => 'field_67069833ec941',
                'label' => 'Spotlight quote block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6706990eeed8e',
                'label' => 'Copy',
                'name' => 'copy',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_67069930eed8f',
                'label' => 'Name',
                'name' => 'name',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67069833ec942',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/spotlight-quote-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67050adcdfbbe',
        'title' => 'Team member options',
        'fields' => array(
            array(
                'key' => 'field_67050add1481b',
                'label' => 'Author',
                'name' => 'author',
                'type' => 'taxonomy',
                'taxonomy' => 'author',
                'return_format' => 'id',
                'field_type' => 'select',
                'allow_null' => 1,
            ),
            array(
                'key' => 'field_67050b0d1481c',
                'label' => 'Job Title',
                'name' => 'job_title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67050b311481e',
                'label' => 'Linkedin',
                'name' => 'linkedin',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67050b391481f',
                'label' => 'X (Twitter)',
                'name' => 'x_twitter',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67050b4214820',
                'label' => 'Other Socials',
                'name' => 'other_socials',
                'type' => 'text',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'team',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_670d530988b6c',
        'title' => 'Test editable block',
        'fields' => array(
            array(
                'key' => 'field_670d5309e8f32',
                'label' => 'block_title',
                'name' => 'block_title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_670d5317e8f33',
                'label' => 'block_content',
                'name' => 'block_content',
                'type' => 'text',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/my-editable-preview-block',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67129adc49add',
        'title' => 'Large podcasts block',
        'fields' => array(
            array(
                'key' => 'field_67129ab748d4b',
                'label' => 'Large podcasts block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67129c0248290',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67129ae164d36',
                'label' => 'Columns',
                'name' => 'columns',
                'type' => 'relationship',
                'required' => 1,
                'post_type' => array(
                    0 => 'podcasts',
                ),
                'post_status' => array(
                    0 => 'publish',
                ),
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                    1 => 'taxonomy',
                ),
                'return_format' => 'id',
                'elements' => '',
            ),
            array(
                'key' => 'field_67129c1548291',
                'label' => 'Number of items',
                'name' => 'number_of_items',
                'type' => 'number',
                'default_value' => 3,
                'step' => '',
            ),
            array(
                'key' => 'field_67129ab748d4c',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/large-podcasts-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_670e7f33eb14c',
        'title' => 'Top menu options',
        'fields' => array(
            array(
                'key' => 'field_670e7f347cb6d',
                'label' => 'Color',
                'name' => 'color',
                'type' => 'color_picker',
                'default_value' => '#C6168D',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'nav_menu_item',
                    'operator' => '==',
                    'value' => 'location/top',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_672ca977749f0',
        'title' => 'Subscribe form block',
        'fields' => array(
            array(
                'key' => 'field_672ca9403c776',
                'label' => 'Subscribe form block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_672ca97f40920',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'default_value' => 'Get Latitude Media in your inbox',
            ),
            array(
                'key' => 'field_672ca9a740921',
                'label' => 'Form code',
                'name' => 'form_code',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_672ca9b740922',
                'label' => 'Description',
                'name' => 'description',
                'type' => 'textarea',
                'default_value' => 'Subscribe to Latitude\'s free newsletters today to receive the latest news on the energy transition:',
                'rows' => 4,
            ),
            array(
                'key' => 'field_672ca9403c777',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/subscribe-form-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));

    acf_add_local_field_group(array(
        'key' => 'field_672ccb402a557',
        'title' => 'Categories section block',
        'fields' => array(
            array(
                'key' => 'field_672ccb402a559',
                'label' => 'Categories section block',
                'name' => '',
                'type' => 'message',
            ),
            array(
                'key' => 'field_672ccb402a55a',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/categories-section-block',
                ),
            ),
        ),
        'style' => 'seamless'
    ));

    acf_add_local_field_group(array(
        'key' => 'field_672ccb5048a72',
        'title' => 'News list with hero section block',
        'fields' => array(
            array(
                'key' => 'field_672ccb5048a73',
                'label' => 'News list with hero section block',
                'name' => '',
                'type' => 'message',
            ),
            array(
                'key' => 'field_672ccb5048a74',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/news-list-with-hero-section-block',
                ),
            ),
        ),
        'style' => 'seamless'
    ));

    acf_add_local_field_group(array(
        'key' => 'group_672cccd9f3aa9',
        'title' => 'News with sidebar section block',
        'fields' => array(
            array(
                'key' => 'field_672ccb6767b8d',
                'label' => 'News with sidebar section block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_672ccce38fc0e',
                'label' => 'Sidebar widget',
                'name' => 'sidebar_widget',
                'type' => 'select',
                'choices' => array(),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_672ccb6767b8e',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/news-with-sidebar-section-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6733cc57c813b',
        'title' => 'Authors list block',
        'fields' => array(
            array(
                'key' => 'field_6733cbf3864f3',
                'label' => 'Authors list block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6733cc62a98f1',
                'label' => 'Authors type',
                'name' => 'authors_type',
                'type' => 'select',
                'choices' => array(
                    'contributor' => 'Contributors',
                    'podcast-host' => 'Podcast Hosts',
                    'latitude-media-staff' => 'Latitude Media Staff',
                    'custom' => 'Custom',
                ),
                'default_value' => 'latitude-media-staff',
                'return_format' => 'array',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_6734c4eca9ba2',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_6733cc62a98f1',
                            'operator' => '==',
                            'value' => 'custom',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_6734c4a6a9ba1',
                'label' => 'Custom',
                'name' => 'custom',
                'type' => 'taxonomy',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_6733cc62a98f1',
                            'operator' => '==',
                            'value' => 'custom',
                        ),
                    ),
                ),
                'taxonomy' => 'author',
                'return_format' => 'id',
                'field_type' => 'multi_select',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_6733cbf3864f4',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/authors-list-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6735511bb3a71',
        'title' => 'Research banner block',
        'fields' => array(
            array(
                'key' => 'field_67354ddb2f440',
                'label' => 'Research banner block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67354ddb2f441',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/research-banner-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67355185aa67f',
        'title' => 'Research overview block',
        'fields' => array(
            array(
                'key' => 'field_67354f7fc926b',
                'label' => 'Research overview block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6735519ce2d00',
                'label' => 'Content',
                'name' => 'content',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_67354f7fc926c',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/research-overview-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6735515080ec9',
        'title' => 'Image and text',
        'fields' => array(
            array(
                'key' => 'field_67354f9994827',
                'label' => 'Image and text',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6735515f7ffa2',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_673551687ffa3',
                'label' => 'Logo',
                'name' => 'logo',
                'type' => 'image',
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_674f062c18efb',
                'label' => 'Image link',
                'name' => 'image_link',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67362db82424e',
                'label' => 'Base color',
                'name' => 'base_color',
                'type' => 'color_picker',
                'default_value' => '#C6168D',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
            array(
                'key' => 'field_6745b76159f4c',
                'label' => 'Shadow color',
                'name' => 'shadow_color',
                'type' => 'color_picker',
                'default_value' => '#F9E8F4',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
            array(
                'key' => 'field_67354f9994828',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/image-and-text',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_673551451512b',
        'title' => 'Research preview block',
        'fields' => array(
            array(
                'key' => 'field_67354de6440d1',
                'label' => 'Research preview block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67354de6440d2',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/research-preview-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'field_67363a22299d4',
        'title' => 'Content with background block',
        'fields' => array(
            array(
                'key' => 'field_67363a22299d6',
                'label' => 'Content with background block',
                'name' => '',
                'type' => 'message',
            ),
            array(
                'key' => 'field_67363a22299d7',
                'label' => 'Background color',
                'name' => 'background_color',
                'type' => 'color_picker',
                'default_value' => '#F5F5F5',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/content-with-background-block',
                ),
            ),
        ),
        'style' => 'seamless'
    ));
    acf_add_local_field_group(array(
        'key' => 'group_672b7b7ed1f8d',
        'title' => 'Section lending options',
        'fields' => array(
            array(
                'key' => 'field_672b7b7f0ccda',
                'label' => 'Section type',
                'name' => 'section_type',
                'type' => 'taxonomy',
                'required' => 1,
                'taxonomy' => 'section',
                'return_format' => 'object',
                'field_type' => 'select',
                'allow_null' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'sections-landing',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'side',
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67353484a9df6',
        'title' => 'Featured research block',
        'fields' => array(
            array(
                'key' => 'field_6734df53622f9',
                'label' => 'Featured research block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6735348a189ac',
                'label' => 'Research',
                'name' => 'research',
                'type' => 'post_object',
                'required' => 1,
                'post_type' => array(
                    0 => 'research',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'return_format' => 'object',
                'allow_null' => 0,
                'bidirectional' => 0,
                'ui' => 1,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_67353704bca28',
                'label' => 'Description',
                'name' => 'description',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_6734df53622fa',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/featured-research-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67377ca62f1bb',
        'title' => 'Standard Page options',
        'fields' => array(
            array(
                'key' => 'field_67377ca665b8c',
                'label' => 'Hide title',
                'name' => 'hide_title',
                'type' => 'true_false',
                'ui' => 1,
            ),
            array(
                'key' => 'field_6747457ef2e54',
                'label' => 'Disable top menu',
                'name' => 'disable_top_menu',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'side',
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'field_6739e4e7caab9',
        'title' => 'Order preview block',
        'fields' => array(
            array(
                'key' => 'field_6739e4e7caabb',
                'label' => 'Order preview block',
                'name' => '',
                'type' => 'message',
            ),
            array(
                'key' => 'field_6739e4e7caabc',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/order-preview-block',
                ),
            ),
        ),
        'style' => 'seamless'
    ));

    acf_add_local_field_group(array(
        'key' => 'field_6739e5a2bd5bb',
        'title' => 'Order form block',
        'fields' => array(
            array(
                'key' => 'field_6739e5a2bd5bd',
                'label' => 'Order form block',
                'name' => '',
                'type' => 'message',
            ),
            array(
                'key' => 'field_6739e5a2bd5be',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/order-form-block',
                ),
            ),
        ),
        'style' => 'seamless'
    ));

    acf_add_local_field_group(array(
        'key' => 'field_673b3369c9f5e',
        'title' => 'Styled button block',
        'fields' => array(
            array(
                'key' => 'field_673b3369c9f5f',
                'label' => 'Styled button block',
                'name' => '',
                'type' => 'message',
            ),
            array(
                'key' => 'field_673b3369c9f60',
                'label' => 'Button',
                'name' => 'button',
                'type' => 'link',
                'return_format' => 'array',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/styled-button-block',
                ),
            ),
        ),
        'style' => 'seamless'
    ));
    acf_add_local_field_group(array(
        'key' => 'group_673d1350d0368',
        'title' => 'Sidebar info block',
        'fields' => array(
            array(
                'key' => 'field_673d1219a4134',
                'label' => 'Sidebar info block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_673d1355a8150',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_673d135ba8151',
                'label' => 'Button',
                'name' => 'button',
                'type' => 'link',
                'return_format' => 'array',
            ),
            array(
                'key' => 'field_673d1219a4135',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/sidebar-info-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_673dddf252f46',
        'title' => 'Info cta block',
        'fields' => array(
            array(
                'key' => 'field_673dda0a1e7b6',
                'label' => 'Info cta block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_674484b91c106',
                'label' => 'Base color',
                'name' => 'base_color',
                'type' => 'color_picker',
                'default_value' => '#00B48D',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
            array(
                'key' => 'field_674484c41c107',
                'label' => 'Shadow color',
                'name' => 'shadow_color',
                'type' => 'color_picker',
                'default_value' => '#CCF0E8',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
            array(
                'key' => 'field_673dda0a1e7b7',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/info-cta-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_673dbb6f2ff61',
        'title' => 'Partner podcasts block',
        'fields' => array(
            array(
                'key' => 'field_673db9fd12133',
                'label' => 'Partner podcasts block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_673dbb7530c1a',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_673dbb7b30c1b',
                'label' => 'Description',
                'name' => 'description',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_673dbb8930c1c',
                'label' => 'Podcasts',
                'name' => 'podcasts',
                'type' => 'repeater',
                'layout' => 'row',
                'button_label' => 'Add Row',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_676155316e9f8',
                        'label' => 'Podcast',
                        'name' => '',
                        'type' => 'accordion',
                        'open' => 1,
                        'multi_expand' => 0,
                        'endpoint' => 0,
                        'parent_repeater' => 'field_673dbb8930c1c',
                    ),
                    array(
                        'key' => 'field_676154d66e9f2',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'array',
                        'library' => 'all',
                        'preview_size' => 'medium',
                        'parent_repeater' => 'field_673dbb8930c1c',
                    ),
                    array(
                        'key' => 'field_676154e46e9f3',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'parent_repeater' => 'field_673dbb8930c1c',
                    ),
                    array(
                        'key' => 'field_676154e86e9f4',
                        'label' => 'Company',
                        'name' => 'company',
                        'type' => 'text',
                        'parent_repeater' => 'field_673dbb8930c1c',
                    ),
                    array(
                        'key' => 'field_676154ef6e9f5',
                        'label' => 'Description',
                        'name' => 'description',
                        'type' => 'textarea',
                        'rows' => 3,
                        'parent_repeater' => 'field_673dbb8930c1c',
                    ),
                    array(
                        'key' => 'field_676155156e9f6',
                        'label' => 'Apple link',
                        'name' => 'apple_link',
                        'type' => 'text',
                        'allow_in_bindings' => 0,
                        'parent_repeater' => 'field_673dbb8930c1c',
                    ),
                    array(
                        'key' => 'field_6761551b6e9f7',
                        'label' => 'Spotify link',
                        'name' => 'spotify_link',
                        'type' => 'text',
                        'allow_in_bindings' => 0,
                        'parent_repeater' => 'field_673dbb8930c1c',
                    ),
                ),
            ),
            array(
                'key' => 'field_673db9fd12134',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/partner-porcasts-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_673f9cef9b6de',
        'title' => 'Events list block',
        'fields' => array(
            array(
                'key' => 'field_673f9b6dd2721',
                'label' => 'Events list block',
                'name' => '',
                'type' => 'message',
                'message' => 'Will pulled automatically by event <b>End date</b>.
Or select event manually.',
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            ),
            array(
                'key' => 'field_673faa95e2af4',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'default_value' => 'Upcoming events',
            ),
            array(
                'key' => 'field_673fad446ef02',
                'label' => 'Type',
                'name' => 'type',
                'type' => 'select',
                'choices' => array(
                    'upcoming' => 'Upcoming events',
                    'past' => 'Past events',
                    'custom' => 'Custom',
                ),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_673f9cf3cdb8e',
                'label' => 'Events',
                'name' => 'events',
                'type' => 'relationship',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_673fad446ef02',
                            'operator' => '==',
                            'value' => 'custom',
                        ),
                    ),
                ),
                'post_type' => array(
                    0 => 'events',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                ),
                'return_format' => 'id',
                'elements' => '',
            ),
            array(
                'key' => 'field_673f9b6dd2722',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/events-list-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_674595857108f',
        'title' => 'Event description block',
        'fields' => array(
            array(
                'key' => 'field_674481518f9b4',
                'label' => 'Event description block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_674595b6064e9',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_674481518f9b5',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-description-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_674597e182742',
        'title' => 'Event speakers block',
        'fields' => array(
            array(
                'key' => 'field_67449c4ecdcd9',
                'label' => 'Event speakers block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_674597fdb07ad',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67459804b07ae',
                'label' => 'Speakers',
                'name' => 'speakers',
                'type' => 'relationship',
                'post_type' => array(
                    0 => 'speakers',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                ),
                'return_format' => 'id',
                'elements' => '',
            ),
            array(
                'key' => 'field_67449c4ecdcda',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-speakers-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'field_6745ca5e34889',
        'title' => 'Event short description block',
        'fields' => array(
            array(
                'key' => 'field_6745ca5e3488b',
                'label' => 'Event short description block',
                'name' => '',
                'type' => 'message',
                'message' => 'Display the text from the post Excerpt field.',
            ),
            array(
                'key' => 'field_6745ca5e3488c',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-short-description-block',
                ),
            ),
        ),
        'style' => 'seamless'
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6745c3ee06935',
        'title' => 'Event venue block',
        'fields' => array(
            array(
                'key' => 'field_6744a68e8404a',
                'label' => 'Event venue block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6745c3f2c517f',
                'label' => 'Embed code',
                'name' => 'embed_code',
                'type' => 'textarea',
                'rows' => '',
            ),
            array(
                'key' => 'field_6745c7fcbdafe',
                'label' => 'Location details',
                'name' => 'location_details',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_6744a68e8404b',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-venue-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6745aa44544dc',
        'title' => 'Event sponsors block',
        'fields' => array(
            array(
                'key' => 'field_67449c54b1bf0',
                'label' => 'Event sponsors block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6745aa4b00418',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6745aa6b00419',
                'label' => 'Sponsors category',
                'name' => 'sponsors_category',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_6745ad27e802f',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'parent_repeater' => 'field_6745aa6b00419',
                    ),
                    array(
                        'key' => 'field_6745ad2ee8030',
                        'label' => 'Sponsors',
                        'name' => 'sponsors',
                        'type' => 'relationship',
                        'post_type' => array(
                            0 => 'sponsors',
                        ),
                        'post_status' => '',
                        'taxonomy' => '',
                        'filters' => array(
                            0 => 'search',
                        ),
                        'return_format' => 'id',
                        'elements' => '',
                        'parent_repeater' => 'field_6745aa6b00419',
                    ),
                ),
            ),
            array(
                'key' => 'field_67449c54b1bf1',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-sponsors-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6745d58b04065',
        'title' => 'Event agenda block',
        'fields' => array(
            array(
                'key' => 'field_6744a1d23baa4',
                'label' => 'Event agenda block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6745d5914809b',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6745d9e1987af',
                'label' => 'Schedule',
                'name' => 'schedule',
                'type' => 'repeater',
                'layout' => 'row',
                'sub_fields' => array(
                    array(
                        'key' => 'field_6745e4dd03a8e',
                        'label' => 'Schedule item',
                        'name' => '',
                        'type' => 'accordion',
                        'open' => 1,
                        'multi_expand' => 0,
                        'endpoint' => 0,
                        'parent_repeater' => 'field_6745d9e1987af',
                    ),
                    array(
                        'key' => 'field_6745e334c6af8',
                        'label' => 'General',
                        'name' => '',
                        'type' => 'tab',
                        'placement' => 'top',
                        'endpoint' => 0,
                        'parent_repeater' => 'field_6745d9e1987af',
                    ),
                    array(
                        'key' => 'field_6745de12af52d',
                        'label' => 'Time',
                        'name' => 'time',
                        'type' => 'text',
                        'wrapper' => array(
                            'width' => '30',
                            'class' => '',
                            'id' => '',
                        ),
                        'parent_repeater' => 'field_6745d9e1987af',
                    ),
                    array(
                        'key' => 'field_6745de0caf52c',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'parent_repeater' => 'field_6745d9e1987af',
                    ),
                    array(
                        'key' => 'field_6745e13b47d00',
                        'label' => 'Details',
                        'name' => '',
                        'type' => 'tab',
                        'placement' => 'top',
                        'endpoint' => 0,
                        'parent_repeater' => 'field_6745d9e1987af',
                    ),
                    array(
                        'key' => 'field_6745de97eafa4',
                        'label' => 'Description',
                        'name' => 'description',
                        'type' => 'textarea',
                        'rows' => 3,
                        'parent_repeater' => 'field_6745d9e1987af',
                    ),
                    array(
                        'key' => 'field_6745ded1af1a3',
                        'label' => 'Speakers',
                        'name' => 'speakers',
                        'type' => 'post_object',
                        'post_type' => array(
                            0 => 'speakers',
                        ),
                        'post_status' => '',
                        'taxonomy' => '',
                        'return_format' => 'id',
                        'multiple' => 1,
                        'allow_null' => 0,
                        'bidirectional' => 0,
                        'ui' => 1,
                        'bidirectional_target' => array(),
                        'parent_repeater' => 'field_6745d9e1987af',
                    ),
                    array(
                        'key' => 'field_6745def4af1a4',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'id',
                        'library' => 'all',
                        'preview_size' => 'medium',
                        'parent_repeater' => 'field_6745d9e1987af',
                    ),
                ),
            ),
            array(
                'key' => 'field_6744a1d23baa5',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-agenda-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6745f79613c6a',
        'title' => 'Event gray icon block',
        'fields' => array(
            array(
                'key' => 'field_6745ed84d47f7',
                'label' => 'Event gray icon block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6746402d35366',
                'label' => 'Settings',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_6745f79a85ad8',
                'label' => 'Type',
                'name' => 'type',
                'type' => 'select',
                'choices' => array(
                    'default' => 'Default (one column)',
                    'type2' => 'Type 2 (two columns)',
                ),
                'default_value' => 'default',
                'return_format' => 'value',
                'allow_null' => 0,
                'ui' => 1,
                'ajax' => 0,
            ),
            array(
                'key' => 'field_6745ed84d47f8',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
            array(
                'key' => 'field_67463f2a9524f',
                'label' => 'Column one',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_6745f7bf729c4',
                'label' => 'Logo',
                'name' => 'logo',
                'type' => 'image',
                'return_format' => 'id',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_67463cd4b8eea',
                'label' => 'Content',
                'name' => 'content',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_67463f139524e',
                'label' => 'Column two',
                'name' => '',
                'type' => 'tab',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_6745f79a85ad8',
                            'operator' => '==',
                            'value' => 'type2',
                        ),
                    ),
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_6745f7d9729c5',
                'label' => 'Logo',
                'name' => 'logo_2',
                'type' => 'image',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_6745f79a85ad8',
                            'operator' => '==',
                            'value' => 'type2',
                        ),
                    ),
                ),
                'return_format' => 'id',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_67463d457c479',
                'label' => 'Content',
                'name' => 'content_2',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-gray-icon-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_674659fc35983',
        'title' => 'Event about sponsors block',
        'fields' => array(
            array(
                'key' => 'field_674657aa30fb3',
                'label' => 'Event about sponsors block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67465c2859b48',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67465c3459b49',
                'label' => 'Sponsors',
                'name' => 'sponsors',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_67465c5c59b4c',
                        'label' => 'Sponsor data',
                        'name' => '',
                        'type' => 'accordion',
                        'open' => 0,
                        'multi_expand' => 0,
                        'endpoint' => 0,
                        'parent_repeater' => 'field_67465c3459b49',
                    ),
                    array(
                        'key' => 'field_67465c3e59b4a',
                        'label' => 'Logo',
                        'name' => 'logo',
                        'type' => 'image',
                        'return_format' => 'id',
                        'library' => 'all',
                        'preview_size' => 'medium',
                        'parent_repeater' => 'field_67465c3459b49',
                    ),
                    array(
                        'key' => 'field_67465c5259b4b',
                        'label' => 'Description',
                        'name' => 'description',
                        'type' => 'wysiwyg',
                        'tabs' => 'all',
                        'toolbar' => 'full',
                        'media_upload' => 1,
                        'delay' => 0,
                        'parent_repeater' => 'field_67465c3459b49',
                    ),
                ),
            ),
            array(
                'key' => 'field_674657aa30fb4',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-about-sponsors-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_674659fcd578b',
        'title' => 'Event partners block',
        'fields' => array(
            array(
                'key' => 'field_6746576f2ae1c',
                'label' => 'Event partners block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67465bd00137b',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67465be50137c',
                'label' => 'Logos',
                'name' => 'logos',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_67465bf20137d',
                        'label' => 'Partner logo',
                        'name' => 'partner_logo',
                        'type' => 'image',
                        'return_format' => 'id',
                        'library' => 'all',
                        'preview_size' => 'medium',
                        'parent_repeater' => 'field_67465be50137c',
                    ),
                ),
            ),
            array(
                'key' => 'field_6746576f2ae1d',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-partners-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67067e55559d3',
        'title' => 'In House Ad options',
        'fields' => array(
            array(
                'key' => 'field_67067e551fe50',
                'label' => 'Type',
                'name' => 'type',
                'type' => 'select',
                'choices' => array(
                    'Event' => 'Event',
                    'Custom' => 'Custom',
                    'Research' => 'Research',
                    'News' => 'News',
                ),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_67067ebc1fe51',
                'label' => 'Banner Text',
                'name' => 'banner_text',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67067ec21fe52',
                'label' => 'Layout',
                'name' => 'layout',
                'type' => 'select',
                'choices' => array(
                    'Text Right' => 'Text Right',
                    'Text Left' => 'Text Left',
                ),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_67067f191fe55',
                'label' => 'Background Image',
                'name' => 'background_image',
                'type' => 'image',
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_67067ef11fe53',
                'label' => 'Additional Image',
                'name' => 'additional_image',
                'type' => 'image',
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_67067f081fe54',
                'label' => 'Heading',
                'name' => 'heading',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67067f3c1fe58',
                'label' => 'Button',
                'name' => 'button',
                'type' => 'link',
                'return_format' => 'array',
            ),
            array(
                'key' => 'field_673df311373a1',
                'label' => 'Base color',
                'name' => 'base_color',
                'type' => 'color_picker',
                'default_value' => '#0095DA',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
            array(
                'key' => 'field_673df342373a2',
                'label' => 'Shadow color',
                'name' => 'shadow_color',
                'type' => 'color_picker',
                'default_value' => '#E5F4FC',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'in-house-ads',
                ),
            ),
        ),
        'style' => 'default',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6712991b1d86e',
        'title' => 'Ad banner block',
        'fields' => array(
            array(
                'key' => 'field_671298a6ae567',
                'label' => 'Ad banner block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67475a59d0aef',
                'label' => 'Ad banner',
                'name' => 'ad_banner',
                'type' => 'clone',
                'clone' => array(
                    0 => 'group_67475993cb90c',
                ),
                'display' => 'seamless',
                'layout' => 'block',
                'prefix_label' => 0,
                'prefix_name' => 0,
            ),
            array(
                'key' => 'field_671298a6ae568',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/ad-banner-section',
                ),
            ),
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/sidebar-ad-banner-section',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_66698c8216cf5',
        'title' => 'DFP Ads Settings',
        'fields' => array(
            array(
                'key' => 'field_66698c85f9bf2',
                'label' => 'DFP Ad Slots',
                'name' => 'dfp_ad_slots',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_66698c95f9bf3',
                        'label' => 'Slot',
                        'name' => 'slot',
                        'type' => 'repeater',
                        'layout' => 'row',
                        'button_label' => 'Add Slot',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_6746ed326d045',
                                'label' => 'Name',
                                'name' => 'name',
                                'aria-label' => '',
                                'type' => 'text',
                                'required' => 1,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'parent_repeater' => 'field_66698c95f9bf3',
                            ),
                            array(
                                'key' => 'field_67470472f37ec',
                                'label' => 'Ad Settings',
                                'name' => '',
                                'aria-label' => '',
                                'type' => 'accordion',
                                'required' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'open' => 0,
                                'multi_expand' => 0,
                                'endpoint' => 0,
                                'parent_repeater' => 'field_66698c95f9bf3',
                            ),
                            array(
                                'key' => 'field_66698c9df9bf4',
                                'label' => 'Spot ID',
                                'name' => 'spot_id',
                                'aria-label' => '',
                                'type' => 'text',
                                'required' => 1,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'parent_repeater' => 'field_66698c95f9bf3',
                            ),
                            array(
                                'key' => 'field_66698cbcf9bf5',
                                'label' => 'DFP Path',
                                'name' => 'dfp_path',
                                'aria-label' => '',
                                'type' => 'text',
                                'required' => 1,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'parent_repeater' => 'field_66698c95f9bf3',
                            ),
                            array(
                                'key' => 'field_666996c213e8d',
                                'label' => 'Size mapping',
                                'name' => 'size_mapping',
                                'aria-label' => '',
                                'type' => 'repeater',
                                'required' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'layout' => 'table',
                                'min' => 0,
                                'max' => 0,
                                'collapsed' => '',
                                'button_label' => 'Add Size',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_666997ab76769',
                                        'label' => 'Ad size',
                                        'name' => 'ad_size_dynamic',
                                        'aria-label' => '',
                                        'type' => 'select',
                                        'required' => 0,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'choices' => array(
                                            '970x90' => '970x90',
                                            '300x250' => '300x250',
                                        ),
                                        'default_value' => array(),
                                        'return_format' => 'value',
                                        'multiple' => 1,
                                        'allow_null' => 1,
                                        'ui' => 1,
                                        'ajax' => 0,
                                        'parent_repeater' => 'field_666996c213e8d',
                                    ),
                                    array(
                                        'key' => 'field_6669980a7676a',
                                        'label' => 'Screen size',
                                        'name' => 'screen_size',
                                        'aria-label' => '',
                                        'type' => 'select',
                                        'required' => 0,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'choices' => array(
                                            '992x1' => 'Desktop and up (992+)',
                                            '768x1' => 'Tablet and up (768+)',
                                            '1x1' => 'Mobile and up (1+)',
                                        ),
                                        'default_value' => false,
                                        'return_format' => 'value',
                                        'allow_null' => 1,
                                        'ui' => 1,
                                        'ajax' => 0,
                                        'parent_repeater' => 'field_666996c213e8d',
                                    ),
                                ),
                                'parent_repeater' => 'field_66698c95f9bf3',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_6669962ace07a',
                'label' => 'DFP Ad Sizes',
                'name' => 'dfp_ad_sizes',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Size',
                'sub_fields' => array(
                    array(
                        'key' => 'field_66699665ce07b',
                        'label' => 'Width',
                        'name' => 'width',
                        'type' => 'number',
                        'step' => '',
                        'parent_repeater' => 'field_6669962ace07a',
                    ),
                    array(
                        'key' => 'field_66699671ce07c',
                        'label' => 'Height',
                        'name' => 'height',
                        'type' => 'number',
                        'step' => '',
                        'parent_repeater' => 'field_6669962ace07a',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options-dfp-ad-slots',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67473ac305a05',
        'title' => 'Event preview block',
        'fields' => array(
            array(
                'key' => 'field_67447f65a31a6',
                'label' => 'Event preview block',
                'name' => '',
                'type' => 'message',
                'message' => 'Display event general information: <ul><li>Date</li><li>Type</li><li>Series</li><li>Title</li><li>Location</li><li>Register link</li></ul> From <b>General event options</b> in sidebar',
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            ),
            array(
                'key' => 'field_67473ac9a1f7c',
                'label' => 'Rows',
                'name' => 'rows',
                'type' => 'checkbox',
                'choices' => array(
                    'date' => 'Date',
                    'type' => 'Type',
                    'series' => 'Series',
                    'title' => 'title',
                    'location' => 'Location',
                    'button' => 'Button',
                ),
                'default_value' => array(
                    0 => 'date',
                    1 => 'type',
                    2 => 'series',
                    3 => 'title',
                    4 => 'location',
                    5 => 'button',
                ),
                'return_format' => 'value',
                'allow_custom' => 0,
                'layout' => 'horizontal',
                'toggle' => 0,
                'save_custom' => 0,
                'custom_choice_button_text' => 'Add new choice',
            ),
            array(
                'key' => 'field_67473b75a1f7d',
                'label' => 'Subtitle',
                'name' => 'subtitle',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67473b7ea1f7e',
                'label' => 'Co-hosted logo',
                'name' => 'co_hosted_logo',
                'type' => 'image',
                'return_format' => 'id',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_67447f65a31a7',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/event-preview-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67475993cb90c',
        'title' => 'Google Ad banner',
        'fields' => array(
            array(
                'key' => 'field_6747599474bb6',
                'label' => 'Ad banner',
                'name' => 'dynamic_ad_banner',
                'type' => 'select',
                'choices' => array(),
                'default_value' => false,
                'return_format' => 'value',
                'allow_null' => 1,
            ),
            array(
                'key' => 'field_674759f174bb7',
                'label' => 'Screen type',
                'name' => 'screen_type',
                'type' => 'radio',
                'choices' => array(
                    'mobile' => 'Mobile only',
                    'desktop' => 'Desktop only',
                ),
                'return_format' => 'value',
                'allow_null' => 1,
                'other_choice' => 0,
                'layout' => 'horizontal',
                'save_other_choice' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
        ),
        'style' => 'default',
        'active' => false,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67487d49f095a',
        'title' => 'Page hero block',
        'fields' => array(
            array(
                'key' => 'field_674746bba069c',
                'label' => 'Page hero block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67487eac69707',
                'label' => 'Background',
                'name' => 'background',
                'type' => 'image',
                'return_format' => 'id',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_67487d4f6c2ac',
                'label' => 'Logo',
                'name' => 'logo',
                'type' => 'image',
                'return_format' => 'id',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_67487d5b6c2ad',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67487d616c2ae',
                'label' => 'Link',
                'name' => 'link',
                'type' => 'link',
                'return_format' => 'array',
            ),
            array(
                'key' => 'field_675ade71d9ed5',
                'label' => 'Link color',
                'name' => 'link_color',
                'type' => 'color_picker',
                'default_value' => '#C6168D',
                'enable_opacity' => 0,
                'return_format' => 'string',
                'allow_in_bindings' => 0,
            ),
            array(
                'key' => 'field_674746bba069d',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/page-hero-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6749ca3d230d6',
        'title' => 'Our approach block',
        'fields' => array(
            array(
                'key' => 'field_6749b7b0370d2',
                'label' => 'Our approach block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6749caa752b85',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6749caac52b86',
                'label' => 'Copy',
                'name' => 'copy',
                'type' => 'textarea',
                'rows' => 4,
            ),
            array(
                'key' => 'field_6749cabd52b87',
                'label' => 'Options',
                'name' => 'options',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_6749cacf52b89',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'parent_repeater' => 'field_6749cabd52b87',
                    ),
                    array(
                        'key' => 'field_6749cae252b8a',
                        'label' => 'Description',
                        'name' => 'description',
                        'type' => 'textarea',
                        'rows' => 3,
                        'parent_repeater' => 'field_6749cabd52b87',
                    ),
                ),
            ),
            array(
                'key' => 'field_6749b7b0370d3',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/our-approach-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_6749ca3c8a54e',
        'title' => 'Sample campaign block',
        'fields' => array(
            array(
                'key' => 'field_6749b7be6f1f2',
                'label' => 'Sample campaign block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6749ca4da4145',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6749ca55a4146',
                'label' => 'Items',
                'name' => 'items',
                'type' => 'repeater',
                'layout' => 'row',
                'sub_fields' => array(
                    array(
                        'key' => 'field_6749ca86a414a',
                        'label' => 'Content item',
                        'name' => '',
                        'type' => 'accordion',
                        'open' => 0,
                        'multi_expand' => 0,
                        'endpoint' => 0,
                        'parent_repeater' => 'field_6749ca55a4146',
                    ),
                    array(
                        'key' => 'field_6749ca5fa4147',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'id',
                        'library' => 'all',
                        'preview_size' => 'medium',
                        'parent_repeater' => 'field_6749ca55a4146',
                    ),
                    array(
                        'key' => 'field_6749ca65a4148',
                        'label' => 'Content',
                        'name' => 'content',
                        'type' => 'wysiwyg',
                        'tabs' => 'all',
                        'toolbar' => 'full',
                        'media_upload' => 1,
                        'delay' => 0,
                        'parent_repeater' => 'field_6749ca55a4146',
                    ),
                    array(
                        'key' => 'field_6749ca6fa4149',
                        'label' => 'Button',
                        'name' => 'button',
                        'type' => 'link',
                        'return_format' => 'array',
                        'parent_repeater' => 'field_6749ca55a4146',
                    ),
                ),
            ),
            array(
                'key' => 'field_6749b7be6f1f3',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/sample-campaign-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67489c408b7f5',
        'title' => 'Advertising options block',
        'fields' => array(
            array(
                'key' => 'field_674894391444f',
                'label' => 'Advertising options block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6748b478ce187',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6748b482ce188',
                'label' => 'Options',
                'name' => 'options',
                'type' => 'repeater',
                'layout' => 'row',
                'sub_fields' => array(
                    array(
                        'key' => 'field_6748b48ace189',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'parent_repeater' => 'field_6748b482ce188',
                    ),
                    array(
                        'key' => 'field_6748b4aece18a',
                        'label' => 'Copy',
                        'name' => 'copy',
                        'type' => 'textarea',
                        'rows' => 3,
                        'parent_repeater' => 'field_6748b482ce188',
                    ),
                    array(
                        'key' => 'field_6748b4b3ce18b',
                        'label' => 'Link',
                        'name' => 'link',
                        'type' => 'text',
                        'parent_repeater' => 'field_6748b482ce188',
                    ),
                ),
            ),
            array(
                'key' => 'field_6748943914450',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/advertising-options-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67489c41eed91',
        'title' => 'Downloads info block',
        'fields' => array(
            array(
                'key' => 'field_6748935625a58',
                'label' => 'Downloads info block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67489c738c369',
                'label' => 'General',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_67489c4e8c366',
                'label' => 'Description',
                'name' => 'description',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_6748935625a59',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
            array(
                'key' => 'field_67489c5a8c367',
                'label' => 'Left',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_67489ca28c36a',
                'label' => 'Number',
                'name' => 'number',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67489cbb8c36b',
                'label' => 'Caption',
                'name' => 'caption',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67489c648c368',
                'label' => 'Rigth',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_67489cc28c36c',
                'label' => 'Number',
                'name' => 'number_2',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67489cca8c36d',
                'label' => 'Caption',
                'name' => 'caption_2',
                'type' => 'text',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/downloads-info-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group(array(
        'key' => 'group_67489c41526dc',
        'title' => 'Listeners info block',
        'fields' => array(
            array(
                'key' => 'field_674893d111d9d',
                'label' => 'Listeners info block',
                'name' => '',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_67489e608f05b',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_67489e678f05c',
                'label' => 'List',
                'name' => 'list',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_6748adfd8f05d',
                        'label' => 'Number',
                        'name' => 'number',
                        'type' => 'text',
                        'parent_repeater' => 'field_67489e678f05c',
                    ),
                    array(
                        'key' => 'field_6748ae088f05e',
                        'label' => 'Copy',
                        'name' => 'copy',
                        'type' => 'textarea',
                        'rows' => 2,
                        'parent_repeater' => 'field_67489e678f05c',
                    ),
                ),
            ),
            array(
                'key' => 'field_674893d111d9e',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/listeners-info-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'active' => true,
    ));
    acf_add_local_field_group( array(
        'key' => 'group_67378491d4e38',
        'title' => 'Our team block',
        'fields' => array(
            array(
                'key' => 'field_673783ff5330d',
                'label' => 'Our team block',
                'type' => 'message',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_673784950ee0c',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_6737849c0ee0d',
                'label' => 'Members',
                'name' => 'members',
                'type' => 'relationship',
                'post_type' => array(
                    0 => 'team',
                ),
                'post_status' => '',
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                ),
                'return_format' => 'id',
                'elements' => '',
                'bidirectional' => 0,
                'bidirectional_target' => array(
                ),
            ),
            array(
                'key' => 'field_673783ff5330e',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/our-team-block',
                ),
            ),
        ),
        'style' => 'seamless',
        'label_placement' => 'top',
        'active' => true,
    ) );

    acf_add_local_field_group(array(
        'key' => 'field_675064dad2f04',
        'title' => 'Popup modal block',
        'fields' => array (
            array (
                'key' => 'field_675064dad2f06',
                'label' => 'Popup modal block',
                'name' => '',
                'type' => 'message',
            ),
            array(
                'key' => 'field_675064dad2f07',
                'label' => 'Display',
                'name' => 'display',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/popup-modal-block',
                ),
            ),
        ),
        'style' => 'seamless'
    ));
    acf_add_local_field_group( array(
        'key' => 'group_675073366e25f',
        'title' => 'Reviews popup block',
        'fields' => array(
            array(
                'key' => 'field_6750714491bbb',
                'label' => 'Reviews popup block',
                'name' => '',
                'aria-label' => '',
                'type' => 'message',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            ),
            array(
                'key' => 'field_6750735c3cfb6',
                'label' => 'Reviews',
                'name' => 'reviews',
                'aria-label' => '',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'table',
                'pagination' => 0,
                'min' => 0,
                'max' => 0,
                'collapsed' => '',
                'button_label' => 'Add Row',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_675073663cfb7',
                        'label' => 'Title',
                        'name' => 'title',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'maxlength' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'parent_repeater' => 'field_6750735c3cfb6',
                    ),
                    array(
                        'key' => 'field_675073733cfb8',
                        'label' => 'Copy',
                        'name' => 'copy',
                        'aria-label' => '',
                        'type' => 'textarea',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'maxlength' => '',
                        'rows' => '',
                        'placeholder' => '',
                        'new_lines' => '',
                        'parent_repeater' => 'field_6750735c3cfb6',
                    ),
                    array(
                        'key' => 'field_6750737d3cfb9',
                        'label' => 'Rating',
                        'name' => 'rating',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '10',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => 5,
                        'min' => 0,
                        'max' => 5,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                        'parent_repeater' => 'field_6750735c3cfb6',
                    ),
                ),
            ),
            array(
                'key' => 'field_6750714491bbc',
                'label' => 'Display',
                'name' => 'display',
                'aria-label' => '',
                'type' => 'true_false',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'ui' => 1,
                'default_value' => 0,
                'message' => '',
                'ui_on_text' => '',
                'ui_off_text' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/reviews-popup-block',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ) );

    acf_add_local_field_group( array(
        'key' => 'group_6759d1f713a46',
        'title' => 'Recap video block',
        'fields' => array(
            array(
                'key' => 'field_6759d0706f204',
                'label' => 'Recap video block',
                'name' => '',
                'aria-label' => '',
                'type' => 'message',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => false,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'esc_html' => 0,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_6759d1f983e50',
                'label' => 'Title',
                'name' => 'title',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'allow_in_bindings' => 0,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_6759d1fe83e51',
                'label' => 'Video',
                'name' => 'video',
                'aria-label' => '',
                'type' => 'oembed',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'width' => '',
                'height' => '',
                'allow_in_bindings' => 0,
            ),
            array(
                'key' => 'field_6759d0706f205',
                'label' => 'Display',
                'name' => 'display',
                'aria-label' => '',
                'type' => 'true_false',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'ui' => 1,
                'default_value' => 0,
                'message' => '',
                'ui_on_text' => '',
                'ui_off_text' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/recap-video-block',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ) );
});