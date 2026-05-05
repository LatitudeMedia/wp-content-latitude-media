/**
 * Internal dependencies
 */
import type { Message } from '../types/messages';
import type { ToolExecutor } from './tool-executor';

/**
 * Defines the shape of the function responsible for making API calls.
 * This allows injecting different clients (e.g., wp.apiFetch, standard fetch).
 */
export type ApiClient = (
	endpoint: string,
	data: {
		messages: Message[];
		model: string;
		tools?: any[];
		tool_choice?: string|{ type: string; name: string };
		tool_input?: Record< string, unknown >;
	}
) => Promise< any >;

/**
 * Dependencies required by the agent orchestrator.
 */
export interface AgentDependencies {
	apiClient: ApiClient;
	toolExecutor?: ToolExecutor;
}

export interface BackendToolCall {
	name: string;
	args: Record< string, unknown >;
	raw?: string;
}

export interface BackendResponse {
	content: string | null;
	called_tools?: Array< {
		name: string;
		args: Record< string, unknown >;
		result: unknown;
	} >;
	output?: any[];
	response_id?: string;
	error?: { message?: string } | null;
	next_tool_calls?: BackendToolCall[];
	has_next_tools?: boolean;
}

/**
 * The interface for the created agent.
 */
export interface Agent {
	/**
	 * Processes a user query, interacts with the LLM via the ApiClient,
	 * potentially uses tools, and yields messages representing the conversation flow.
	 * @param query           The user's input string.
	 * @param currentMessages The existing conversation history.
	 * @param modelId         The ID of the model to use.
	 * @return An async generator yielding Message objects.
	 */
	processQuery: (
		query: string,
		currentMessages: Message[],
		modelId: string
	) => AsyncGenerator< Message >;
}

export const prepareToolsForApi = ( tools: Array<any> ): any[] => {
	return tools.map( ( tool ) => ( {
		type: 'function',
		name: tool.name,
		displayName: tool.displayName,
		description: tool.description,
		parameters: tool.parameters,
	} ) );
};

const runToolCallsFromBackend = async function* (
	toolCalls: BackendToolCall[],
	toolExecutor?: ToolExecutor
): AsyncGenerator< Message > {

	if ( ! toolExecutor || ! toolCalls?.length ) {
		return;
	}

	for ( const toolCall of toolCalls ) {
		const toolName = toolCall.name;
		const args     = toolCall.args || {};

		console.log( toolName );
		console.log( args );

		const result = await toolExecutor.executeTool( toolName, args );

		// Build a “tool” message for the conversation UI.
		const toolMessage: Message = {
			role: 'tool',
			name: toolName,
			content: result?.error
				? `Error: ${ String( result.error ) }`
				: JSON.stringify(
					result?.result ?? result?.content ?? null
				  ),
		};

		yield toolMessage;
	}
};

/**
 * Factory function to create an AI agent instance.
 * @param deps Dependencies like the API client and optional tool executor.
 * @return An Agent instance.
 */
