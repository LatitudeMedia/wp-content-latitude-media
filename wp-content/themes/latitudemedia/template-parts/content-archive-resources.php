<?php
if(!have_posts()) {
    return;
}
global $wp_query;
if($wp_query->found_posts <= 10) {
    $wp_query->max_num_pages = 1;
}

$postItemTemplate = get_wrap_rows_from_template('<li>
            <div class="image-folder">
                [thumb]
            </div>
            <div class="content-folder">
                [resource-tag]
                [title]
                [excerpt]
            </div>
        </li>');

?>
<div class="resources-list pink">
    <div class="container">
        <div class="resources-list-wrapper">
            <ul>
                <?php
                while(have_posts()) {
                    the_post();
                    get_template_part(
                        'template-parts/components/post',
                        'item',
                        array(
                            'post_id'  => get_the_ID(),
                            'settings' => array(
                                'thumb'   => array(
                                    'size'       => 'single-resource-featured',
                                    'link'       => true,
                                    'alt_image'  => false,
                                ),
                            ),
                            'rows'     => $postItemTemplate['rows'],
                            'wrap'     => $postItemTemplate['wrap'],
                        )
                    );
                    wp_reset_postdata();
                }
                ?>
            </ul>
        </div>
        <?php
            do_action('paginator', null, true);
        ?>
    </div>
</div>