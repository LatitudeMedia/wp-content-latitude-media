<?php
// Set defaults Image and text.
$options = wp_parse_args(
    $args,
    [
        'type'            => 'default',
        'blockAttributes' => [],
    ]
);

get_template_part(
    'template-parts/components/event-gray-icon/' . $options['type'], '', $options
);