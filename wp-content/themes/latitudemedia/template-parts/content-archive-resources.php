<?php
if(!have_posts()) {
    return;
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

$i = 0;

$resourceForm = get_field('subscribe_form', 'options');
?>
<div class="resources-list pink">
    <div class="container">
        <div class="resources-list-wrapper">
            <ul>
            <?php
            while(have_posts() && $i < 3) {
                the_post();

                get_template_part(
                    'template-parts/components/post',
                    'item',
                    array(
                        'post_id'  => get_the_ID(),
                        'settings' => array(
                            'thumb'   => array(
                                'size'       => 'article-related',
                                'link'       => true,
                                'alt_image'  => false,
                            ),
                        ),
                        'rows'     => $postItemTemplate['rows'],
                        'wrap'     => $postItemTemplate['wrap'],
                    )
                );
                wp_reset_postdata();
                $i++;
            }
            ?>
            </ul>
        </div>
    </div>
</div>

<?php
    get_template_part('template-parts/components/form/type3', '', $resourceForm);
?>

<div class="resources-list pink">
    <div class="container">
        <?php if($i > 0) : ?>
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
                                    'size'       => 'article-related',
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
        <?php endif; ?>
        <?php
            do_action('paginator', null, true);
        ?>
    </div>
</div>