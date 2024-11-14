<?php
// Set defaults footer socials.

$options = wp_parse_args(
    $args,
    [
        'title' => '',
    ]
);

if( empty($options['title']) ) {
    return;
}

?>

<div class="topics-title-block">
    <div class="container">
        <div class="topics-title-block-wrapper">
            <h1 class="topics-title"><?php _e($options['title'], 'ltm'); ?></h1>
        </div>
    </div>
</div>