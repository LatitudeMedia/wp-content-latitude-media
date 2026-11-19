<?php
namespace LTMCore\PostTypes;

/**
 * Custom post type for Team
 *
 * @package LTMCore
 */

/**
 * Class for the Team post type.
 */
class Team {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'team';

    /**
     * Constructor.
     */
    public function __construct() {
        // Create the post type
        add_action( 'init', array( $this, 'create_post_type' ) );

        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            add_action( 'admin_notices', array( $this, 'missing_acf_notice' ) );
            return;
        }

        add_action( 'acf/include_fields', array( $this, 'register_field_group' ) );
    }

    /**
     * Warns in wp-admin that the team member fields need ACF Pro active.
     */
    public function missing_acf_notice() {
        echo '<div class="notice notice-warning"><p>' .
            esc_html__( 'Latitude Media Core: the Team member options fields require Advanced Custom Fields Pro to be active.', 'ltm' ) .
            '</p></div>';
    }

    /**
     * Registers the "Team member options" field group. Copied verbatim from
     * the theme's acf-export.php — the group key and every field key are
     * unchanged so existing team member data keeps resolving.
     */
    public function register_field_group() {
        acf_add_local_field_group(array(
            'key' => 'group_67050adcdfbbe',
            'title' => 'Team member options',
            'fields' => array(
                array(
                    'key' => 'field_67050add1481b',
                    'label' => 'Author',
                    'name' => 'author',
                    'type' => 'taxonomy',
                    'taxonomy' => 'author',
                    'return_format' => 'id',
                    'field_type' => 'select',
                    'allow_null' => 1,
                ),
                array(
                    'key' => 'field_67050b0d1481c',
                    'label' => 'Job Title',
                    'name' => 'job_title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_67050b311481e',
                    'label' => 'Linkedin',
                    'name' => 'linkedin',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_67050b391481f',
                    'label' => 'X (Twitter)',
                    'name' => 'x_twitter',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_67acc3aa3bd5a',
                    'label' => 'Bluesky',
                    'name' => 'bluesky',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_67050b4214820',
                    'label' => 'Other Socials',
                    'name' => 'other_socials',
                    'type' => 'text',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'team',
                    ),
                ),
            ),
            'style' => 'default',
            'active' => true,
        ));
    }

    /**
     * Creates the post type.
     */
    public function create_post_type() {
        register_post_type(
            $this->name,
            [
                'labels' => [
                    'name'                  => __( 'Team members', 'ltm' ),
                    'singular_name'         => __( 'Team member', 'ltm' ),
                    'add_new'               => __( 'Add New Team member', 'ltm' ),
                    'add_new_item'          => __( 'Add New Team member', 'ltm' ),
                    'edit_item'             => __( 'Edit Team member', 'ltm' ),
                    'new_item'              => __( 'New Team member', 'ltm' ),
                    'view_item'             => __( 'View Team member', 'ltm' ),
                    'view_items'            => __( 'View Team members', 'ltm' ),
                    'search_items'          => __( 'Search Team members', 'ltm' ),
                    'not_found'             => __( 'No Team members found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Team members found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Team member:', 'ltm' ),
                    'all_items'             => __( 'All Team members', 'ltm' ),
                    'archives'              => __( 'Team member Archives', 'ltm' ),
                    'attributes'            => __( 'Team member Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Team member', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Team member', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Team members list', 'ltm' ),
                    'items_list_navigation' => __( 'Team members list navigation', 'ltm' ),
                    'items_list'            => __( 'Team members list', 'ltm' ),
                    'menu_name'             => __( 'Team members', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-groups',
                'public'        => true,
                'map_meta_cap'  => true,
                'has_archive'   => false,
                'show_ui'       => true,
                'show_in_rest'  => true,
                'exclude_from_search' => true,
                'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
            ]
        );
    }
}
