<?php

namespace LatitudeMedia;

use WP_Query;

class Manage_Data
{
    use Instance;

    private $manualSearchArgs = [];

    /**
     * Curated Posts Query
     *
     * @param  array  $args     the query arguments
     * @param  array  $post_ids the curated post IDs to quer    y for
     * @return WP_Query         the query object
     */
    public function curated_query( $args = array(), $post_ids = array() ) {
        if( !empty($this->manualSearchArgs) ) {
            $args = $this->manualSearchArgs;
            $this->manualSearchArgs = [];
        }
        // NOTE: If both `post__in` and `post__not_in` are used in same instance of WP_Query, `post__not_in` will be ignored.
        $args['ignore_sticky_posts'] = 1;

        if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
            $args['post__in'] = $post_ids;
            if(!isset($args['orderby'])) {
                $args['orderby']  = 'post__in';
            }
        }

        // Default to published posts only.
        if ( empty( $args['post_status'] ) ) {
            $args['post_status'] = 'publish';
        }

        return new \WP_Query( $args );
    }

    public function setManualSearchArgs( $customArgs = [], $defaults = [] ) {
        $this->manualSearchArgs = $defaults;
        if( !empty($customArgs['number_of_items']) ) {
            $this->manualSearchArgs['posts_per_page'] = $customArgs['number_of_items'];
        }

        if( isset($customArgs['exclude']) && $customArgs['exclude'] === true ) {
            $customArgs['exclude'] = \LatitudeMedia\Page_Data()->getItems();
        }
        else {
            $customArgs['exclude'] = [];
        }

        $post_ids = [];
        switch($customArgs['type']) {
            case 'category' && !empty($customArgs['category']):
                $this->manualSearchArgs['cat'] = $customArgs['category'];
                break;
            case 'tag' && !empty($customArgs['tag']):
                $this->manualSearchArgs['tag_id'] = $customArgs['tag'];
                break;
            case 'type' && !empty($customArgs['news_type']):
                $this->manualSearchArgs['meta_query'][] = [
                    'key'       => 'news_type',
                    'value'     => $customArgs['news_type'],
                    'compare'   => '='
                ];
                break;
            case 'post_type' && !empty($customArgs['post_type']):
                $this->manualSearchArgs['post_type'] = $customArgs['post_type'];
                break;
            case 'custom' && !empty($customArgs['custom']):
                $this->manualSearchArgs['post_type'] = ['post', 'industry-news', 'resources', 'research', 'events', 'podcasts'];
                $post_ids = (!empty($customArgs['custom']) ? $customArgs['custom'] : []);
                break;
        }

        if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
            $this->manualSearchArgs['post__in'] = $post_ids;
            if(!isset($this->manualSearchArgs['orderby'])) {
                $this->manualSearchArgs['orderby']  = 'post__in';
            }
        }

        $this->manualSearchArgs['post__not_in'] = $customArgs['exclude'];
    }

}

/**
 * Initialize the landing page class on theme setup
 *
 * @return object the landing page class
 */
function Manage_Data() {
    return Manage_Data::instance();
}
add_action( 'after_setup_theme', 'LatitudeMedia\Manage_Data', 8 );
