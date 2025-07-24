<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Editors picks block', 'ltm') . '</h3>';
}
// Set defaults Editors picks block.

$postItemTemplate = get_wrap_rows_from_template('<li>
            <ul class="tags-list">
                <li><span>[article-type]</span></li>
                [sponsored-label]
            </ul>
            [title]
            <div class="info">
                [author]<span></span>[date]
            </div>
        </li>');

$options = wp_parse_args(
    array_merge($postItemTemplate,
        $args,
    ),
    [
        'title'             => 'Editor’s picks',
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

$editorsPicksGlobal = get_field('editors_picks_global', 'options') ?: [];
if( empty($editorsPicksGlobal) ) {
    return;
}

$queryArgs = [
    'post_type'        => 'post',
    'posts_per_page'   => -1,
];
$items = \LatitudeMedia\Manage_Data()->curated_query($queryArgs, $editorsPicksGlobal);

if( !$items->have_posts() ) {
    return;
}
?>
<div class="content-block editors-picks-block">
    <div
        <?php
        echo wp_kses_data(
            get_block_wrapper_attributes(
                [
                    "class" => 'editors-picks-block-wrapper',
                    "id" => $blockAttributes['anchor'] ?: '',
                ]
            )
        );
        ?>
    >
        <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
        <ul class="posts">
            <?php
            while($items->have_posts() ) {
                $items->the_post();

                \LatitudeMedia\Page_Data()->addItems([get_the_ID()]);
                get_template_part(
                    'template-parts/components/post',
                    'item',
                    array(
                        'post_id'  => get_the_ID(),
                        'settings' => array(
                            'author' => array(
                                'link_class' => 'author'
                            ),
                            'date' => array(
                                'format' => 'M j, Y'
                            ),
                            'article-type' => array(
                                    'wrap' => '%s'
                            ),
                            'sponsored-label' => array(
                                    'wrap' => '<li><span class="sponsored">%s</span></li>'
                            )
                        ),
                        'rows'          => $rows,
                        'wrap'          => $wrap,
                    )
                );
                wp_reset_postdata();
            }
            ?>
        </ul>
    </div>
</div>

