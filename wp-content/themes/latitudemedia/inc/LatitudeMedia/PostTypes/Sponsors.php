<?php
namespace LatitudeMedia\PostTypes;

use LatitudeMedia\Taxonomies\PostSponsor;

/**
 * Custom post type for Sponsors
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Sponsors post type.
 */
class Sponsors {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'sponsors';

    /**
     * Constructor.
     */
    public function __construct() {
        // Create the post type
        add_action( 'init', array( $this, 'create_post_type' ) );

        // Keep the sponsor taxonomy term in sync with sponsors posts
        add_action( 'save_post_' . $this->name, array( $this, 'sync_taxonomy_term' ), 10, 2 );
        add_action( 'before_delete_post', array( $this, 'delete_taxonomy_term' ) );

        // Read-only list of content linked to this sponsor
        add_action( 'add_meta_boxes_' . $this->name, array( $this, 'add_sponsored_content_meta_box' ) );

        // One-time backfill so sponsors created before this relationship existed get a term too.
        add_action( 'init', array( $this, 'backfill_sponsor_terms' ), 20 );

        // "Sponsored" column on the Posts list table
        add_filter( 'manage_edit-post_columns', array( $this, 'add_sponsored_column' ) );
        add_filter( 'manage_posts_custom_column', array( $this, 'render_sponsored_column' ), 10, 2 );
    }

