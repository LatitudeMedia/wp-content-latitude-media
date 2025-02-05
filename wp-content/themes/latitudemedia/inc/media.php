<?php

add_image_size( 'home-top', 1300, 1424, true );
//Single resource page
add_image_size( 'single-resource-featured', 400, 518, false );
add_image_size( 'resource-sponsor-logo', 159, 32, false );
add_image_size( 'podcast-landing-overview', 413, 310, true );
add_image_size( 'event-speakers-list', 230, 230, true );
add_image_size( 'event-speakers-modal', 200, 200, true );
add_image_size( 'event-sponsors-list', 261, 160, false );
add_image_size( 'image-text-default', 430);
add_image_size( 'image-text-type4', 558);
add_image_size( 'image-text-type5', 416);
add_image_size( 'image-text-type6', 622);
add_image_size( 'image-text-type7', 513);
add_image_size( 'list-three-events', 367 );
add_image_size( 'event-preview-hosted', 129, 64, false );
add_image_size( 'news-with-hero', 405, 270, true );
add_image_size( 'articles-list', 416, 301, true );
add_image_size( 'posts-list-small', 217, 163, true );
add_image_size( 'large-podcast-default', 597, 448, false );
add_image_size( 'large-podcast-type2', 516, 402, false );
add_image_size( 'large-event', 1270, 715, false );
add_image_size( 'article-related-news', 329, 256, true );
add_image_size( 'author-archive-hero', 427, 427, true );
add_image_size( 'event-agenda', 552);

if( !function_exists('add_preload_images_tag') ) {
    add_action('wp_head', 'add_preload_images_tag');
    function add_preload_images_tag() {
        if( is_home() || is_front_page() ) {
            printf('<link rel="preload" as="image" href="%1$s">', 'https://i0.wp.com/latitudemedia.mystagingwebsite.com/wp-content/uploads/2024/12/674e1a0ed3b8fb4fa0cf3daa_Latitude-1920px-32-Image-2024-12-02T153519.762.jpg?resize=338%2C270&ssl=1');
        }

//        if( is_singular('product') ) {
//            global $product;
//            $attachment_ids = $product->get_gallery_image_ids();
//
//            if(!$attachment_ids) {
//                return;
//            }
//
//            printf('<link rel="preload" as="image" href="%1$s">', wp_get_attachment_image_url( $attachment_ids[0], 'product-gallery-thumb' ));
//        }
//
//        if( is_singular('post') && has_post_thumbnail() ) {
//            if (class_exists('\Automattic\Jetpack\Image_CDN\Image_CDN_Core')) {
//                $featuredUrl = \Automattic\Jetpack\Image_CDN\Image_CDN_Core::cdn_url(get_the_post_thumbnail_url(), array( 'fit' => "1024,551"));
//                printf('<link rel="preload" as="image" href="%1$s">', $featuredUrl);
//            }
//        }
//
//https://i0.wp.com/latitudemedia.mystagingwebsite.com/wp-content/uploads/2024/12/674e1a0ed3b8fb4fa0cf3daa_Latitude-1920px-32-Image-2024-12-02T153519.762.jpg?resize=338%2C270&ssl=1
//<link rel="preload" href="https://example.com/image.webp" as="image" imagesrcset="https://example.com/image.webp" imagesizes="(max-width: 338px) 100vw, 338px">
    }
}
