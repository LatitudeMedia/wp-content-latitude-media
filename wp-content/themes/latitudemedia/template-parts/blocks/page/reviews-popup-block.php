<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Reviews popup block', 'ltm') . '</h3>';
}
// Set defaults Reviews popup block.
$options = wp_parse_args(
    $args,
    [
        'reviews' => [],
        'display' => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if ( empty($reviews) ) {
    return;
}

$classes = !is_admin() ? 'modal-content' : '';
$blockAttrs = wp_kses_data(
    get_block_wrapper_attributes(
        [
            "class" => 'content-block reviews-popup-block green ' . $classes,
            "id" => ($options['blockAttributes']['anchor'] ?: ''),
        ]
    )
);

$starHtml = '<div class="star">
    <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
    </svg>
</div>';
?>

<div <?php echo $blockAttrs; ?>
>
    <div class="modal-folder">
        <a href="#" class="cross js-modal-close"></a>
        <div class="podcast-modal-content">
            <ul>
                <?php
                foreach ( $reviews as $review ) {
                    $stars = implode('', array_fill(0, $review['rating'], $starHtml));
                    printf('<li><h5>%s</h5><p>%s</p><div class="stars">%s</div></li>', $review['title'], $review['copy'], $stars);
                }
                ?>
            </ul>
        </div>
        <a href="#" class="close-button js-modal-close">Close</a>
    </div>
</div>