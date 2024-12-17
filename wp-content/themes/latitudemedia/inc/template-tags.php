<?php

if ( ! function_exists( 'button_unit' ) ) :
    function button_unit( $link = [], $wrap = null, $classes = '' ) {
        if(empty($link['title']) || empty($link['url'])) {
            return;
        }

        if( !isset($link['target']) ) {
            $link['target'] = '';
        }

        if(!$wrap) {
            printf('<a href="%1$s" class="%2$s" target="%3$s">%4$s</a>', $link['url'], $classes, $link['target'], $link['title']);
        } else {
            printf($wrap, $link['url'], $link['title'], $link['target']);
        }
    }
    add_action('button_unit', 'button_unit', 10, 5);
endif;

if ( ! function_exists( 'thumbnail_formatting' ) ) :
    /**
     * @param $post_id
     * @param $args
     * @param $echo
     * @return string
     */
    function thumbnail_formatting( $post_id = null, $args = [], $echo = true): string {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $defaults = [
            'image_id'      => null,
            'size'          => null,
            'mobile_size'   => null,
            'link'          => false,
            'link_class'    => '',
            'img_attr'      => [],
            'default'       => true
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args);

        if($image_id) {
            $img = sprintf('%1$s', wp_get_attachment_image($image_id, (wp_is_mobile() && $mobile_size) ? $mobile_size : $size, false, $img_attr));
        } elseif (!has_post_thumbnail($post_id)) {
            $img = '';
        } else {
            $img = sprintf('%1$s', get_the_post_thumbnail($post_id, (wp_is_mobile() && $mobile_size) ? $mobile_size : $size, $img_attr));
        }

        if(!$img && !$default) {
            return '';
        }

        if($link) {
            $img = sprintf('<a class="%1$s" href="%2$s">%3$s</a>',
                $link_class,
                get_the_permalink($post_id),
                $img
            );
        }

        if($echo) {
            echo $img;
        }

        return $img;
    }
    add_action('thumbnail_formatting', 'thumbnail_formatting', 10, 3);
endif;

if ( ! function_exists( 'get_user_excerpt' ) ) :
    function get_user_excerpt( $post_id = null, $words_num = 55, $strip = '...', $characters = false, $customContent = '' ) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $post = get_post($post_id);

        if(!empty($customContent)) {
            $content = strip_tags(apply_filters('the_content', $customContent));
        }
        elseif(has_excerpt($post_id)) {
            $content = get_the_excerpt($post_id);
        }
        else {
            $post_content = preg_replace("<!-- wp:acf/(.*?)-->", "", $post->post_content);
            $content = strip_tags(apply_filters('the_content', $post_content));
        }

        if($characters && strlen($content) > $words_num) {
            $wordsSet = wordwrap($content, $words_num, '<...>');
            return array_shift(explode('<...>', $wordsSet) ) . '...';
        } else {
            return wp_trim_words($content, $words_num, $strip);
        }
    }
endif;

if ( ! function_exists( 'section_title' ) ) :
    function section_title( $title = '', string $wrap = '' ) {
        if( empty($title) ) {
            return;
        }

        if( !empty($wrap) ) {
            printf($wrap, $title);
        } else {
            if( !empty($title) ) {
                printf('<h2>%1$s</h2>', $title);
            }
        }
    }
    add_action('section_title', 'section_title', 10, 2);
endif;

if ( ! function_exists( 'print_article_title' ) ) :
    function print_article_title( $post_id = null, $args = [] ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $defaults = [
            'wrap' => '',
            'link' => true,
        ];
        $args = wp_parse_args($args, $defaults);

        if( !$args['link'] ) {
            $title_markup = sprintf($args['wrap'] ?: '<div class="title">%1$s</div>',
                esc_html__(get_the_title($post_id))
            );
        }
        else {
            $title_markup = sprintf($args['wrap'] ?: '<a href="%1$s" class="title">%2$s</a>',
                get_the_permalink($post_id),
                esc_html__(get_the_title($post_id))
            );
        }

        echo $title_markup;
    }
    add_action('print_article_title', 'print_article_title', 10, 2);
