<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Image and text TYPE 5', 'ltm') . '</h3>';
}
// Set defaults Image and text.

$options = wp_parse_args(
    array_merge($args),
    [
        'title'         => '',
        'logo'          => null,
        'description'   => '',
        'base_color'    => '#c6168d',
        'shadow_color'  => '#F9E8F4',
        'display'       => false,
        'blockAttributes' => [],
    ]
);
extract($options);

if(!$display && !is_admin()) {
    return;
}

$blockType = ltm_get_block_style($blockAttributes['className'] ?? []);

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "style" => "--custom-block-base-color: {$base_color}; --custom-block-shadow-color: {$shadow_color};",
          "class" => 'content-block podcasts-sponsorship-section',
          "id" => 'image-and-text' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

$my_block_template = array(
    array(
        'core/paragraph',
    ),
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
        <div class="podcasts-sponsorship-section-wrapper">
            <?php if( !empty($logo) ) : ?>
                <div class="image-folder">
                    <?php do_action('thumbnail_formatting', null, ['size' => 'image-and-text-type4', 'link' => true, 'image_id' => $logo['ID']]); ?>
                </div>
            <?php endif; ?>

            <div class="content-folder">
                <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
                <div id="modal1" class="modal-content green">
                    <div class="modal-folder">
                        <a href="#" class="cross js-modal-close"></a>
                        <div class="podcast-modal-content">
                            <ul>
                                <li>
                                    <h5>Every week is a must listen.</h5>
                                    <p>“This is the best show on climate/clean tech – make sure you’re ready to go deep! Shayle is a great host and knows how to get into the core issues with each guest. Every week is a must listen.”</p>
                                    <div class="stars">
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <h5>Every week is a must listen.</h5>
                                    <p>“This is the best show on climate/clean tech – make sure you’re ready to go deep! Shayle is a great host and knows how to get into the core issues with each guest. Every week is a must listen.”</p>
                                    <div class="stars">
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                        <div class="star">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <a href="#" class="close-button js-modal-close">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>