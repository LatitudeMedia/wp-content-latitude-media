<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Query_Builder;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class CCT_Query_Editor extends \Jet_Engine\Query_Builder\Query_Editor\Base_Query {

	public function __construct() {
		parent::__construct();

		add_action(
			'jet-engine/query-builder/editor/before-print-templates',
			array( $this, 'print_custom_editor_templates' )
		);
	}

	/**
	 * Qery type ID
	 */
	public function get_id() {
		return Manager::instance()->slug;
	}

	/**
	 * Qery type name
	 */
	public function get_name() {
		return __( 'Custom Content Type Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_name() {
		return 'jet-cct-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return [type] [description]
	 */
	public function editor_component_data() {

		$types  = array();
		$fields = array();

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {
			$types[] = array(
				'value' => $type,
				'label' => $instance->get_arg( 'name' ),
			);

			$fields[ $type ] = $instance->get_fields_list( 'all', 'blocks' );

		}

		return array(
			'content_types'    => $types,
			'types_fields'     => $fields,
			'order_by_options' => Module::instance()->manager->get_additional_order_by_options( true ),
		);

	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_template() {
		ob_start();
		include Module::instance()->module_path( 'templates/admin/query-editor.php' );
		return ob_get_clean();
	}

	/**
	 * Print custom editor template for SQL nested fields.
	 *
	 * @return void
	 */
	public function print_custom_editor_templates() {

		ob_start();
		include Module::instance()->module_path( 'templates/admin/query-field.php' );
		$content = ob_get_clean();

		printf( '<script type="text/x-template" id="jet-engine-cct-query-field">%s</script>', $content );
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_file() {
		return Module::instance()->module_url( 'assets/js/admin/query-editor.js' );
	}

}