endif;

if ( ! function_exists( 'print_article_read_more' ) ) :
    function print_article_read_more( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $title_markup = sprintf( '<a href="%1$s" class="blog-button">Read more</a> ',
            get_the_permalink($post_id)
        );

        echo $title_markup;
    }
    add_action('print_article_read_more', 'print_article_read_more', 10, 1);
endif;

if ( ! function_exists( 'print_article_date' ) ) :
    function print_article_date( $post_id = null, $args = array()) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $defaults = [
            'wrap' => '<div class="date">%1$s</div>',
            'format' => 'F j, Y',
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args);

        $date_markup = sprintf( $wrap, get_the_time($format, $post_id));

        echo $date_markup;
    }
    add_action('print_article_date', 'print_article_date', 10, 2);
endif;

if ( ! function_exists( 'print_article_excerpt' ) ) :
    function print_article_excerpt( $post_id = null, $characters = null, $wrap = null) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        if(!$wrap) {
                $wrap = '<p>%1$s</p>';
        }

        $excerpt_markup = sprintf($wrap,
            get_user_excerpt($post_id, $characters ?: 50, '...', $characters ? true : false)
        );
        echo $excerpt_markup;
    }
    add_action('print_article_excerpt', 'print_article_excerpt', 10, 4);
endif;

if ( ! function_exists( 'print_article_tags_list' ) ) :
    function print_article_tags_list( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $tags = [];
        $news_type = get_field('news_type', $post_id);
        if( !empty($news_type['label']) ) {
            $tags[] = sprintf('<span>%s</span>', $news_type['label']);
        }

        if( is_sponsored($post_id) ) {
            $tags[] = sprintf('<span class="sponsored">%s</span>', __('Sponsored', 'ltm'));
        }

        $categories = get_the_terms( $post_id, 'category' );
        if( !empty($categories) ) {
            foreach ($categories as $category) {
                $abbreviatedName = get_field('abbreviated_name', 'category_' . $category->term_id, true);
                $tags[] = sprintf('<a href="%s">%s</a>', get_category_link($category), $abbreviatedName);
            }
        }

        $tags_markup = '';
        foreach ($tags as $tag) {
            $tags_markup .= sprintf('<li>%s</li>', $tag);
        }

        echo '<ul class="tags-list">' . $tags_markup . '</ul>';
    }
    add_action('print_article_tags_list', 'print_article_tags_list', 10, 4);
endif;

if ( ! function_exists( 'print_article_sponsored_label' ) ) :
    function print_article_sponsored_label( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        if( is_sponsored($post_id) ) {
            echo '<ul class="tags-list"><li><span class="sponsored">Sponsored</span></li></ul>';
        }
    }
    add_action('print_article_sponsored_label', 'print_article_sponsored_label', 10, 1);
endif;

if ( ! function_exists( 'print_article_type' ) ) :
    function print_article_type( $post_id = null, $args ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $news_type = ltm_get_news_type($post_id);
        if(!$news_type) {
            return;
        }
        $defaults = [
            'wrap' => '<div class="category">%1s</div>',
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args);

        $typeLine = sprintf( $wrap, $news_type);

        echo $typeLine;
    }
    add_action('print_article_type', 'print_article_type', 10, 2);
endif;

if ( ! function_exists( 'print_podcast_time' ) ) :
    function print_podcast_time( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $podcast_time = get_field('podcast_time', $post_id);

        if( !empty($podcast_time) ) {
            echo '<div class="time">' . $podcast_time . ' min</div>';
        }
    }
    add_action('print_podcast_time', 'print_podcast_time', 10, 4);
endif;

