/**
 * Internal dependencies
 */
import type { Tool, ToolResult } from '../types/messages';
import { wpApiClient } from '../context/conversation-provider';
import { prepareToolsForApi } from './orchestrator';

/**
 * Interface for a provider that can supply tools to the executor.
 * Abstracted to show that WP Feature API is just one source of tools, and other sources can be added.
 */
export interface ToolProvider {
	/**
	 * Retrieves the tools provided by this source.
	 */
	getTools: () => Promise< Tool[] > | Tool[];
}

/**
 * Interface for the central tool execution engine.
 */
export interface ToolExecutor {
	/**
	 * Retrieves a list of all currently registered tools.
	 */
	listTools: () => Tool[];

	/**
	 * Executes a registered tool by name with the given arguments.
	 *
	 * @param name The name (ID) of the tool to execute.
	 * @param args The arguments to pass to the tool's execute function.
	 * @return A promise resolving to the tool's result or error.
	 */
	executeTool: (
		name: string,
		args: Record< string, unknown >
	) => Promise< ToolResult >;

	/**
	 * Registers tools from a ToolProvider.
	 *
	 * @param provider The provider supplying the tools.
	 * @return A promise that resolves when the provider's tools have been added.
	 */
	addProvider: ( provider: ToolProvider ) => Promise< void >;
}

/**
 * Factory function to create a ToolExecutor instance.
 *
 * @return A new ToolExecutor instance.
 */
export const createToolExecutor = ( model: string ): ToolExecutor => {

	const toolMap = new Map< string, Tool >();

	const addProvider = async ( provider: ToolProvider ): Promise< void > => {
		try {
			const tools = await Promise.resolve( provider.getTools() );
			for ( const tool of tools ) {
				if ( toolMap.has( tool.name ) ) {
					// eslint-disable-next-line no-console
					console.warn(
						`Tool name collision: Tool "${ tool.name }" is already registered. Overwriting.`
					);
				}
				toolMap.set( tool.name, tool );
			}
		} catch ( error ) {
			// eslint-disable-next-line no-console
			console.error( 'Error adding tools from provider:', error );
		}
	};

	const listTools = (): Tool[] => {
		return Array.from( toolMap.values() );
	};

	// find tool by name or tool.displayName
	const findTool = ( name: string ): Tool | undefined => {
		let tool = toolMap.get( name );

		if ( ! tool ) {
			tool = Array.from( toolMap.values() ).find( ( t ) => t.displayName === name );
		}

		return tool;
	};

	const remoteExecuteTool = async ( tool: Tool, args: Record< string, unknown > ): Promise< ToolResult > => {

		try {
			const result = await wpApiClient(
				'response',
				{
					tools: prepareToolsForApi( [ tool ] ),
					tool_choice: { type: 'function', name: tool.name },
					tool_input: args,
					messages: [ { role: 'user', content: 'Execute given tool with args: ' + JSON.stringify( args ) } ],
					model: model,
				}
			);

			return result;

		} catch ( error ) {
			return {
				result: null,
				error:
					error instanceof Error
						? error.message
						: 'Unknown error executing client feature',
			};
		}
	};

	const executeTool = async (
		name: string,
		args: Record< string, unknown >
	): Promise< ToolResult > => {

		const tool = findTool( name );

		if ( ! tool ) {
			return {
				result: null,
				error: `Tool "${ name }" not found.`,
			};
		}

		try {
			const result = await remoteExecuteTool( tool, args );
			return result;
		} catch ( executionError ) {
			// eslint-disable-next-line no-console
			console.error(
				`Error executing tool "${ name }":`,
				executionError
			);
			return {
				result: null,
				error:
					executionError instanceof Error
						? executionError.message
						: 'An unknown execution error occurred',
			};
		}
	};

	return {
		addProvider,
		listTools,
		executeTool,
	};
};
