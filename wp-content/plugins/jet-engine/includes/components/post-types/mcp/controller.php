<?php
namespace Jet_Engine\Post_Types\MCP;

class Controller {

	public function __construct() {
		add_action( 'jet-engine/mcp-tools/register-features', [ $this, 'register_tools' ] );
	}

	public function register_tools( $registry ) {
		require_once jet_engine()->plugin_path( 'includes/components/post-types/mcp/tool-add-cpt.php' );
		new Tool_Add_CPT();
	}
}
