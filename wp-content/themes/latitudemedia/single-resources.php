<?php
/**
 * The main template file
 *
 */

get_header();

$resourceData = get_resource_data(get_the_ID());
$sponsorId = $resourceData['sponsor'] ?? null;
$sponsor = get_published_post_by_id($sponsorId, ['post_type' => 'sponsors']);
?>

    <div class="right-sidebar-layout">
        <div class="container">
            <div class="right-sidebar-layout-wrapper">
                <div class="main-column">
                    <div class="single-resource-main-post">
                        <div class="single-resource-main-post-wrapper">
                            <div class="hero">
                                <div class="image-folder">
                                    <?php do_action('thumbnail_formatting', get_the_ID(), ['size' => 'single-resource-featured', 'link' => false]); ?>
                                </div>
                                <div class="content-folder">
                                    <h1><?php the_title(); ?></h1>
                                </div>
                            </div>
                            <div class="description">
                                <?php the_content(); ?>
                                <?php if($sponsor) :
                                    $sponsorUrl = get_field('website_link', $sponsor);
                                    ?>
                                <div class="sponsor">
                                    <div class="label"><?php _e('Sponsored by:', 'ltm'); ?></div>
                                    <a href="<?php _e($sponsorUrl); ?>" target="_blank">
                                        <?php do_action('thumbnail_formatting', $sponsor->ID, ['size' => 'resource-sponsor-logo', 'link' => false, 'class' => 'logo'], true); ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar">
                    <?php if( !empty($resourceData['form_code']) ) : ?>
                    <div class="form-block pink">
                        <div class="form-block-wrapper">
                            <?php printf('%s', $resourceData['form_code']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php get_footer();

