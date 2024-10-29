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
    function thumbnail_formatting( $post_id = null, $args = [], $echo = true) {
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
    function print_article_title( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $title_markup = sprintf( '<a href="%1$s" class="title">%2$s</a> ',
            get_the_permalink($post_id),
            esc_html__(get_the_title($post_id))
        );

        echo $title_markup;
    }
    add_action('print_article_title', 'print_article_title', 10, 1);
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
                $tags[] = sprintf('<a href="%s">%s</a>', get_category_link($category), $category->name);
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

if ( ! function_exists( 'print_article_type' ) ) :
    function print_article_type( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $news_type = ltm_get_news_type($post_id);

        echo '<div class="category">' . $news_type . '</div>';
    }
    add_action('print_article_type', 'print_article_type', 10, 4);
endif;

if ( ! function_exists( 'print_podcast_time' ) ) :
    function print_podcast_time( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $podcast_time = get_field('podcast_time', $post_id);

        echo '<span class="time">' . $podcast_time . ' MIN</span>';
    }
    add_action('print_podcast_time', 'print_podcast_time', 10, 4);
endif;

if ( ! function_exists( 'print_article_authors' ) ) :
    function print_article_authors( $post_id = null, $args = [] ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $defaults = [
            'link_class' => '',
            'link' => true,
            'wrap' => '',
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

        if(!$wrap) {
            echo implode('<span></span> ', $authorsHtml);
        } else {
            printf($wrap, implode('<span></span> ', $authorsHtml));
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