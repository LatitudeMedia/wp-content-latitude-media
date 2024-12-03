<?php
$podcastData = get_fields(get_the_ID());

?>
<div class="podcast-category-hero-section right-image-folder">
    <div class="container">
        <div class="podcast-category-hero-section-wrapper">
            <div class="content-folder">
                <h1><?php the_title(); ?></h1>
                <p><strong><?php the_excerpt(); ?></strong></p>
                <?php the_content(); ?>
                <div class="info">
                    <div class="row">
                        <span class="label"><?php _e('Host', 'ltm')?>:&nbsp</span>
                        <span class="value"><?php do_action('print_article_authors', get_the_ID(), ['separator' => '<span> + </span>']); ?></span>
                    </div>
                    <div class="row">
                        <?php do_action('print_podcast_listening', get_the_ID(), 'listen on:'); ?>
                    </div>
                    <div class="row">
                        <?php
                        get_template_part('template-parts/components/social', 'links', [
                            'title' => 'Follow:',
                            'links' => [
                                'x_twitter' => $podcastData['x_twitter'],
                                'linkedin'  => $podcastData['linkedin'],
                                'instagram' => $podcastData['instagram'],
                                'facebook'  => $podcastData['facebook']
                            ]
                        ]);
                        ?>
                    </div>
                </div>
            </div>
            <div class="image-folder">
                <?php
                    do_action('thumbnail_formatting', get_the_ID(), ['size' => 'podcast-landing-overview', 'link' => false]);
                ?>
            </div>
        </div>
    </div>
</div>
