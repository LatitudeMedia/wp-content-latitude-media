<?php
namespace LatitudeMedia\RestApi;

/**
 * Custom post type for Events
 *
 * @package LatitudeMedia
 */

/**
 * Class for the events post type.
 */

use LatitudeMedia\Instance;
class LoadMoreEvents {

    use Instance;
    /**
     * Namespace for REST API endoints
     *
     * @var string
     */
    const REST_API_NAMESPACE = 'wp/v2';

    const REST_API_LOAD_MORE_EVENTS = 'events/load-more';

    /**
     * Constructor.
     */
    public function __construct() {
        // Create the post type
        add_action( 'after_setup_theme', [$this, 'setup'], 8 );
    }

    public function setup() {
        // Localize important JS vars to innolead's main JS script.
        // to fire after main innolead JS script has been enqueued.
        add_action( 'wp_enqueue_scripts', array( $this, 'localize_js_vars' ), 11 );

        // Register REST callbacks.
        add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
    }

    public function localize_js_vars() {
        // Localize appData object.
        wp_localize_script(
            'jquery',
            'appRest',
            array(
                'rest_endpoints' => array(
                    'load_more_events' => get_rest_url( null, self::REST_API_NAMESPACE . '/' . self::REST_API_LOAD_MORE_EVENTS),
                ),
            )
        );
    }

    /**
     * Register REST API endpoints
     */
    public function rest_api_init() {
        // Subscribe user on list
        register_rest_route(
            self::REST_API_NAMESPACE,
            self::REST_API_LOAD_MORE_EVENTS,
            array(
                'methods'       => 'GET',
                'callback'      => array( $this, 'load_more_events' ),
                'show_in_index' => false,
            )
        );
    }

    /**
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     */
    public function load_more_events( \WP_REST_Request $request ) {
        $body = $request->get_query_params();

        if ( ! isset( $body['page'] )
            || empty( $body['page'] ) ) {

            return new \WP_Error( 'no-page', __( 'Page is not correct', 'innolead' ), array( 'status' => 400 ) );
        }

        $response       = [];
        $customEvents   = [];
        $page       = intval( $body['page'] );
        if ( isset( $body['events'] )
            && !empty( $body['events'] ) ) {
            $customEvents = explode(',', $body['events']);
        }

        switch ($body['type'] ?? '') {
            case 'upcoming':
                $eventsList = get_events_list('upcoming', ['paged' => $body['page']]);
                break;
            case 'past':
                $eventsList = get_events_list('past', ['paged' => $body['page']]);
                break;
            default:
                $eventsList = get_events_list('', ['paged' => $body['page']], $customEvents);
                break;
        }

        if( !$eventsList->have_posts() ) {
            return [];
        }

        $postItemTemplate = get_wrap_rows_from_template('
        <li>
            <div class="image-folder green">
                [thumb]
            </div>
            <div class="content-folder">
                <div class="event-date green">
                    [event-type]
                    [event-start-date]
                </div>
                [title]
                [excerpt]
            </div>
        </li>
        ');

        ob_start();
        while( $eventsList->have_posts() ) {
            $eventsList->the_post();

            $rows = $postItemTemplate['rows'];
            $wrap = $postItemTemplate['wrap'];
            get_template_part(
                'template-parts/components/post',
                'item',
                array(
                    'post_id'  => get_the_ID(),
                    'settings' => array(
                        'thumb'   => array(
                            'size'       => 'list-three-events',
                            'link'       => true,
                            'link_class' => '',
                            'alt_image'  => false,
                            'type'       => true,
                        ),
                    ),
                    'rows'          => $rows,
                    'wrap'          => $wrap,
                )
            );
            wp_reset_postdata();
        }
        $response['content'] = ob_get_contents();
        ob_end_clean();

        if($eventsList->max_num_pages > 1 && $eventsList->max_num_pages > $page) {
            $response['next_page'] = ++$page;
        }
        return $response;
    }
}