    /**
     * Creates the post type.
     */
    public function create_post_type() {
        register_post_type(
            $this->name,
            [
                'labels' => [
                    'name'                  => __( 'Sponsors', 'ltm' ),
                    'singular_name'         => __( 'Sponsor', 'ltm' ),
                    'add_new'               => __( 'Add New Sponsor', 'ltm' ),
                    'add_new_item'          => __( 'Add New Sponsor', 'ltm' ),
                    'edit_item'             => __( 'Edit Sponsor', 'ltm' ),
                    'new_item'              => __( 'New Sponsor', 'ltm' ),
                    'view_item'             => __( 'View Sponsor', 'ltm' ),
                    'view_items'            => __( 'View Sponsors', 'ltm' ),
                    'search_items'          => __( 'Search Sponsors', 'ltm' ),
                    'not_found'             => __( 'No Sponsors found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Sponsors found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Sponsor:', 'ltm' ),
                    'all_items'             => __( 'All Sponsors', 'ltm' ),
                    'archives'              => __( 'Sponsor Archives', 'ltm' ),
                    'attributes'            => __( 'Sponsor Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Sponsor', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Sponsor', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Sponsors list', 'ltm' ),
                    'items_list_navigation' => __( 'Sponsors list navigation', 'ltm' ),
                    'items_list'            => __( 'Sponsors list', 'ltm' ),
                    'menu_name'             => __( 'Sponsors', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-money-alt',
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

    /**
     * Creates or updates the sponsor term derived from this post.
     */
    public function sync_taxonomy_term( $post_id, $post ) {
        if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( in_array( $post->post_status, [ 'auto-draft', 'trash' ], true ) ) {
            return;
        }

        $taxonomy = new PostSponsor();
        $term_id  = $this->get_synced_term_id( $post_id, $taxonomy->name );

        if ( $term_id ) {
            wp_update_term(
                $term_id,
                $taxonomy->name,
                [
                    'name' => $post->post_title,
                    'slug' => $post->post_name,
                ]
            );
            return;
        }

        $result = wp_insert_term(
            $post->post_title,
            $taxonomy->name,
            [ 'slug' => $post->post_name ]
        );

        if ( ! is_wp_error( $result ) ) {
            add_term_meta( $result['term_id'], '_sponsor_post_id', $post_id, true );
        }
    }

    /**
     * Syncs a taxonomy term for every existing sponsor, once per environment.
     */
    public function backfill_sponsor_terms() {
        if ( get_option( 'ltm_sponsor_terms_synced' ) ) {
            return;
        }

        $sponsor_ids = get_posts(
            [
                'post_type'      => $this->name,
                'post_status'    => [ 'publish', 'draft', 'pending', 'private', 'future' ],
                'numberposts'    => -1,
                'fields'         => 'ids',
            ]
        );

        foreach ( $sponsor_ids as $sponsor_id ) {
            $this->sync_taxonomy_term( $sponsor_id, get_post( $sponsor_id ) );
        }

        update_option( 'ltm_sponsor_terms_synced', 1, false );
    }

    /**
     * Removes the synced sponsor term when the source sponsor is permanently deleted.
     */
    public function delete_taxonomy_term( $post_id ) {
        if ( get_post_type( $post_id ) !== $this->name ) {
            return;
        }

        $taxonomy = new PostSponsor();
        $term_id  = $this->get_synced_term_id( $post_id, $taxonomy->name );

        if ( $term_id ) {
            wp_delete_term( $term_id, $taxonomy->name );
        }
    }

    /**
     * Finds the sponsor term linked to a sponsor post via term meta.
     */
    private function get_synced_term_id( $post_id, $taxonomy_name ) {
        $terms = get_terms(
            [
                'taxonomy'   => $taxonomy_name,
                'hide_empty' => false,
                'meta_key'   => '_sponsor_post_id',
                'meta_value' => $post_id,
                'fields'     => 'ids',
            ]
        );

        return ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0] : 0;
    }

    /**
     * Registers the "Content sponsored preview" meta box.
     */
    public function add_sponsored_content_meta_box() {
        add_meta_box(
            'ltm_sponsored_content',
            __( 'Content sponsored preview', 'ltm' ),
            array( $this, 'render_sponsored_content_meta_box' ),
            $this->name,
            'side',
            'default'
        );
    }

    /**
     * Renders a read-only list of posts linked to this sponsor.
     */
    public function render_sponsored_content_meta_box( $post ) {
        $taxonomy = new PostSponsor();
        $term_id  = $this->get_synced_term_id( $post->ID, $taxonomy->name );

        $sponsored_posts = $term_id ? get_posts(
            [
                'post_type'   => 'post',
                'post_status' => 'any',
                'numberposts' => -1,
                'orderby'     => 'title',
                'order'       => 'ASC',
                'tax_query'   => [
                    [
                        'taxonomy' => $taxonomy->name,
                        'field'    => 'term_id',
                        'terms'    => $term_id,
                    ],
                ],
            ]
        ) : [];

        if ( empty( $sponsored_posts ) ) {
            echo '<p>' . esc_html__( 'No content linked yet.', 'ltm' ) . '</p>';
            return;
        }

        echo '<ul class="ltm-sponsored-content-list">';
        foreach ( $sponsored_posts as $sponsored_post ) {
            printf(
                '<li><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></li>',
                esc_url( get_edit_post_link( $sponsored_post->ID ) ),
                esc_html( get_the_title( $sponsored_post ) )
            );
        }
        echo '</ul>';
    }

    /**
     * Adds the "Sponsored" column to the Posts list table.
     */
    public function add_sponsored_column( $columns ) {
        $columns['sponsored'] = __( 'Sponsored', 'ltm' );
        return $columns;
    }

    /**
     * Renders the "Sponsored" column: linked sponsor's title, legacy toggle, or "Not sponsored".
     */
    public function render_sponsored_column( $column_name, $post_id ) {
        if ( $column_name !== 'sponsored' ) {
            return;
        }

        $sponsor = get_post_sponsor( $post_id );

        if ( $sponsor ) {
            $label = $sponsor->post_title;
        } elseif ( get_field( 'sponsored', $post_id ) ) {
            $label = __( 'Yes (legacy)', 'ltm' );
        } else {
            $label = __( 'Not sponsored', 'ltm' );
        }

        printf( '<span>%s</span>', esc_html( $label ) );
    }
}
