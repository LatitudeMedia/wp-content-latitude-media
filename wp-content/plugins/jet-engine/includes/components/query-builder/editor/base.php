<?php
namespace Jet_Engine\Query_Builder\Query_Editor;

abstract class Base_Query {

	/**
	 * Leave empty constructor in case of adding custom functionality in the future
	 * to avoid break children classes, where constructor is not empty
	 */
	public function __construct() {
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 * @return [type] [description]
	 */
	public function editor_component_name() {
		return null;
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * @return [type] [description]
	 */
	public function editor_component_template() {
		return null;
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * @return [type] [description]
	 */
	public function editor_component_file() {
		return null;
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_data() {
		return null;
	}

	/**
	 * Qery type ID
	 */
	abstract public function get_id();

	/**
	 * Qery type name
	 */
	abstract public function get_name();

}
