<?php
/**
 * Main class for the AI API Proxy REST endpoints.
 *
 * @package WordPress\Feature_API_Agent
 */

namespace Crocoblock\Agent_UI;

use \Jet_Engine\MCP_Tools\Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tool_Dispatcher {

	protected $tool_name = '';

	protected $tool_args = [];

	public function __construct( string $tool_name, array $tool_args = [] ) {
		$this->tool_name = $tool_name;
		$this->tool_args = $tool_args;
	}

	public function execute() : array {

		$tool = Registry::instance()->get_feature( $this->tool_name );

		if ( ! $tool ) {
			return [ 'error' => 'Tool not found' ];
		}

		return $tool->run( $this->tool_args );
	}

}