if ( ! function_exists( 'print_podcast_listening' ) ) :
    function print_podcast_listening( $podcast_id = null, $title = '' ) {
        if ( ! $podcast_id ) {
            $podcast_id = get_the_ID();
        }

        get_template_part('template-parts/podcast/listening', 'links', [
            'title' => $title,
            'links' => [
                'apple'     => get_field('apple_podcast', $podcast_id),
                'spotify'   => get_field('spotify', $podcast_id),
                'rss'       => get_field('rss_feed', $podcast_id),
            ]
        ]);
    }
    add_action('print_podcast_listening', 'print_podcast_listening', 10, 4);
endif;

if ( ! function_exists( 'print_podcast_organization' ) ) :
    function print_podcast_organization( $podcast_id = null ) {
        if ( ! $podcast_id ) {
            $podcast_id = get_the_ID();
        }
        $podcastOrganization = get_field('organization', $podcast_id);

        echo '<div class="company">' . $podcastOrganization . ' </div>';

    }
    add_action('print_podcast_organization', 'print_podcast_organization', 10, 1);
endif;

if ( ! function_exists( 'print_resource_tag' ) ) :
    function print_resource_tag( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        $resourceTag = get_field('resource_type', $post_id);

        echo '<div class="tag">' . $resourceTag . ' </div>';
    }
    add_action('print_resource_tag', 'print_resource_tag', 10, 1);
endif;

if ( ! function_exists( 'print_event_type' ) ) :
    function print_event_type( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $eventType = get_field('event_type', $post_id);

        if( !empty($eventType) ) {
            echo '<span class="solid">' . $eventType . '</span>';
        }
    }
    add_action('print_event_type', 'print_event_type', 10, 1);
endif;


if ( ! function_exists( 'print_article_authors' ) ) :
    function print_article_authors( $post_id = null, $args = [] ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        if(get_post_type($post_id) == 'industry-news') {
            print_industry_news_company($post_id);
            return;
        }

        $defaults = [
            'link_class' => '',
            'link' => true,
            'wrap' => '',
            'separator' => '<span></span> ',
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args);

        $authorsHtml = [];
        // PublishPress Authors functionality.
        if ( function_exists( 'get_post_authors' ) ) {
            $authors       = get_post_authors( $post_id );
            foreach ( $authors as $i => $author_obj ) {
                if($link) {
                    $authorsHtml[] = sprintf('<a class="%1$s" href="%2$s">%3$s</a>',
                        $link_class,
                        esc_url($author_obj->link),
                        esc_attr($author_obj->name)
                    );
                } else {
                    $authorsHtml[] = $author_obj->name;
                }
            }

        } else {

            $author_id   = get_post_field( 'post_author', $post_id );
            $author_name = get_the_author_meta( 'display_name', $author_id );
            $author_link = get_author_posts_url( $author_id );

            if($link) {
                $authorsHtml[] = sprintf('<a class="%1$s" href="%2$s">%3$s</a>',
                    $link_class,
                    esc_url( $author_link ),
                    esc_attr( $author_name )
                );
            } else {
                $authorsHtml[] = esc_attr( $author_name );
            }
        }

        if( is_news_type('podcast') && $podcast = get_post_assigned_podcast($post_id)) {
            $authorsHtml[] = sprintf('<a class="%1$s" href="%2$s">%3$s</a>', $link_class, get_the_permalink($podcast->ID), $podcast->post_title);
        }

        if(!$wrap) {
            echo implode($separator, $authorsHtml);
        } else {
            printf($wrap, implode($separator, $authorsHtml));
        }
    }
    add_action('print_article_authors', 'print_article_authors', 10, 2);
endif;

if ( ! function_exists( 'social_icons' ) ) :
    function social_icons(string $social, $echo = true) {
        if( empty($social) ) {
            return '';
        }

        $item = '';
        switch ($social) {
            case 'linkedin':
                $item = sprintf('<img class="initial" src="%1$s/src/assets/images/linkedin.svg" alt="linkedin" /><img class="hover" src="%1$s/src/assets/images/linkedin_green.svg" alt="linkedin" />', get_template_directory_uri());
                break;
            case 'facebook':
                $item = sprintf('<img class="initial" src="%1$s/src/assets/images/facebook.svg" alt="facebook" /><img class="hover" src="%1$s/src/assets/images/facebook_green.svg" alt="facebook" />', get_template_directory_uri());
                break;
        }

        if( $echo ) {
            echo $item;
        }

        return $item;
    }
