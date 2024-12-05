<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event speakers block', 'ltm') . '</h3>';
}
// Set defaults Event speakers block.
$options = wp_parse_args(
    $args,
    [
        'title'     => 'Featured speakers',
        'speakers'  => [],
        'display'   => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($speakers) ) {
    return;
}

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block our-team-section',
          "id" => 'event-speakers-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);

$speakersPosts = get_published_posts_by_ids($speakers, ['post_type' => 'speakers']);
if(!$speakersPosts->have_posts()) {
    return;
}

$postItemTemplate = get_wrap_rows_from_template('
    <div class="image-folder green"><a href="#{uniqid}" class="js-modal-open">[thumb]</a></div>
    <div class="content-folder">
        <a href="#{uniqid}" class="name green js-modal-open">[title]</a>
        [speaker-company]
        <a class="more-link js-modal-open" href="#{uniqid}">Read more</a>
    </div>
');

$modalItemTemplate = get_wrap_rows_from_template('
<div id="{uniqid}" class="modal-content green">
    <div class="modal-folder">
        <a class="cross js-modal-close"></a>
        <div class="bio">
            [thumb]
            <div class="info">
                [title]
                [speaker-job-title]
                [speaker-company]
                <p class="connect">
                    <span class="label">Connect via:</span>
                    [speaker-socials]
                </p>
            </div>
        </div>
        <div class="description">
            [content]
        </div>
        <a class="strict-button green js-modal-close">Close</a>
    </div>
</div>');
?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="our-team-section-wrapper">
            <div class="bordered-title green"><?php echo $title; ?></div>
            <ul class="team">
                <?php
                    while($speakersPosts->have_posts()) {
                        echo '<li>';
                        $speakersPosts->the_post();
                        $modalId = uniqid();
                        get_template_part(
                            'template-parts/components/post',
                            'item',
                            array(
                                'post_id'  => get_the_ID(),
                                'settings' => array(
                                    'thumb'   => array(
                                        'size'       => 'event-speakers-list',
                                        'link'       => false,
                                    ),
                                    'title' => array(
                                        'wrap' => '%1$s',
                                        'link' => false
                                    ),
                                    'speaker-company' => [
                                            'wrap' => '<p class="occupation">%1$s</p>',
                                    ]
                                ),
                                'rows'     => $postItemTemplate['rows'],
                                'wrap'     => preg_replace("/\{uniqid\}/", $modalId, $postItemTemplate['wrap']),
                            )
                        );

                        get_template_part(
                            'template-parts/components/post',
                            'item',
                            array(
                                'post_id'  => get_the_ID(),
                                'settings' => array(
                                    'thumb'   => array(
                                        'size'       => 'event-speakers-modal',
                                        'class'      => 'avatar',
                                        'link'       => false,
                                        'img_attr'   => [
                                                'class'     => 'avatar',
                                        ]
                                    ),
                                    'title' => array(
                                        'wrap' => '<h3>%1$s</h3>',
                                        'link' => false
                                    ),
                                ),
                                'rows'     => $modalItemTemplate['rows'],
                                'wrap'     => preg_replace("/\{uniqid\}/", $modalId, $modalItemTemplate['wrap']),
                            )
                        );
                        wp_reset_postdata();
                        echo '</li>';
                    }
                ?>
            </ul>
        </div>
    </div>
</div>

