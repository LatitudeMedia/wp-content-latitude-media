<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Data_Render {

	use \Jet_Engine_Setup_Listing_Trait;

	private static $instance;
	private $attributes = [];
	private $called_scripts = [];

	/**
	 * Class instance.
	 *
	 * @return Data_Render
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get allowed data types to render
	 *
	 * @param string $format Result format.
	 * @return array
	 */
	public function get_data_types( $format = 'elementor' ) {
		$allowed_types = apply_filters(
			'jet-engine/compatibility/woocommerce/data-render/types',
			[
				'hook'              => __( 'Hook', 'jet-engine' ),
				'template_function' => __( 'Template Function', 'jet-engine' ),
				// 'template_part' => __( 'Template Part', 'jet-engine' ), Disabled for now
			]
		);

		return $this->get_formatted_result( $allowed_types, $format );
	}

	/**
	 * Get allowed hooks to render
	 *
	 * @param string $format Result format.
	 * @return array
	 */
	public function get_allowed_hooks( $format = 'elementor' ) {
		$allowed_hooks = apply_filters(
			'jet-engine/compatibility/woocommerce/data-render/hooks',
			[
				'woocommerce_before_shop_loop_item' => __( 'Before product', 'jet-engine' ),
				'woocommerce_before_shop_loop_item_title' => __( 'Before product title', 'jet-engine' ),
				'woocommerce_shop_loop_item_title' => __( 'Product title', 'jet-engine' ),
				'woocommerce_after_shop_loop_item_title' => __( 'After product title', 'jet-engine' ),
				'woocommerce_after_shop_loop_item' => __( 'After product', 'jet-engine' ),
			]
		);

		return $this->get_formatted_result( $allowed_hooks, $format );
	}

	/**
	 * Get allowed template functions to render
	 *
	 * @param string $format Result format.
	 * @return array
	 */
	public function get_allowed_template_functions( $format = 'elementor' ) {

		$allowed_functions = $this->get_allowed_template_functions_data();
		$raw_functions = [];

		foreach ( $allowed_functions as $key => $value ) {
			if ( is_array( $value ) ) {
				$raw_functions[ $key ] = $value['label'];
			} else {
				$raw_functions[ $key ] = $value;
			}
		}

		return $this->get_formatted_result( $raw_functions, $format );
	}

	/**
	 * Get allowed template functions to render with link
	 *
	 * @return array
	 */
	public function get_functions_with_link_allowed() {

		$allowed_functions = $this->get_allowed_template_functions_data();
		$raw_functions = [];

		foreach ( $allowed_functions as $key => $value ) {
			if ( is_array( $value ) && ! empty( $value['allow_link'] ) ) {
				$raw_functions[] = $key;
			}
		}

		return $raw_functions;
	}

	/**
	 * Get allowed template functions to call in mixed format
	 * - just funciton label if there is no additional data
	 * - array with label and any other data related to this function.
	 *
	 * @return array
	 */
	public function get_allowed_template_functions_data() {
		return apply_filters(
			'jet-engine/compatibility/woocommerce/data-render/template-functions',
			[
				'woocommerce_template_loop_add_to_cart' => __( 'Add to cart', 'jet-engine' ),
				'woocommerce_quantity_input' => [
					'label'   => __( 'Quantity input', 'jet-engine' ),
					'scripts' => [ $this, 'quantity_input_scripts' ],
				],
				'woocommerce_template_loop_product_thumbnail' => [
					'label' => __( 'Product thumbnail', 'jet-engine' ),
					'allow_link' => true,
				],
				'woocommerce_template_loop_product_title' => [
					'label' => __( 'Product title', 'jet-engine' ),
					'allow_link' => true,
				],
				'woocommerce_template_loop_price' => __( 'Product price', 'jet-engine' ),
				'woocommerce_template_loop_rating' => __( 'Product rating', 'jet-engine' ),
				'woocommerce_show_product_loop_sale_flash' => __( 'Product sale flash', 'jet-engine' ),
				'woocommerce_template_single_meta' => __( 'Product meta', 'jet-engine' ),
			]
		);
	}

	/**
	 * Get allowed template parts to render
	 *
	 * @param string $format Result format.
	 * @return array
	 */
	public function get_allowed_template_parts( $format = 'elementor' ) {

		$allowed_templates = apply_filters(
			'jet-engine/compatibility/woocommerce/data-render/template-parts',
			[
				'content::product' => __( 'Content product', 'jet-engine' ),
				'content::single-product' => __( 'Content single product', 'jet-engine' ),
			]
		);

		return $this->get_formatted_result( $allowed_templates, $format );
	}

	/**
	 * Get formatted result
	 *
	 * @param array  $input  Input data.
	 * @param string $format Result format.
	 * @return array
	 */
	public function get_formatted_result( $input = [], $format = 'elementor' ) {

		$result = [];

		if ( 'elementor' !== $format ) {
			foreach ( $input as $key => $value ) {
				$result[] = [
					'value' => $key,
					'label' => $value,
				];
			}
		} else {
			$result = $input;
		}

		return $result;
	}

	/**
	 * Process given render action accroding attributes.
	 *
	 * @param  array $attributes
	 * @return void
	 */
	public function process( $attributes = [] ) {

		do_action( 'jet-engine/woocommerce/data-render/before-run', $attributes );

		global $product;

		if ( ! $product || ! $product instanceof \WC_Product ) {

			$current_object = jet_engine()->listings->data->get_current_object();

			if ( $current_object && $current_object instanceof \WC_Product ) {
				$product = $current_object;
			} elseif ( $current_object && $current_object instanceof \WP_Post ) {
				$product = wc_get_product( $current_object->ID );
			}
		}

		if ( ! $product || ! $product instanceof \WC_Product ) {
			return;
		}

		do_action( 'jet-engine/woocommerce/data-render/before-render', $attributes );

		$type = ! empty( $attributes['data_type'] ) ? $attributes['data_type'] : 'hook';

		switch ( $type ) {
			case 'hook':
				$this->render_hook( $attributes );
				break;
			case 'template_function':
				$this->render_template_function( $attributes );
				break;
			case 'template_part':
				$this->render_template_part( $attributes );
				break;
			default:
				break;
		}

		do_action( 'jet-engine/woocommerce/data-render/after-render', $attributes );
	}

	/**
	 * Get content.
	 *
	 * @param  array $attributes
	 * @return string
	 */
	public function get_content( $attributes = [] ) {
		if ( empty( $attributes ) ) {
			$attributes = $this->attributes;
		}

		ob_start();
		$this->process( $attributes );
		$content = ob_get_clean();

		return $content;
	}

	/**
	 * Render hook.
	 *
	 * @param  array $settings
	 * @return void
	 */
	public function render_hook( $settings = [] ) {

		if ( ! empty( $settings['hook_name'] ) ) {

			$hook_name      = $settings['hook_name'];
			$allowed_hooks  = $this->get_allowed_hooks();
			$core_callbacks = isset( $settings['core_callbacks'] ) ? $settings['core_callbacks'] : false;

			$core_callbacks_list = [
				'woocommerce_template_loop_product_link_open',
				'woocommerce_show_product_loop_sale_flash',
				'woocommerce_template_loop_product_thumbnail',
				'woocommerce_template_loop_product_title',
				'woocommerce_template_loop_rating',
				'woocommerce_template_loop_price',
				'woocommerce_template_loop_product_link_close',
				'woocommerce_template_loop_add_to_cart',
			];

			if ( ! empty( $allowed_hooks[ $hook_name ] ) ) {

				$restore_callbacks = [];

				if ( ! $core_callbacks ) {
					foreach ( $core_callbacks_list as $callback ) {
						global $wp_filter;
						if ( ! empty( $wp_filter[ $hook_name ]->callbacks ) ) {
							foreach ( $wp_filter[ $hook_name ]->callbacks as $priority => $callbacks ) {
								if ( isset( $callbacks[ $callback ] ) ) {
									$restore_callbacks[] = [
										'callback' => $callback,
										'priority' => $priority,
									];

									remove_action( $hook_name, $callback, $priority );
								}
							}
						}
					}
				}

				do_action( $hook_name );

				if ( ! $core_callbacks ) {
					foreach ( $restore_callbacks as $callback ) {
						add_action( $hook_name, $callback['callback'], $callback['priority'] );
					}
				}
			}
		}
	}

	/**
	 * Call template function.
	 *
	 * @param array $settings
	 * @return void
	 */
	public function render_template_function( $settings = [] ) {

		if ( ! empty( $settings['template_function'] ) ) {

			$template_function = $settings['template_function'];
			$allowed_functions = $this->get_allowed_template_functions_data();

			if ( ! empty( $allowed_functions[ $template_function ] )
				&& is_callable( $template_function )
			) {

				$function_data = $allowed_functions[ $template_function ];

				if ( ! is_array( $function_data ) ) {
					$function_data = [];
				}

				if (
					! empty( $function_data['scripts'] )
					&& is_callable( $function_data['scripts'] )
					&& ! in_array( $template_function, $this->called_scripts, true )
				) {
					call_user_func( $function_data['scripts'] );
					$this->called_scripts[] = $template_function;
				}

				$add_link = false;

				if (
					! empty( $function_data['allow_link'] )
					&& ! empty( $settings['add_link'] )
				) {
					$add_link = true;
				}

				if ( $add_link ) {
					woocommerce_template_loop_product_link_open();
				}

				call_user_func( $template_function );

				if ( $add_link ) {
					woocommerce_template_loop_product_link_close();
				}
			}
		}
	}

	/**
	 * Call quantity input scripts.
	 *
	 * @return void
	 */
	public function quantity_input_scripts() {
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$( '.quantity' ).each( function() {

					var $this = $( this );
					var $qty = $this.find( '.qty' );

					if ( ! $qty.length ) {
						return;
					}

					var $container = $this.closest( '.jet-listing-grid__item' );

					if ( ! $container.length ) {
						return;
					}

					var $add_to_cart = $container.find( '.add_to_cart_button' );

					if ( ! $add_to_cart.length ) {
						return;
					}

					$qty.on( 'change keyup', function() {
						var qty = parseInt( $( this ).val(), 10 );

						if ( isNaN( qty ) || qty < 0 ) {
							qty = 1;
						}

						$add_to_cart.attr( 'data-quantity', qty );
						$add_to_cart.data( 'quantity', qty );
					} );
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Call template part.
	 *
	 * @param array $settings
	 * @return void
	 */
	public function render_template_part( $settings = [] ) {

		if ( ! empty( $settings['template_part'] ) ) {

			$template_part     = $settings['template_part'];
			$allowed_templates = $this->get_allowed_template_parts();

			if ( ! empty( $allowed_templates[ $template_part ] ) ) {
				$template_part_data = explode( '::', $template_part );
				wc_get_template_part( $template_part_data[0], $template_part_data[1] );
			}
		}
	}

	/**
	 * Set render attributes.
	 * Required for blocks compatibility to avoid rewriting whole block render method.
	 *
	 * @param array $attributes Attributes.
	 * @return self
	 */
	public function set_attributes( $attributes = [] ) {
		$this->attributes = $attributes;
		return $this;
	}
}
