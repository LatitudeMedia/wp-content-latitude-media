<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Render_Dynamic_Repeater' ) ) {

	class Jet_Engine_Render_Dynamic_Repeater extends Jet_Engine_Render_Base {

		private $show_field = true;

		public function get_name() {
			return 'jet-listing-dynamic-repeater';
		}

		/**
		 * Return saved fields from post/term meta
		 *
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function get_saved_fields( $settings ) {

			$saved = apply_filters( 'jet-engine/listings/dynamic-repeater/pre-get-saved', false, $settings );

			if ( $saved ) {
				return $saved;
			}

			$source         = isset( $settings['dynamic_field_source'] ) ? $settings['dynamic_field_source'] : false;
			$object_context = isset( $settings['object_context'] ) ? $settings['object_context'] : false;

			if ( 'options_page' === $source ) {
				$option = ! empty( $settings['dynamic_field_option'] ) ? $settings['dynamic_field_option'] : false;
				return jet_engine()->listings->data->get_option( $option );
			} else {
				return jet_engine()->listings->data->get_meta_by_context( $source, $object_context );
			}

		}

		/**
		 * Frontend Query Editor compatibility
		 *
		 * @param array $settings Dynamic Repeater settings
		 */
		public function frontend_query_editor( $settings ) {
			if ( ! isset( \Jet_Engine\Query_Builder\Manager::instance()->frontend_editor ) ) {
				return;
			}

			$source = $settings['dynamic_field_source'] ?? '';

			if ( $source !== 'je_query' ) {
				return;
			}

			if ( empty( $settings['je_query_id'] ) ) {
				return;
			}

			\Jet_Engine\Query_Builder\Manager::instance()->frontend_editor->render_edit_buttons( false, $settings['je_query_id'] );
		}

		/**
		 * Get author ID by object
		 * added to fix %author_id% macro in repeater format
		 *
		 * @param int     $author_id  Filtered author ID
		 * @param object  $object     Macros object
		 *
		 * @return int|false          Filtered author ID
		 */
		public function get_author_id( $author_id, $object ) {
			if ( ! is_object( $object ) ) {
				return false;
			}

			if ( is_a( $object, '\WP_Post' ) ) {
				$author_id = $object->post_author;
			} elseif ( is_a( $object, '\WC_Product' ) )  {
				$post = get_post( $object->get_id() );
				$author_id = $post->post_author;
			}

			return $author_id;
		}

		/**
		 * Render field content
		 *
		 * @return [type] [description]
		 */
		public function render_repeater_items( $settings ) {

			global $post;

			$fields = $this->get_saved_fields( $settings );

			if ( empty( $fields ) ) {

				$hide_if_empty = filter_var( $settings['hide_if_empty'] ?? false, FILTER_VALIDATE_BOOLEAN );

				if ( $hide_if_empty ) {
					$this->show_field = false;
				}

				if ( $this->show_field && ! empty( $settings['dynamic_field_fallback'] ) ) {
					echo $settings['dynamic_field_fallback'];
				}

				return;
			}

			$raw_format    = isset( $settings['dynamic_field_format'] ) ? $settings['dynamic_field_format'] : '';
			$format        = isset( $settings['dynamic_field_format'] ) ? wp_kses_post( $settings['dynamic_field_format'] ) : false;
			$delimiter     = isset( $settings['items_delimiter'] ) ? wp_kses_post( $settings['items_delimiter'] ) : false;
			$item_tag      = isset( $settings['item_tag'] ) ? $settings['item_tag'] : 'div';
			$item_tag      = Jet_Engine_Tools::sanitize_html_tag( $item_tag );
			$items_before  = isset( $settings['dynamic_field_before'] ) ? wp_kses_post( $settings['dynamic_field_before'] ) : '';
			$items_after   = isset( $settings['dynamic_field_after'] ) ? wp_kses_post( $settings['dynamic_field_after'] ) : '';
			$is_first      = true;
			$counter       = ! empty( $settings['dynamic_field_counter'] ) ? $settings['dynamic_field_counter'] : false;
			$counter_after = ! empty( $settings['dynamic_field_counter_after'] ) ? wp_kses_post( $settings['dynamic_field_counter_after'] ) : false;
			$leading_zero  = ! empty( $settings['dynamic_field_leading_zero'] ) ? $settings['dynamic_field_leading_zero'] : false;
			$counter_pos   = ! empty( $settings['dynamic_field_counter_position'] ) ? $settings['dynamic_field_counter_position'] : 'at-left';

			$base_class = $this->get_name();

			printf(
				'<div class="%1$s__items %2$s">',
				esc_attr( $base_class ),
				( $counter ? 'has-counter counter--' . esc_attr( $counter_pos ) : '' )
			);

			if ( $items_before ) {
				// Escaped above
				echo $items_before; // phpcs:ignore
			}

			$index = 1;

			if ( ! is_array( $fields ) ) {

				$unserialized = maybe_unserialize( $fields );

				if ( is_array( $unserialized ) ) {
					$fields = $unserialized;
				}

			}

			if ( ! is_array( $fields ) ) {

				$decoded = json_decode( $fields, true );

				if ( is_array( $decoded ) ) {
					$fields = $decoded;
				}

			}

			if ( ! is_array( $fields ) ) {
				$fields = array( $fields );
			}

			$initial_object = jet_engine()->listings->data->get_current_object();

			add_filter( 'jet-engine/listings/macros/author-id', array( $this, 'get_author_id' ), 10, 2 );

			foreach ( $fields as $field ) {

				$item_content = $format;

				if ( is_object( $field ) ) {
					jet_engine()->listings->data->set_current_object( $field );

					//take format that is not stripped of tags, to ensure macros and shortcodes
					$item_content = jet_engine()->listings->macros->do_macros( $raw_format );

					if ( apply_filters( 'jet-engine/listings/dynamic-repeater/allow-shorcodes', false ) ) {
						$item_content = do_shortcode( $item_content );
					}

					//strip content from disallowed tags
					$item_content = wp_kses_post( $item_content );
				}

				$item_content = preg_replace_callback(
					'/\%(([a-zA-Z0-9_-]+)(\|([a-zA-Z0-9\(\)\.\,\:\/\s_-]+))*)\%/',
					function( $matches ) use ( $field ) {

						if ( ! isset( $matches[2] ) ) {
							return $matches[0];
						}

						$field_name = $matches[2];

						if ( is_array( $field ) ) {
							if ( ! isset( $field[ $field_name ] ) ) {
								return $matches[0];
							} else {
								if ( isset( $matches[4] ) ) {
									return jet_engine()->listings->filters->apply_filters(
										$field[ $field_name ], $matches[4]
									);
								} else {
									return $field[ $field_name ];
								}
							}
						} elseif ( is_object( $field ) ) {
							if ( ! isset( $field->$field_name ) ) {
								return $matches[0];
							} else {
								if ( isset( $matches[4] ) ) {
									return jet_engine()->listings->filters->apply_filters(
										$field->$field_name, $matches[4]
									);
								} else {
									return $field->$field_name;
								}
							}
						}

						return $matches[0];

					},
					$item_content
				);

				add_filter( 'wp_kses_allowed_html', [ $this, 'allow_iframe_for_kses' ], 10, 2 );

				$item_content = wp_kses_post( $item_content );

				remove_filter( 'wp_kses_allowed_html', [ $this, 'allow_iframe_for_kses' ] );

				if ( $delimiter && ! $is_first ) {
					printf(
						'<div class="%1$s__delimiter">%2$s</div>',
						esc_attr( $base_class ),
						$delimiter // phpcs:ignore (escaped above with wp_kses_post)
					);
				}

				if ( apply_filters( 'jet-engine/listings/dynamic-repeater/allow-shorcodes', false ) ) {
					$item_content = do_shortcode( $item_content );
				}

				if ( false === strpos( $item_content, '<' ) ) {
					$item_content = '<div>' . $item_content . '</div>';
				}

				if ( $counter ) {

					$counter_html = $index;

					if ( $leading_zero ) {
						$counter_html = zeroise( $counter_html, 2 );
					}

					if ( $counter_after ) {
						$counter_html = $counter_html . $counter_after;
					}

					$item_content = sprintf(
						'<div class="%2$s__counter">%3$s</div><div class="%2$s__body">%1$s</div>',
						$item_content, // phpcs:ignore
						esc_attr( $base_class ),
						wp_kses_post( $counter_html )
					);
				}

				printf(
					'<%3$s class="%1$s__item">%2$s</%3$s>',
					esc_attr( $base_class ),
					$item_content, // phpcs:ignore
					$item_tag // phpcs:ignore
				);

				$is_first = false;
				$index++;

			}

			remove_filter( 'jet-engine/listings/macros/author-id', array( $this, 'get_author_id' ), 10 );

			jet_engine()->listings->data->set_current_object( $initial_object );

			if ( $items_after ) {
				// Escaped above
				echo $items_after; // phpcs:ignore
			}

			echo '</div>';

		}

		public function render() {

			$base_class = $this->get_name();
			$settings   = $this->get_settings();

			$classes = array(
				'jet-listing',
				$base_class,
			);

			if ( ! empty( $settings['className'] ) ) {
				$classes[] = esc_attr( $settings['className'] );
			}

			ob_start();

			$this->frontend_query_editor( $settings );

			printf( '<div class="%1$s">', esc_attr( implode( ' ', $classes ) ) );

				do_action( 'jet-engine/listing/dynamic-repeater/before-field', $this );

				$this->render_repeater_items( $settings );

				do_action( 'jet-engine/listing/dynamic-repeater/after-field', $this );

			echo '</div>';

			$content = ob_get_clean();

			if ( $this->show_field ) {
				// Escaped by parts while rendering
				echo $content; // phpcs:ignore
			}

		}

		/**
		 * Allow iframe temporarily for wp_kses_post()
		 *
		 * @param array  $tags
		 * @param string $context
		 * @return array
		 */
		function allow_iframe_for_kses( $tags, $context ) {

			if ( 'post' === $context ) {
				$tags['iframe'] = [
					'src'             => true,
					'width'           => true,
					'height'          => true,
					'frameborder'     => true,
					'allow'           => true,
					'allowfullscreen' => true,
					'title'           => true,
					'referrerpolicy'  => true,
					'loading'         => true,
				];
			}

			return $tags;
		}

	}

}
