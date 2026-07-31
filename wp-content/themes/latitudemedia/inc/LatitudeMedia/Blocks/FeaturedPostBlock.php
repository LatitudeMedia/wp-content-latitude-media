<?php
namespace LatitudeMedia\Blocks;

/**
 * Native "Featured Post" block: highlights a single, editor-picked post as a
 * hero-style card, optionally scoped to the current page's sponsor.
 *
 * @package LatitudeMedia
 */
class FeaturedPostBlock {

	/**
	 * Registered block name.
	 *
	 * @var string
	 */
	public $name = 'latitudemedia/featured-post-block';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Registers the block type, wiring the dynamic render callback.
	 */
	public function register_block() {
		register_block_type(
			get_template_directory() . '/src/blocks/featured-post-block/block.json',
			[
				'render_callback' => array( $this, 'render' ),
			]
		);
	}

	/**
	 * Enqueues the block editor script, with explicit core dependencies
	 * since this block isn't built via @wordpress/scripts dependency
	 * extraction. The block's stylesheet is declared via block.json's
	 * "style" field instead of enqueued here — WordPress resolves that
	 * into the block editor iframe's `resolvedAssets.styles` correctly,
	 * whereas a plain `wp_enqueue_style()` call from
	 * `enqueue_block_editor_assets` only reaches the iframe by accident
	 * (Gutenberg's iframe only clones stray stylesheets that happen to
	 * contain a `.wp-block`/`.editor-styles-wrapper` selector, logging
	 * "was added to the iframe incorrectly" when it does).
	 */
	public function enqueue_editor_assets() {
		$script_rel_path = '/dist/js/blocks/featured-post-block.min.js';
		$script_path      = get_template_directory() . $script_rel_path;

		if ( file_exists( $script_path ) ) {
			wp_enqueue_script(
				'ltm-featured-post-block-editor',
				get_template_directory_uri() . $script_rel_path,
				[
					'wp-blocks',
					'wp-element',
					'wp-block-editor',
					'wp-components',
					'wp-data',
					'wp-core-data',
					'wp-i18n',
					'wp-api-fetch',
					'wp-url',
					'wp-server-side-render',
				],
				filemtime( $script_path ),
				true
			);
		}
	}

	/**
	 * Renders the block on the front end (and via ServerSideRender in the
	 * block editor, so the preview matches the front end exactly).
	 */
	public function render( $attributes ) {
		$post_id = absint( $attributes['postId'] ?? 0 );

		if ( ! $post_id || get_post_status( $post_id ) !== 'publish' ) {
			return '';
		}

		if ( $attributes['excludeFromOtherBlocks'] ?? true ) {
			\LatitudeMedia\Page_Data()->addItems( [ $post_id ] );
		}

		$template = get_wrap_rows_from_template(
			'<div class="image-folder">[thumb]</div>
			<div class="content-folder">
				[tags-list]
				[title]
				[excerpt]
				<div class="info">
					[author]<span></span>[date]
				</div>
			</div>'
		);

		ob_start();
		?>
		<div class="container">
			<div class="ltm-featured-post-block">
				<?php
				get_template_part(
					'template-parts/components/post',
					'item',
					[
						'post_id'  => $post_id,
						'rows'     => $template['rows'],
						'wrap'     => $template['wrap'],
						'settings' => [
							'thumb'  => [
								'size' => 'list-three-events',
								'link' => true,
							],
							'author' => [
								'link_class' => 'author',
							],
							'date'   => [
								'format' => 'M j, Y',
							],
						],
					]
				);
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
