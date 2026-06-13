<?php
/**
 * Timber view class
 */
namespace Jet_Engine\Timber_Views;

use Timber\Timber;
use Timber\Loader;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Package {

	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	public $editor;
	public $registry;
	public $render;
	public $listing;

	protected $sanitized_templates = [];

	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Initialize
	 *
	 * @return [type] [description]
	 */
	public function init() {

		require_once $this->package_path( 'integration.php' );

		$integration = new Integration();

		if ( ! $integration->is_enabled() || ! $integration->has_timber() ) {
			return;
		}

		require_once $this->package_path( 'editor/render.php' );
		require_once $this->package_path( 'editor/listing.php' );
		require_once $this->package_path( 'view/registry.php' );
		require_once $this->package_path( 'view/render.php' );
		require_once $this->package_path( 'conditional-tags.php' );
		require_once $this->package_path( 'object-factory.php' );
		require_once $this->package_path( 'content-setter.php' );
		require_once $this->package_path( 'components/register.php' );

		$this->editor   = new Editor\Render();
		$this->listing  = new Editor\Listing();
		$this->registry = new View\Registry();
		$this->render   = new View\Render();

		new Conditional_Tags();
		new Components\Register();
		new Content_Setter();

		add_action( 'init', [ $this, 'after_init_hook' ], 999 );

	}

	public function after_init_hook() {
		do_action( 'jet-engine/twig-views/after-init', $this );
	}

	/**
	 * Return path inside package.
	 *
	 * @param string $relative_path
	 *
	 * @return string
	 */
	public function package_path( $relative_path = '' ) {
		return jet_engine()->plugin_path( 'includes/components/timber-views/inc/' . $relative_path );
	}

	/**
	 * Return url inside package.
	 *
	 * @param string $relative_path
	 *
	 * @return string
	 */
	public function package_url( $relative_path = '' ) {
		return jet_engine()->plugin_url( 'includes/components/timber-views/inc/' . $relative_path );
	}

	/**
	 * Sanitize HTML template including twig components
	 *
	 * @param  [type] $html [description]
	 * @return [type]       [description]
	 */
	public function sanitize_html( $html ) {

		/**
		 * Main sanitization is done in sanitize_twig_content() method.
		 *
		 * Keep wp_unslash() as in
		 * https://github.com/Crocoblock/issues-tracker/issues/12437 fix has been added
		 * to ensure HTML is slashed upon saving the listing item.
		 */
		$html = wp_unslash( $html );

		/**
		 * Ensure macros advanced args keep slashed because them used as arguments in Twig functions.
		 *
		 * https://github.com/Crocoblock/issues-tracker/issues/17237
		 */
		$html = preg_replace_callback(
			'/\%(\{".*?"\})/',
			function ( $matches ) {
				return '%' . wp_slash( $matches[1] );
			},
			$html
		);


		return $html;
	}

	/**
	 * Sanitize listing CSS before save or render
	 *
	 * @param  [type] $css [description]
	 * @return [type]      [description]
	 */
	public function sanitize_css( $css ) {

		$css = wp_kses( $css, [] );
		$css = str_replace(
			[ '</style>', '<' ],
			[ '&lt;&#47;style&gt;', '&lt;' ],
			$css
		);

		return $css;
	}

	/**
	 * Render HTML with Twig context.
	 *
	 * @param  string $html    HTML content to render.
	 * @param  array  $context Context for Twig rendering.
	 * @param  object $twig    Optional Twig instance to use.
	 * @return string
	 */
	public function render_html( $html = '', $context = [], $twig = null ) {

		if ( ! $twig ) {
			$dummy_loader = new Loader();
			$twig = $dummy_loader->get_twig();
		}

		// Do macros early to prevent overlaps with Twig syntax
		$html = $this->sanitize_html( do_shortcode( $this->sanitize_twig_content( $html ) ) );

		$template = $twig->createTemplate( $html );

		return $template->render( $context );
	}

	/**
	 * get twig context for given object.
	 *
	 * @param  object $object Object to get contxt for.
	 * @return array
	 */
	public function get_context_for_object( $object ) {

		$context        = [];
		$object_factory = new Object_Factory();

		if ( is_object( $object ) && 'WP_Post' === get_class( $object ) ) {
			$context['post'] = $object_factory->get_post( $object, false );
		}

		if ( is_object( $object ) && 'WP_User' === get_class( $object ) ) {
			$context['user'] = $object_factory->get_user( $object, false );
		} elseif ( is_user_logged_in() ) {
			$context['user'] = $object_factory->get_user( wp_get_current_user(), false );
		}

		$object_factory->set_current( $object );

		return apply_filters( 'jet-engine/twig-views/current-context', $context, $object );
	}

	/**
	 * Sanitize Timber/Twig template content before render.
	 * Remove insecure Twig tokens, run with Sandbox (optionally).
	 *
	 * @param  string $input Raw content to sanitize.
	 * @return string
	 */
	public function sanitize_twig_content( $input = '' ) {

		$hash = md5( $input );

		// Check if we already sanitized this template
		// to avoid reprocessing the same content.
		if ( isset( $this->sanitized_templates[ $hash ] ) ) {
			return $this->sanitized_templates[ $hash ];
		}

		$input = preg_replace( '/\R/', "\n", $input );
		$input = trim( $input );

		$original_input = $input;

		// Remove entire Twig block structures including content inside
		$dangerous_block_tags = apply_filters( 'jet-engine/twig-views/dangerous-block-tags', [
			'block',
			'set',
			'embed',
			'macro',
			'filter',
			'apply',
			'verbatim',
			'sandbox',
			'with',
		] );

		foreach ( $dangerous_block_tags as $tag ) {
			$input = preg_replace(
				'/\{%\s*' . preg_quote( $tag, '/' ) . '\b.*?%\}(.*?)\{%\s*end' . preg_quote( $tag, '/' ) . '\s*%\}/is',
				'',
				$input
			);
		}

		$dangerous_tags = apply_filters( 'jet-engine/twig-views/dangerous-selfclosing-tags', [
			'include',
			'import',
			'from',
			'use',
			'extends',
			'do',
			'flush',
		] );

		foreach ( $dangerous_tags as $tag ) {
			$input = preg_replace( '/\{%\s*' . preg_quote( $tag, '/' ) . '\b[^%]*%\}/i', '', $input );
		}

		// Remove string concatenation inside filter/function calls
		$input = preg_replace( '/[\'"][^\'"]+[\'"]\s*~\s*[\'"][^\'"]+[\'"]/', '', $input );

		// Remove dangerous function calls inside expressions
		$dangerous_functions = [
			'block', 'passthru', 'exec', 'eval', 'system', 'shell_exec', 'proc_open',
			'popen', 'assert', 'file_put_contents', 'file_get_contents', 'unlink',
			'fopen', 'fwrite', 'call_user_func', 'call_user_func_array',
			'create_function', 'reduce', 'constant'
		];

		foreach ( $dangerous_functions as $func ) {
			$input = preg_replace('/\{\{[^}]*\b' . preg_quote($func, '/') . '\s*\([^}]*\)[^}]*\}\}/i', '', $input);
			$input = preg_replace('/\b' . preg_quote($func, '/') . '\b/i', '', $input);
		}

		// Remove block function calls like {{ block('name') }}
		$input = preg_replace('/\{\{\s*block\s*\([^}]*\)\s*\}\}/i', '', $input);

		foreach ( $dangerous_functions as $func ) {
			$input = preg_replace( '/\b' . preg_quote( $func, '/' ) . '\b/i', '', $input );
		}

		// Remove access to special globals and internals
		$input = preg_replace(
			'/\{\{\s*(_self|_charset|_env|_globals)[^}]*\}\}/i',
			'',
			$input
		);

		$input = preg_replace(
			'/\{%\s*(if|for|elseif|else).*(_self|_charset|_env|_globals)[^%]*%\}/i',
			'',
			$input
		);

		$input = preg_replace(
			'/\{\{[^}]*\.(globals|_self|_env)[^}]*\}\}/i',
			'',
			$input
		);

		if ( $input !== $original_input ) {

			$dangerous_constructs = array_merge(
				$dangerous_block_tags,
				$dangerous_tags,
				$dangerous_functions,
				[ '_self', '_charset', '_env', 'globals' ]
			);

			$dangerous_constructs_str = implode( ', ', array_unique( $dangerous_constructs ) );

			return sprintf(
				esc_html__( 'Your template contains unsafe Twig tags, functions or globals. Please check it and remove any of these code constructs: %s. Also make sure you do not use string concatenations inside functions or filters calls.', 'jet-engine' ),
				esc_html( $dangerous_constructs_str )
			);
		}

		$this->sanitized_templates[ $hash ] = $input;

		return $input;
	}

	/**
	 * Slug for listing views
	 *
	 * @return [type] [description]
	 */
	public function get_view_slug() {
		return 'twig';
	}

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return static
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

Package::instance();
