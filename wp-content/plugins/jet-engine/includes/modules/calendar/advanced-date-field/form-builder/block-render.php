<?php
namespace Jet_Engine\Dynamic_Calendar\Advanced_Date_Field;

use Jet_Form_Builder\Blocks\Render\Base;

class Block_Render extends Base {

	protected $base_path;

	/**
	 * Returns block renderer name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'advanced-date-field';
	}

	/**
	 * Set base path
	 *
	 * @param string $base_path
	 * @return $this
	 */
	public function set_base_path( $base_path ) {
		$this->base_path = $base_path;
		return $this;
	}

	/**
	 * Check if we are in the editor preview
	 *
	 * @return boolean
	 */
	public function is_editor_preview() {
		$res = false;

		if ( ! empty( $_GET['context'] )
			&& 'edit' === $_GET['context']
			&& ! empty( $_GET['post_id'] )
		) {
			$res = true;
		}

		return $res;
	}

	/**
	 * Render field
	 *
	 * @return string
	 */
	public function render_field() {

		// Render simplified version for the editor preview
		if ( $this->is_editor_preview() ) {
			return '<div class="jfb-advanced-date">
				<input class="jet-form-builder-advanced-date__input" type="date" style="width:100%" />
				<div>
					<i><small>* Advanced Date field preview</small></i>
				</div>
			</div>';
		}

		$name = ! empty( $this->block_type->block_attrs['name'] ) ? $this->block_type->block_attrs['name'] : '';
		$default = ! empty( $this->block_type->block_attrs['default'] ) ? $this->block_type->block_attrs['default'] : '';
		$name = esc_attr( $name );

		$field_attrs = [
			'name="' . $name . '"',
			'data-field-name="' . $name . '"',
			'value="' . esc_attr( $default ) . '"',
		];

		return sprintf(
			'<div class="jfb-advanced-date">%3$s<input class="jet-form-builder-advanced-date__input" type="hidden" %1$s %2$s data-jfb-sync /></div>',
			implode( ' ', $field_attrs ),
			$this->get_data_attrs_string(),
			$this->get_style()
		);
	}

	/**
	 * Get field CSS styles
	 *
	 * @return string
	 */
	public function get_style() {
		ob_start();
		include $this->base_path . $this->get_name() . '/style.css';
		$styles = ob_get_clean();

		return '<style>' . $styles . '</style>';
	}

	public function get_data_attrs_string() {

		$block_attrs = $this->block_type->block_attrs;

		$attrs = array(
			'data-allow-time' => isset( $block_attrs['allow_timepicker'] ) ? filter_var( $block_attrs['allow_timepicker'], FILTER_VALIDATE_BOOLEAN ) : false,
			'data-format'  => isset( $block_attrs['recurrency_format'] ) ? esc_attr( $block_attrs['recurrency_format'] ) : 'rrule',
		);

		if ( ! empty( $block_attrs['labels'] ) ) {
			$sanitized_labels = array_map( 'esc_html', $block_attrs['labels'] );
			$attrs['data-labels'] = htmlspecialchars( json_encode( $sanitized_labels ) );
		}

		$attrs_string = '';

		foreach ( $attrs as $name => $value ) {
			$attrs_string .= sprintf( ' %1$s="%2$s"', $name, $value );
		}

		return $attrs_string;
	}

	/**
	 * Set up field attributes
	 *
	 * @return $this
	 */
	public function set_up() {
		$this->attrs = $this->block_type->block_attrs;
		return $this;
	}

	/**
	 * Get rendered field template
	 *
	 * @return string
	 */
	public function complete_render() {
		return $this->render(
			null,
			$this->render_field()
		);
	}

}