export const createAgent = ( deps: AgentDependencies ): Agent => {

	const { apiClient, toolExecutor } = deps; // Destructure toolExecutor

	const processQuery = async function* (
		query: string,
		currentMessages: Message[],
		modelId: string
	): AsyncGenerator< Message > {

		const userMessage: Message = { role: 'user', content: query };

		yield userMessage;

		// Limit the history to the last 15 messages for context to avoid exceeding token limits
		const limitedHistory = currentMessages.slice( -15 );

		while ( limitedHistory.length > 0 && limitedHistory[ 0 ].role === 'tool' ) {
			limitedHistory.shift();
		}

		let assistantResponseContent: string | null = null;
		let toolCallsResult: any[] | null = null;

		try {
			const apiPayload: any = {
				message: userMessage.content,
				model: modelId,
			};

			if ( toolExecutor && toolExecutor.listTools().length > 0 ) {
				apiPayload.tools = prepareToolsForApi( toolExecutor.listTools() );
				apiPayload.tool_choice = 'auto';
			}

			const response = await apiClient(
				'response',
				apiPayload
			) as BackendResponse;

			if ( ! response || typeof response !== 'object' ) {
				throw new Error(
					'Invalid response structure from API proxy.'
				);
			}

			if ( response.error && response.error.message ) {
				throw new Error( response.error.message );
			}

			assistantResponseContent = response.content || null;
			toolCallsResult = response.called_tools || null;

			const assistantTurnMessage: Message = {
				role: 'assistant',
				content:
					assistantResponseContent ??
					( toolCallsResult ? '' : null ),
			};

			if ( toolCallsResult && toolExecutor ) {
				for ( const toolCall of toolCallsResult ) {
					if (
						toolCall.name &&
						typeof toolCall.name === 'string' &&
						toolCall.args &&
						typeof toolCall.args === 'object' &&
						toolCall.result
					) {

						const toolMessage: Message = {
							role: 'tool',
							name: toolCall.name,
							content: toolCall.result.error
								? `Error: ${ toolCall.result.error }`
								: JSON.stringify( toolCall.result ),
						};

						yield toolMessage;
					}
				}
			}

			yield assistantTurnMessage;

			let pendingTextToolCalls = response.next_tool_calls ?? [];

			while ( pendingTextToolCalls.length > 0 ) {
				const toolCallBatch = pendingTextToolCalls;

				// Run the current batch of tool calls and collect their results
				const toolMessages: Message[] = [];

				console.log( toolCallBatch );

				for await ( const toolMessage of runToolCallsFromBackend(
					toolCallBatch,
					toolExecutor
				) ) {
					toolMessages.push(toolMessage);
					yield toolMessage;
				}

				// Prepare the payload for the next backend call
				const nextApiPayload: any = {
					messages: [
						userMessage,
						assistantTurnMessage,
						...toolMessages
					],
					model: modelId,
				};

				if ( toolExecutor && toolExecutor.listTools().length > 0 ) {
					nextApiPayload.tools = prepareToolsForApi( toolExecutor.listTools() );
					nextApiPayload.tool_choice = 'auto';
				}

				const nextResponse = await apiClient(
					'response',
					nextApiPayload
				) as BackendResponse;

				if ( ! nextResponse || typeof nextResponse !== 'object' ) {
					throw new Error(
						'Invalid response structure from API proxy.'
					);
				}

				if ( nextResponse.error && nextResponse.error.message ) {
					throw new Error( nextResponse.error.message );
				}

				// Yield any new assistant content, if present
				if ( nextResponse.content ) {
					yield {
						role: 'assistant',
						content: nextResponse.content,
					};
				}

				// Yield any new tool call results, if present
				if ( nextResponse.called_tools && toolExecutor ) {
					for ( const toolCall of nextResponse.called_tools ) {
						if (
							toolCall.name &&
							typeof toolCall.name === 'string' &&
							toolCall.args &&
							typeof toolCall.args === 'object' &&
							toolCall.result
						) {
							const toolMessage: Message = {
								role: 'tool',
								name: toolCall.name,
								content: toolCall.result.error
									? `Error: ${ toolCall.result.error }`
									: JSON.stringify( toolCall.result ),
							};
							yield toolMessage;
						}
					}
				}

				// Update pendingTextToolCalls for the next iteration
				pendingTextToolCalls = nextResponse.next_tool_calls ?? [];
			}
		} catch ( error ) {

			let errorMessage = 'Unknown error';

			if ( error instanceof Error ) {
				errorMessage = error.message;
			} else if ( typeof error === 'string' ) {
				errorMessage = error;
			} else if ( typeof error === 'object' && error !== null && 'message' in error ) {
				errorMessage = String( ( error as any ).message );
			}

			// Parse message content to replace tools names with display names if possible
			if ( toolExecutor ) {
				const tools = toolExecutor.listTools();
				for ( const tool of tools ) {
					if ( errorMessage.includes( tool.name ) ) {
						errorMessage = errorMessage.replace(
							tool.name,
							tool.label || tool.displayName || tool.name
						);
					}
				}
			}

			const errorMsgContent = `Sorry, I encountered an error: \`${ errorMessage }\``;

			yield {
				role: 'assistant',
				content: errorMsgContent,
			};
		}
	};

	return { processQuery };
};
