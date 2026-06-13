<?php
namespace Jet_Engine\Query_Builder\Query_Gateway;

class Dynamic_CSS_Manager {

	public function __construct() {
		add_filter( 'jet-tabs/widgets/template_content', array( $this, 'jet_tabs_compatibility' ), 10, 4 );
		add_filter( 'jet-elements/widgets/template_content', array( $this, 'jet_elements_compatibility' ), 10, 3 );
	}

	public function jet_tabs_compatibility( $content, $item, $css_id, $widget ) {
		if ( empty( $item['_jet_engine_queried_object'] ) || empty( $item['item_template_id'] ) ) {
			return $content;
		}

		if ( ! class_exists( 'Jet_Engine_Elementor_Dynamic_CSS' ) ) {
			return $content;
		}

		$template_id = apply_filters( 'jet-tabs/widgets/template_id', $item['item_template_id'] );

		$dcss = new \Jet_Engine_Elementor_Dynamic_CSS( $template_id, $template_id );
		$dcss->set_listing_unique_selector(
			sprintf(
				'%s #%s',
				$widget->get_unique_selector(),
				$css_id
			)
		);

		$css = $dcss->get_content();

		if ( empty( $css ) ) {
			return $content;
		}

		return sprintf( '<style>%s</style>', $css ) . $content;
	}

	public function jet_elements_compatibility( $content, $item, $template_id ) {
		if ( empty( $item['_jet_engine_queried_object'] ) || empty( $item['unique_item_id'] ) ) {
			return $content;
		}

		if ( ! class_exists( 'Jet_Engine_Elementor_Dynamic_CSS' ) ) {
			return $content;
		}

		$dcss = new \Jet_Engine_Elementor_Dynamic_CSS( $template_id, $template_id );
		$dcss->set_listing_unique_selector(
			sprintf(
				'[data-unique-item-id="%s"]',
				$item['unique_item_id']
			)
		);

		$css = $dcss->get_content();

		if ( empty( $css ) ) {
			return $content;
		}

		return sprintf( '<style>%s</style>', $css ) . $content;
	}

}
