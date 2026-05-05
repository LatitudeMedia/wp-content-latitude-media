<?php
namespace Jet_Engine\Taxonomies\MCP;

class Controller {
	public function __construct() {
		add_action( 'jet-engine/mcp-tools/register-features', [ $this, 'register_tools' ] );
	}

	public function register_tools( $registry ) {
		require_once jet_engine()->plugin_path( 'includes/components/taxonomies/mcp/tool-add-taxonomy.php' );
		new Tool_Add_Taxonomy();
	}
}
