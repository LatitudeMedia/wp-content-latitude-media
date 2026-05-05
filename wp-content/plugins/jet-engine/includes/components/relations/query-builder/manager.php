<?php
namespace Jet_Engine\Relations\Query_Builder;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	public static $instance = null;

	public $slug = 'relations-query';

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action(
			'jet-engine/query-builder/query-editor/register',
			array( $this, 'register_editor_component' )
		);

		add_action(
			'jet-engine/query-builder/queries/register',
			array( $this, 'register_query' )
		);

		add_filter(
			'jet-engine/query-builder/set-props',
			array( $this, 'adjust_query_type_for_filters' ), 0, 4
		);
	}

	/**
	 * Adjust query type for the filters request
	 *
	 * @param  array  $props
	 * @param  string $query_id
	 * @param  object $query
	 *
	 * @return array
	 */
	public function adjust_query_type_for_filters( $props, $provider, $query_id, $query ) {

		if ( $this->slug !== $query->get_query_type() ) {
			return $props;
		}

		$query_args = ! empty( $query->query ) ? $query->query : array();

		if ( empty( $query_args ) ) {
			return $props;
		}

		$rel_id = isset( $query_args['rel_id'] ) ? $query_args['rel_id'] : false;

		if ( ! $rel_id ) {
			return $props;
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return $props;
		}

		if ( ! $relation ) {
			return $props;
		}

		$rel_object = isset( $query_args['rel_object'] ) ? $query_args['rel_object'] : 'child_object';

		$queried_type =$relation->get_object_type_for( $rel_object );
		$object_name = $relation->get_object_name_for( $rel_object );

		if ( ! $queried_type ) {
			return $props;
		}

		$query_type = $queried_type->get_query_type();

		if ( 'mix' === $query_type ) {
			$query_type = $object_name;
		}

		$props['query_type'] = $query_type;
		$props['query_meta'] = [
			'content_type' => $object_name,
		];

		return $props;
	}

	/**
	 * Register editor component for the query builder
	 *
	 * @param  $manager
	 *
	 * @return void
	 */
	public function register_editor_component( $manager ) {
		require_once jet_engine()->relations->component_path( 'query-builder/editor.php' );
		$manager->register_type( new Query_Editor() );
	}

	/**
	 * Register query class
	 *
	 * @param  $manager
	 *
	 * @return void
	 */
	public function register_query( $manager ) {

		require_once jet_engine()->relations->component_path( 'query-builder/query.php' );
		$type  = $this->slug;
		$class = __NAMESPACE__ . '\Relations_Query';

		$manager::register_query( $type, $class );
	}

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

}
