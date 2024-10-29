<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Three podcasts section', 'ltm') . '</h3>';
}
// Set defaults Three podcasts block.

$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'title'             => 'Podcasts',
        'number_of_items'   => 3,
        'columns'           => [],
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($columns) ) {
    return;
}

?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-section podcasts-large-blue-section',
                "id" => 'three-podcasts-section' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <div class="container">
        <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
        <div class="podcasts-large-blue-section-wrapper">
            <?php foreach ($columns as $column) :

                $items = \LatitudeMedia\Manage_Data()->curated_query(
                    [
                        'post_type'         => 'post',
                        'posts_per_page'    => $number_of_items ?? 3,
                        'meta_query'        => array(
                            array(
                                'key'       => 'podcast',
                                'value'     => $column,
                                'compare'   => '=',
                            ),
                        ),

                    ]
                );
                if( !$items->have_posts() ) {
                    continue;
                }
                ?>
                <div class="col">
                    <div class="posts-list-section">
                        <ul class="posts">
                            <?php
                            $postItemTemplate = get_wrap_rows_from_template('<li>
                                <div class="content-folder">
                                    [title]
                                    <div class="image-folder">
                                        [thumb]
                                    </div>
                                </div>
                            </li>');
                            $rows = $postItemTemplate['rows'];
                            $wrap = $postItemTemplate['wrap'];
                            get_template_part(
                                'template-parts/components/post',
                                'item',
                                array(
                                    'post_id'  => $column,
                                    'settings' => array(
                                        'author' => array(
                                            'link_class' => 'author'
                                        ),
                                        'date' => array(
                                            'format' => 'M j, Y'
                                        ),
                                    ),
                                    'rows'          => $rows,
                                    'wrap'          => $wrap,
                                )
                            );

                            $postItemTemplate = get_wrap_rows_from_template('<li>
                                <div class="content-folder">
                                    [title]
                                    <div class="info">
                                        [time]
                                        <span></span>
                                        [date]
                                    </div>
                                </div>
                            </li>');
                            $rows = $postItemTemplate['rows'];
                            $wrap = $postItemTemplate['wrap'];
                            while( $items->have_posts() ) {
                                $items->the_post();

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
                                        ),
                                        'rows'          => $rows,
                                        'wrap'          => $wrap,
                                    )
                                );
                                wp_reset_postdata();
                            }
                            ?>
                        </ul>
                        <?php do_action('button_unit', ['url' => get_the_permalink($column), 'title' => 'Browse episodes'], null, 'browse'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>