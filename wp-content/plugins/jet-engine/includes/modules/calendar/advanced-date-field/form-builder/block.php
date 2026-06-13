<?php
namespace Jet_Engine\Dynamic_Calendar\Advanced_Date_Field;

use Jet_Form_Builder\Blocks\Types\Base;
use Jet_Form_Builder\Blocks\Native_Block_Wrapper_Attributes;

class Block extends Base implements Native_Block_Wrapper_Attributes {

	/**
	 * Base path
	 *
	 * @var string
	 */
	public $base_path;

	/**
	 * Base URL
	 *
	 * @var string
	 */
	public $base_url;

	public function __construct( $base_path, $base_url ) {
		$this->base_path = $base_path;
		$this->base_url  = $base_url;
	}

	/**
	 * Returns block name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return 'advanced-date-field';
	}

	public function get_field_template( $path ) {
		return $this->base_path . 'templates/' . $path;
	}

	public function get_path_metadata_block() {
		return $this->base_path . $this->get_name();
	}

	public function render_instance() {
		$this->enqueue_scripts();
		return new Block_Render( $this );
	}

	/**
	 * Returns current block render instance
	 *
	 * @param null $wp_block
	 *
	 * @return string
	 */
	public function get_block_renderer( $wp_block = null ) {
		return $this->get_template();
	}

	/**
	 * Returns rendered block template
	 *
	 * @return string
	 */
	public function get_template() {
		return $this->render_instance()
			->set_up()
			->set_base_path( $this->base_path )
			->complete_render();
	}

	private function enqueue_scripts() {
		wp_enqueue_script(
			$this->get_name() . '-public',
			$this->base_url . 'js/jet-form-builder-public.js',
			array( 'jet-form-builder-frontend-forms', 'jet-plugins' ),
			jet_engine()->get_version(),
			true
		);
	}
}