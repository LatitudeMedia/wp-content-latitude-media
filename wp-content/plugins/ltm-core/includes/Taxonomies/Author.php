<?php
namespace LTMCore\Taxonomies;

/**
 * ACF fields for the `author` taxonomy.
 *
 * The taxonomy itself is registered by the PublishPress Authors plugin, so
 * this class only attaches the "Author options" field group to it.
 *
 * @package LTMCore
 */

/**
 * Class for the author taxonomy's fields.
 */
class Author {

    /**
     * Constructor.
     */
    public function __construct() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            add_action( 'admin_notices', array( $this, 'missing_acf_notice' ) );
            return;
        }

        add_action( 'acf/include_fields', array( $this, 'register_field_group' ) );
    }

    /**
     * Warns in wp-admin that the author fields need ACF Pro active.
     */
    public function missing_acf_notice() {
        echo '<div class="notice notice-warning"><p>' .
            esc_html__( 'Latitude Media Core: the Author options fields require Advanced Custom Fields Pro to be active.', 'ltm' ) .
            '</p></div>';
    }

    /**
     * Registers the "Author options" field group. Copied verbatim from the
     * theme's acf-export.php — the group key and every field key are
     * unchanged so existing author data keeps resolving.
     */
    public function register_field_group() {
        acf_add_local_field_group(array(
            'key' => 'group_66fbe0fc57909',
            'title' => 'Author options',
            'fields' => array(
                array(
                    'key' => 'field_66fbe0fc3ae2d',
                    'label' => 'Author type',
                    'name' => 'author_type',
                    'type' => 'select',
                    'choices' => array(
                        'contributor' => 'Contributor',
                        'podcast-host' => 'Podcast Host',
                        'latitude-media-staff' => 'Latitude Media Staff',
                    ),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 1,
                    'allow_null' => 1,
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_66fbe53bed04e',
                    'label' => 'Linkedin',
                    'name' => 'linkedin',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_66fbe547ed04f',
                    'label' => 'X (Twitter)',
                    'name' => 'x_twitter',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_67acc3d28e7b8',
                    'label' => 'Bluesky',
                    'name' => 'bluesky',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_66fbe5c1ed050',
                    'label' => 'Other Social',
                    'name' => 'other_social',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_66fd24eac1d37',
                    'label' => 'LinkOut',
                    'name' => 'linkout',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_66fd25d0c1d38',
                    'label' => 'Logo',
                    'name' => 'logo',
                    'type' => 'image',
                    'return_format' => 'array',
                    'library' => 'all',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_67375ad57f5ca',
                    'label' => 'Bio',
                    'name' => 'bio',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'author',
                    ),
                ),
            ),
            'style' => 'default',
            'active' => true,
        ));
    }
}