endif;


if ( ! function_exists( 'paginator' ) ) :
    function paginator( $wp_query = null, $dynamic = false, $base = 'page' ) {
        if(!$wp_query) {
            global $wp_query;
        }

        if ( !$wp_query->max_num_pages <= 1 ) {
            $args = [
                'format' => $dynamic ? "?{$base}=%#%" : '',
                'total' => $wp_query->max_num_pages,
                'current' => max(1, $wp_query->get('paged')),
                'end_size' => 1,
                'mid_size' => 1,
                'prev_next' => false,
                'prev_text' => '',
                'next_text' => '',
                'type' => 'array',
            ];

            if(!$dynamic) {
                $args['base'] = str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999)));
            }

            $paginator = paginate_links($args);
            $paginator_content = '';

            if (is_array($paginator)) {
                foreach ($paginator as $page) {
                    $paginator_content .= sprintf('<li>%1$s</li>', $page);
                }

                printf('<div class="pager pink"><ul>%1$s</ul></div>', $paginator_content);
            }
        }

    }
    add_action('paginator', 'paginator', 10, 3);
endif;

if ( ! function_exists( 'print_speaker_job_title' ) ) :
    function print_speaker_job_title( $post_id = null, $args = array()): void {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $jobTitle = get_field('job_title', $post_id);
        if(!$jobTitle) {
            return;
        }

        $defaults = [
            'wrap' => '<p class="occupation">%1s</p>',
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args);

        $jobLine = sprintf( $wrap, $jobTitle);

        echo $jobLine;
    }
    add_action('print_speaker_job_title', 'print_speaker_job_title', 10, 2);
endif;

if ( ! function_exists( 'print_speaker_company' ) ) :
    function print_speaker_company( $post_id = null, $args = array()): void
    {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        $company = get_field('company', $post_id);
        if(!$company) {
            return;
        }

        $defaults = [
            'wrap' => '<p class="company">%1s</p>',
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args);

        $jobLine = sprintf( $wrap, $company);

        echo $jobLine;
    }
    add_action('print_speaker_company', 'print_speaker_company', 10, 2);
endif;

if ( ! function_exists( 'print_speaker_socials' ) ) :
    function print_speaker_socials( $post_id = null, $args = array()): void {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $linkedin = get_field('linkedin_link', $post_id);
        if(!$linkedin) {
            return;
        }

        $defaults = [
            'wrap' => '<a href="%1$s" class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M5 1.25C3.48122 1.25 2.25 2.48122 2.25 4C2.25 5.51878 3.48122 6.75 5 6.75C6.51878 6.75 7.75 5.51878 7.75 4C7.75 2.48122 6.51878 1.25 5 1.25ZM3.75 4C3.75 3.30964 4.30964 2.75 5 2.75C5.69036 2.75 6.25 3.30964 6.25 4C6.25 4.69036 5.69036 5.25 5 5.25C4.30964 5.25 3.75 4.69036 3.75 4Z" fill="currentColor"></path>
<path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 8C2.25 7.58579 2.58579 7.25 3 7.25H7C7.41421 7.25 7.75 7.58579 7.75 8V21C7.75 21.4142 7.41421 21.75 7 21.75H3C2.58579 21.75 2.25 21.4142 2.25 21V8ZM3.75 8.75V20.25H6.25V8.75H3.75Z" fill="currentColor"></path>
<path fill-rule="evenodd" clip-rule="evenodd" d="M9.25 8C9.25 7.58579 9.58579 7.25 10 7.25H14C14.4142 7.25 14.75 7.58579 14.75 8V8.43402L15.1853 8.24748C15.9336 7.92676 16.7339 7.72565 17.5433 7.65207C20.3182 7.3998 22.75 9.58038 22.75 12.3802V21C22.75 21.4142 22.4142 21.75 22 21.75H18C17.5858 21.75 17.25 21.4142 17.25 21V14C17.25 13.6685 17.1183 13.3505 16.8839 13.1161C16.6495 12.8817 16.3315 12.75 16 12.75C15.6685 12.75 15.3505 12.8817 15.1161 13.1161C14.8817 13.3505 14.75 13.6685 14.75 14V21C14.75 21.4142 14.4142 21.75 14 21.75H10C9.58579 21.75 9.25 21.4142 9.25 21V8ZM10.75 8.75V20.25H13.25V14C13.25 13.2707 13.5397 12.5712 14.0555 12.0555C14.5712 11.5397 15.2707 11.25 16 11.25C16.7293 11.25 17.4288 11.5397 17.9445 12.0555C18.4603 12.5712 18.75 13.2707 18.75 14V20.25H21.25V12.3802C21.25 10.4759 19.589 8.97227 17.6791 9.14591C17.025 9.20536 16.3784 9.36807 15.7762 9.6262L14.2954 10.2608C14.0637 10.3601 13.7976 10.3363 13.5871 10.1976C13.3767 10.0588 13.25 9.82354 13.25 9.57143V8.75H10.75Z" fill="currentColor"></path>
</svg></a>',
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args);

        $jobLine = sprintf( $wrap, $linkedin);

        echo $jobLine;
    }
    add_action('print_speaker_socials', 'print_speaker_socials', 10, 2);
