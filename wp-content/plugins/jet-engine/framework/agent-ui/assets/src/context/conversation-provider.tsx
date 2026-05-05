/**
 * WordPress dependencies
 */
import {
	createContext,
	useState,
	useCallback,
	useMemo,
	useEffect,
	useRef,
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

/**
 * External dependencies
 */
import { type ReactNode, type Dispatch, type SetStateAction } from 'react';

/**
 * Internal dependencies
 */
import type { Message } from '../types/messages';
import { createAgent, type Agent, type ApiClient } from '../agent/orchestrator';
import { createToolExecutor, type ToolExecutor } from '../agent/tool-executor';
import { createWpFeatureToolProvider } from '../agent/wp-feature-tool-provider';

export interface ConversationContextType {
	messages: Message[];
	setMessages: Dispatch< SetStateAction< Message[] > >;
	sendMessage: ( query: string ) => Promise< void >;
	isLoading: boolean;
	clearConversation: () => void;
	toolNameMap: Record< string, string >;
	toolExecutor?: ToolExecutor | null;
	setModel: ( model: string ) => void;
	model: string;
	clearLocalStorage: () => void;
}

export const ConversationContext =
	createContext< ConversationContextType | null >( null );

interface ConversationProviderProps {
	children: ReactNode;
}

export const wpApiClient: ApiClient = async ( endpoint, data ) => {

	if ( ! apiFetch ) {
		throw new Error(
			'wp.apiFetch is not available. Ensure script dependencies are loaded.'
		);
	}

	return await apiFetch( {
		url: window.crocoAgentUI.base_endpoint + endpoint,
		method: 'POST',
		data
	} );
};

// Storage key for localStorage, basic memory persistence.
const STORAGE_KEY = 'jet-ai-conv-' + window.crocoAgentUI.site_key;
const STORAGE_LENGTH_LIMIT = 150;

export const ConversationProvider = ( {
	children,
}: ConversationProviderProps ) => {
	const [ messages, setMessages ] = useState< Message[] >( () => {
		try {
			const stored = localStorage.getItem( STORAGE_KEY );
			return stored ? JSON.parse( stored ) : [];
		} catch ( error ) {
			return [];
		}
	} );
	const [ isLoading, setIsLoading ] = useState< boolean >( false );
	const [ toolExecutor, setToolExecutor ] = useState< ToolExecutor | null >(
		null
	);
	const [ toolNameMap, setToolNameMap ] = useState<
		Record< string, string >
	>( {} );

	const [ model, setModel ] = useState<string>( window.crocoAgentUI.model );
	const isInitializing = useRef( false );

	useEffect( () => {
		if ( messages.length > 0 ) {

			// Limit stored messages to last N entries to avoid localStorage bloat.
			const limitedMessages = messages.slice( -STORAGE_LENGTH_LIMIT );
			const serialized = JSON.stringify( limitedMessages );
			localStorage.setItem( STORAGE_KEY, serialized );
		}
	}, [ messages ] );

	useEffect( () => {
		if ( isInitializing.current ) {
			return;
		}
		isInitializing.current = true;

		const initializeExecutor = async () => {
			const executor = createToolExecutor( model );
			const provider = createWpFeatureToolProvider();

			try {
				await executor.addProvider( provider );

				// Build hash-to-feature-name map, so we can display the feature name in the UI.
				const tools = await Promise.resolve( provider.getTools() );
				const nameMap: Record< string, string > = {};
				for ( const tool of tools ) {
					nameMap[ tool.name ] = tool.displayName;
				}

				setToolNameMap( nameMap );

				setToolExecutor( executor );
			} catch ( error ) {
				// eslint-disable-next-line no-console
				console.error( 'Failed to initialize Tool Executor:', error );
			}
		};

		initializeExecutor();
	}, [] );

	const agent: Agent | null = useMemo( () => {

		if ( toolExecutor ) {
			return createAgent( { apiClient: wpApiClient, toolExecutor } );
		}

		return null;
	}, [ toolExecutor ] );

	const sendMessage = useCallback(
		async ( query: string ) => {
			if ( isLoading || ! agent ) {
				return;
			}

			setIsLoading( true );

			const historyBeforeQuery = messages;

			try {
				const messageStream = agent.processQuery(
					query,
					historyBeforeQuery,
					model
				);

				for await ( const messageChunk of messageStream ) {
					setMessages( ( prev ) => {
						return [ ...prev, messageChunk ];
					} );
				}
			} catch ( error ) {
				// eslint-disable-next-line no-console
				console.error( 'Error sending message:', error );
				setMessages( ( prev ) => [
					...prev,
					{
						role: 'assistant',
						content: `Error: ${
							error instanceof Error
								? error.message
								: 'Failed to get response'
						}`,
					},
				] );
			} finally {
				setIsLoading( false );
			}
		},
		[ isLoading, agent, messages, toolExecutor ]
	);

	const clearConversation = useCallback( () => {
		setMessages( [] );
		wpApiClient( 'clear-conversation', {} );
		clearLocalStorage();
	}, [] );

	const clearLocalStorage = useCallback( () => {
		localStorage.removeItem( STORAGE_KEY );
	}, [] );

	const contextValue = useMemo(
		() => ( {
			messages,
			setMessages,
			sendMessage,
			isLoading,
			clearConversation,
			toolNameMap,
			toolExecutor,
			setModel,
			model,
			clearLocalStorage,
		} ),
		[
			messages,
			setMessages,
			sendMessage,
			isLoading,
			clearConversation,
			toolNameMap,
			toolExecutor,
			setModel,
			model,
			clearLocalStorage,
		]
	);


	// Wait until the tool name map is populated before rendering the chat UI.
	const isReady = Object.keys( toolNameMap ).length > 0;

	return (
		<ConversationContext.Provider value={ contextValue }>
			{ ! isReady ? <div /> : children }
		</ConversationContext.Provider>
	);
};
