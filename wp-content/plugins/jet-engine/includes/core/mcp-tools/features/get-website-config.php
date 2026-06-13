<?php
namespace Jet_Engine\MCP_Tools;

class Feature_Get_Website_Config extends Feature_Get_Configuration {

	public function __construct() {
		Registry::instance()->add_feature( 'get-website-config', array(
			'type' => 'resource',
			'label' => 'Get Website Config',
			'description' => 'Retrieve information about registered post types, taxonomies, and active plugins in JSON format.',
			'input_schema' => array(
				'parts' => array(
					'type' => 'object',
					'description' => 'Specific parts of the website configuration to retrieve. If empty or not provided, all sections will be returned.',
					'properties' => array(
						'post_types' => array(
							'type' => 'boolean',
							'description' => 'Include registered post types.',
						),
						'taxonomies' => array(
							'type' => 'boolean',
							'description' => 'Include registered taxonomies.',
						),
						'active_plugins' => array(
							'type' => 'boolean',
							'description' => 'Include active plugins.',
						),
					),
				),
			),
			'output_schema' => array(
				'success' => array(
					'type' => 'boolean',
					'description' => 'Indicates whether the data was collected successfully.',
				),
				'post_types' => array(
					'type' => 'array',
					'description' => 'Registered post types.',
					'items' => array( 'type' => 'object' ),
				),
				'taxonomies' => array(
					'type' => 'array',
					'description' => 'Registered taxonomies.',
					'items' => array( 'type' => 'object' ),
				),
				'active_plugins' => array(
					'type' => 'array',
					'description' => 'Active plugins and their metadata.',
					'items' => array( 'type' => 'object' ),
				),
			),
			'execute_callback' => array( $this, 'callback' ),
		) );
	}

	public function callback( $input = array() ) {

		$parts = $this->prepare_parts( $input );

		$sections = array(
			'post_types'     => 'get_post_types',
			'taxonomies'     => 'get_taxonomies',
			'active_plugins' => 'get_active_plugins',
		);

		$result = array( 'success' => true );

		foreach ( $sections as $section => $callback ) {
			if ( empty( $parts ) || ! empty( $parts[ $section ] ) ) {
				$result[ $section ] = call_user_func( array( $this, $callback ) );
			} else {
				$result[ $section ] = array();
			}
		}

		return $result;
	}

	private function prepare_parts( $input ) {
		if ( empty( $input ) || ! is_array( $input ) ) {
			return array();
		}

		if ( empty( $input['parts'] ) || ! is_array( $input['parts'] ) ) {
			return array();
		}

		$parts = array();

		foreach ( $input['parts'] as $section => $value ) {
			if ( $value ) {
				$parts[ $section ] = true;
			}
		}

		return $parts;
	}

	private function get_post_types() {

		if ( ! function_exists( 'get_post_types' ) ) {
			return array();
		}

		$post_types = get_post_types( array(), 'objects' );

		if ( empty( $post_types ) ) {
			return array();
		}

		$prepared_post_types = array();

		foreach ( $post_types as $post_type ) {
			$prepared_post_types[] = array(
				'name' => $post_type->name,
				'label' => $post_type->label,
				'description' => $post_type->description,
				'public' => $post_type->public,
				'supports' => isset( $post_type->supports ) ? $post_type->supports : array(),
				'taxonomies' => isset( $post_type->taxonomies ) ? $post_type->taxonomies : array(),
				'has_archive' => $post_type->has_archive,
				'rewrite' => $post_type->rewrite,
				'args' => isset( $post_type->args ) ? $post_type->args : array(),
			);
		}

		return $prepared_post_types;
	}

	private function get_taxonomies() {

		if ( ! function_exists( 'get_taxonomies' ) ) {
			return array();
		}

		$taxonomies = get_taxonomies( array(), 'objects' );

		if ( empty( $taxonomies ) ) {
			return array();
		}

		$prepared_taxonomies = array();

		foreach ( $taxonomies as $taxonomy ) {
			$prepared_taxonomies[] = array(
				'name' => $taxonomy->name,
				'object_type' => $taxonomy->object_type,
				'label' => $taxonomy->label,
				'description' => $taxonomy->description,
				'hierarchical' => $taxonomy->hierarchical,
				'public' => $taxonomy->public,
				'rewrite' => $taxonomy->rewrite,
				'args' => isset( $taxonomy->args ) ? $taxonomy->args : array(),
			);
		}

		return $prepared_taxonomies;
	}

	private function get_active_plugins() {
		$active_plugins = array();

		if ( function_exists( 'get_option' ) ) {
			$active_plugins = (array) get_option( 'active_plugins', array() );
		}

		if ( function_exists( 'is_multisite' ) && is_multisite() && function_exists( 'get_site_option' ) ) {
			$network_active = (array) get_site_option( 'active_sitewide_plugins', array() );
			$active_plugins = array_merge( $active_plugins, array_keys( $network_active ) );
		}

		$active_plugins = array_values( array_unique( $active_plugins ) );

		if ( empty( $active_plugins ) ) {
			return array();
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			if ( defined( 'ABSPATH' ) && file_exists( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
		}

		$all_plugins = function_exists( 'get_plugins' ) ? get_plugins() : array();
		$result = array();

		foreach ( $active_plugins as $plugin_file ) {
			if ( isset( $all_plugins[ $plugin_file ] ) ) {
				$data = $all_plugins[ $plugin_file ];
				$result[] = array(
					'plugin_file' => $plugin_file,
					'name' => isset( $data['Name'] ) ? $data['Name'] : '',
					'version' => isset( $data['Version'] ) ? $data['Version'] : '',
					'author' => isset( $data['Author'] ) ? $data['Author'] : '',
					'plugin_uri' => isset( $data['PluginURI'] ) ? $data['PluginURI'] : '',
					'description' => isset( $data['Description'] ) ? $data['Description'] : '',
				);
			} else {
				$result[] = array(
					'plugin_file' => $plugin_file,
					'name' => '',
					'version' => '',
					'author' => '',
					'plugin_uri' => '',
					'description' => '',
				);
			}
		}

		return $result;
	}
}