endif;

if ( ! function_exists( 'print_content' ) ) :
    function print_content( $post_id = null ): void {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        echo get_the_content( $post_id );
    }
    add_action('print_content', 'print_content', 10, 1);
endif;

if ( ! function_exists( 'print_event_start_date' ) ) :
    function print_event_start_date( $post_id = null, $args = [] ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        $defaults = [
            'wrap'  => '<div class="date">%1s</div>',
            'format' => 'M j, Y',
        ];
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $eventStartDate = get_event_start_date($post_id, $format);
        $dateLine = sprintf( $wrap, $eventStartDate);

        echo $dateLine;
    }
    add_action('print_event_start_date', 'print_event_start_date', 10, 2);
endif;

if ( ! function_exists( 'print_event_location' ) ) :
    function print_event_location( $post_id = null, $args = [] ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        $defaults = [
            'wrap'  => '<div class="place">%1s</div>',
        ];
        $args = wp_parse_args($args, $defaults);
        extract($args);
        $location = get_field('location', $post_id);
        $locationLine = sprintf( $wrap, $location);

        echo $locationLine;
    }
    add_action('print_event_location', 'print_event_location', 10, 2);
endif;


if ( ! function_exists( 'print_industry_news_company' ) ) :
    function print_industry_news_company( $post_id = null, $args = array()): void
    {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        $companyName = get_field('company_name', $post_id);
        $companyLink = get_field('company_link', $post_id);
        if(empty($companyName) && empty($companyLink)) {
            return;
        }

        $defaults = [
            'wrap' => '<a class="author" href="%s" target="_blank">%s</a>',
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args);

        $companyLine = sprintf( $wrap, $companyLink, $companyName);

        echo $companyLine;
    }
    add_action('print_industry_news_company', 'print_industry_news_company', 10, 2);
endif;

if ( ! function_exists( 'print_image_and_text_image' ) ) :
    function print_image_and_text_image( $logo = null, $size = '', $imageLink = null ): void
    {
        if( empty($logo) ) {
            return;
        }

        $imageHtml = thumbnail_formatting(null, ['image_id' => $logo['ID'], 'size' => $size, 'link' => false], false);
        if( !empty($imageLink) )
        {
            printf('<a href="%1$s">%2$s</a>', $imageLink, $imageHtml);
        }
        else
        {
            printf('<span>%1$s</span>', $imageHtml);
        }
    }
    add_action('print_image_and_text_image', 'print_image_and_text_image', 10, 3);
endif;