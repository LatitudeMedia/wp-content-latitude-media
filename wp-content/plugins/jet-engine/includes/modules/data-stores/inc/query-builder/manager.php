<?php
namespace Jet_Engine\Modules\Data_Stores\Query_Builder;

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

	public $slug = 'data-stores-query';

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
	 * Register editor component for the query builder
	 *
	 * @param  $manager
	 *
	 * @return void
	 */
	public function register_editor_component( $manager ) {
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/query-builder/editor.php' );
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

		require_once jet_engine()->modules->modules_path( 'data-stores/inc/query-builder/query-result.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/query-builder/query.php' );
		$type  = $this->slug;
		$class = __NAMESPACE__ . '\Data_Stores_Query';

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

		$current_query = $query->get_current_query();

		if ( ! $current_query || ! is_object( $current_query ) ) {
			return $props;
		}

		$final_query = $current_query->get_final_query();

		if ( empty( $final_query )
			|| ! is_array( $final_query )
			|| empty( $final_query['_query_type'] )
		) {
			return $props;
		}

		$props['query_type'] = $final_query['_query_type'];

		if ( ! empty( $final_query['content_type'] ) ) {
			$props['query_meta'] = [
				'content_type' => $final_query['content_type'],
			];
		}

		return $props;
	}
}
