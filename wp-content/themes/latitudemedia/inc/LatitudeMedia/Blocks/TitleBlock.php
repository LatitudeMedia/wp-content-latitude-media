<?php
namespace LatitudeMedia\Blocks;

/**
 * Native "Title Block": full-bleed kicker + page title header for Thematic
 * Pages, with an optional sponsor logo when the page is sponsored.
 *
 * @package LatitudeMedia
 */
class TitleBlock {

	/**
	 * Registered block name.
	 *
	 * @var string
	 */
	public $name = 'latitudemedia/title-block';

	/**
	 * Post type this block is restricted to.
	 *
	 * @var string
	 */
	public $post_type = 'thematic-pages';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_end_style' ) );
		add_filter( 'allowed_block_types_all', array( $this, 'restrict_to_thematic_pages' ), 10, 2 );
	}

	/**
	 * Registers the block type, wiring the dynamic render callback.
	 */
	public function register_block() {
		register_block_type(
			get_template_directory() . '/src/blocks/title-block/block.json',
			[
				'render_callback' => array( $this, 'render' ),
			]
		);
	}

	/**
	 * Enqueues the block editor script/style, with explicit core dependencies
	 * since this block isn't built via @wordpress/scripts dependency extraction.
	 */
	public function enqueue_editor_assets() {
		$script_rel_path = '/dist/js/blocks/title-block.min.js';
		$style_rel_path  = '/dist/css/blocks/title-block.min.css';
		$script_path     = get_template_directory() . $script_rel_path;
		$style_path      = get_template_directory() . $style_rel_path;

		if ( file_exists( $script_path ) ) {
			wp_enqueue_script(
				'ltm-title-block-editor',
				get_template_directory_uri() . $script_rel_path,
				[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-core-data', 'wp-i18n' ],
				filemtime( $script_path ),
				true
			);
		}

		if ( file_exists( $style_path ) ) {
			wp_enqueue_style(
				'ltm-title-block-editor-style',
				get_template_directory_uri() . $style_rel_path,
				[],
				filemtime( $style_path )
			);
		}
	}

	/**
	 * Enqueues the block's front-end style, only on pages that use it.
	 */
	public function enqueue_front_end_style() {
		if ( ! has_block( $this->name ) ) {
			return;
		}

		$style_rel_path = '/dist/css/blocks/title-block.min.css';
		$style_path     = get_template_directory() . $style_rel_path;

		if ( file_exists( $style_path ) ) {
			wp_enqueue_style(
				'ltm-title-block',
				get_template_directory_uri() . $style_rel_path,
				[],
				filemtime( $style_path )
			);
		}
	}

	/**
	 * Keeps this block out of the inserter everywhere except Thematic Pages.
	 */
	public function restrict_to_thematic_pages( $allowed_block_types, $editor_context ) {
		if ( empty( $editor_context->post ) || $editor_context->post->post_type === $this->post_type ) {
			return $allowed_block_types;
		}

		if ( ! is_array( $allowed_block_types ) || empty( $allowed_block_types ) ) {
			$registered_blocks   = \WP_Block_Type_Registry::get_instance()->get_all_registered();
			$allowed_block_types = array_keys( $registered_blocks );
		}

		return array_values( array_diff( $allowed_block_types, [ $this->name ] ) );
	}

	/**
	 * Renders the block on the front end. $content is the already-rendered
	 * markup of the inner core/post-title block.
	 */
	public function render( $attributes, $content ) {
		$post_id = get_the_ID();
		$sponsor = get_post_sponsor( $post_id );

		$kicker               = $attributes['kicker'] ?? '';
		$background_image_id  = $attributes['backgroundImageId'] ?? 0;
		$logo_override_id     = $attributes['sponsorLogoOverrideId'] ?? 0;

		$style = '';
		if ( $background_image_id ) {
			$bg_url = wp_get_attachment_image_url( $background_image_id, 'full' );
			if ( $bg_url ) {
				$style = sprintf( ' style="--ltm-title-block-bg: url(%s);"', esc_url( $bg_url ) );
			}
		}

		ob_start();
		?>
		<div class="ltm-title-block"<?php echo $style; ?>>
			<div class="ltm-title-block__inner">
				<div class="ltm-title-block__content">
					<?php if ( $kicker ) : ?>
						<p class="ltm-title-block__kicker"><?php echo esc_html( $kicker ); ?></p>
					<?php endif; ?>
					<?php echo $content; ?>
				</div>
				<?php if ( $sponsor ) : ?>
					<div class="ltm-title-block__sponsor">
						<span class="ltm-title-block__sponsor-label"><?php esc_html_e( 'Presented By', 'ltm' ); ?></span>
						<?php
						if ( $logo_override_id ) {
							echo wp_get_attachment_image( $logo_override_id, 'medium', false, [ 'class' => 'ltm-title-block__sponsor-logo' ] );
						} elseif ( has_post_thumbnail( $sponsor->ID ) ) {
							echo get_the_post_thumbnail( $sponsor->ID, 'medium', [ 'class' => 'ltm-title-block__sponsor-logo' ] );
						}
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
