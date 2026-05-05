<?php
namespace Jet_Engine\Relations\Query_Builder;

class Query_Editor extends \Jet_Engine\Query_Builder\Query_Editor\Base_Query {

	/**
	 * Query type ID
	 */
	public function get_id() {
		return Manager::instance()->slug;
	}

	/**
	 * Query type name
	 */
	public function get_name() {
		return __( 'Relations Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 *
	 * @return string
	 */
	public function editor_component_name() {
		return 'jet-relations-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return mixed|void
	 */
	public function editor_component_data() {
		return apply_filters( 'jet-engine/query-builder/types/relations-query/data', [
			'relations' => jet_engine()->relations->get_relations_for_js(
				false,
				esc_html__( 'Select relation...', 'jet-engine' )
			),
			'sources' => jet_engine()->relations->sources->get_sources_for_js(),
		] );
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return false|string
	 */
	public function editor_component_template() {
		ob_start();
		include jet_engine()->relations->component_path( 'templates/query-editor.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return string
	 */
	public function editor_component_file() {
		return jet_engine()->relations->component_url( 'assets/js/query-editor.js' );
	}
}
