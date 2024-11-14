<?php get_header(); ?>
<div class="topics-title-block">
    <div class="container">
        <div class="topics-title-block-wrapper">
            <h1 class="topics-title"><?php _e('From the Industry', 'ltm'); ?></h1>
        </div>
    </div>
</div>

<div class="topics-archive-section">
    <div class="container-narrow">
        <div class="topics-archive-section-wrapper load-more-container">
            <div class="posts-list-section">
                <?php
                $postItemTemplate = get_wrap_rows_from_template('<li>
                    <div class="content-folder">
                        <h4>[title]</h4>
                        <div class="info">
                            [author]
                            <span></span>
                            [date]
                        </div>
                    </div>
                </li>');

                $args = [
                    'display'       => true,
                    'forced'        => true,
                    'pagination'    => true,
                ];

                get_template_part('template-parts/category/articles', 'list',
                    array_merge($args, $postItemTemplate)
                );
                ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
