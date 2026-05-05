<?php
namespace Jet_Engine\Listings\MCP;

class Controller {

	public function __construct() {
		add_action( 'jet-engine/mcp-tools/register-features', array( $this, 'register_tools' ) );
	}

	public function register_tools( $registry ) {
		require_once jet_engine()->plugin_path( 'includes/components/listings/mcp/tool-add-listing.php' );
		new Tool_Add_Listing();
	}
}
