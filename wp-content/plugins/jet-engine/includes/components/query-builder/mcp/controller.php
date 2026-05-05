<?php
namespace Jet_Engine\Query_Builder\MCP;

class Controller {

	public function __construct() {
		add_action( 'jet-engine/mcp-tools/register-features', array( $this, 'register_tools' ) );
	}

	public function register_tools( $registry ) {
		require_once jet_engine()->plugin_path( 'includes/components/query-builder/mcp/tool-add-query.php' );
		new Tool_Add_Query();
	}

	/**
	 * Get the appropriate converter for the given type.
	 */
	public static function get_converter( $type ) {

		$base_path = jet_engine()->plugin_path( 'includes/components/query-builder/mcp/converters/' );

		require_once $base_path . 'converter-interface.php';
		require_once $base_path . 'common-trait.php';

		switch ( $type ) {
			case 'posts':
				require_once $base_path . 'posts.php';
				return new Converters\Posts();

			case 'terms':
				require_once $base_path . 'terms.php';
				return new Converters\Terms();

			case 'users':
				require_once $base_path . 'users.php';
				return new Converters\Users();
			case 'comments':
				require_once $base_path . 'comments.php';
				return new Converters\Comments();

			case 'repeater':
				require_once $base_path . 'repeater.php';
				return new Converters\Repeater();

			case 'sql':
				require_once $base_path . 'sql.php';
				return new Converters\SQL();

			default:
				return apply_filters( 'jet-engine/query-builder/mcp/get-converter/' . $type, null );
		}

		return null;
	}
}
