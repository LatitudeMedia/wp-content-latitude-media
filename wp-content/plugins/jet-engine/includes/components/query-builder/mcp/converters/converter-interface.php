<?php
namespace Jet_Engine\Query_Builder\MCP\Converters;

interface Converter_Interface {

	/**
	 * Convert the given arguments.
	 */
	public function convert( array $args ): array;

}