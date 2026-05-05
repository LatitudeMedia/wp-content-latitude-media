/**
 * WordPress dependencies
 */
import { useState, useRef, useEffect } from '@wordpress/element';
import {
	Button,
	TextareaControl,
	Icon,
	Panel,
	PanelBody,
	Popover
} from '@wordpress/components';

import { arrowUp, trash } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { useConversation } from '../hooks/use-conversation';
import {
	UserMessage,
	AssistantMessage,
	PendingAssistantMessage,
} from './chat-message';

export const ChatApp = () => {

	const { messages, sendMessage, isLoading, clearConversation } =
		useConversation();
	const [ input, setInput ] = useState( '' );
	const [ suggestedInputID, setSuggestedInputID ] = useState( null );
	const messagesEndRef = useRef< HTMLDivElement | null >( null );
	const [ showClearConfirm, setShowClearConfirm ] = useState( false );

	// Scroll to bottom when messages change
	useEffect( () => {
		messagesEndRef.current?.scrollIntoView( {
			behavior: "auto",
			block: "end",
		} );
	}, [ messages ] );

	const handleSend = () => {
		if ( input.trim() && ! isLoading ) {
			sendMessage( input.trim() );
			setSuggestedInputID( null );
			setInput( '' );
		}
	};

	const handleKeyDown = (
		event: React.KeyboardEvent< HTMLTextAreaElement >
	) => {

		// adjust textarea height based on content
		const textarea = event.currentTarget;
		if ( ! input ) {
			textarea.style.height = '60px';
		} else if ( textarea.scrollHeight > 60 ) {
			textarea.style.height = `${ textarea.scrollHeight + 2 }px`;
		}

		// iterate through previous/next user messages on up/down arrow
		if ( event.key === 'ArrowUp' ) {
			// get previous user message
			const lastUserMessage = [ ...messages ].reverse().find( ( msg, index ) => {
				if ( msg.role === 'user' && msg.content ) {
					if ( null === suggestedInputID ) {
						setSuggestedInputID( index );
						return true;
					} else if ( index > suggestedInputID ) {
						setSuggestedInputID( index );
						return true;
					}
				}
			} );

			if ( lastUserMessage ) {
				setInput( lastUserMessage.content );
			}
		}

		// Send on Enter, allow Shift+Enter for newline
		if ( event.key === 'Enter' && ! event.shiftKey ) {
			event.preventDefault();
			handleSend();
		}
	};

	useEffect( () => {

		const handleClickOutside = ( event: MouseEvent ) => {

			const target = event.target as HTMLElement;

			if ( target.closest( '.cmd-cnt-header-actions' ) ) {
				return;
			}

			setShowClearConfirm( false );
		};

		document.addEventListener( 'click', handleClickOutside );

		return () => document.removeEventListener( 'click', handleClickOutside );
	}, [] );

	return (
		<Panel
			header="Command Center"
			className="cmd-cnt-chat-panel"
		>
			<PanelBody>
				<div className="cmd-cnt-header-actions">
					<Button
						onClick={ () => {
							setShowClearConfirm( ( prev ) => ! prev );
							//clearConversation();
						} }
						label="Clear Conversation"
						icon={ <Icon icon={ trash } /> }
						variant="tertiary"
						size="small"
					>
						{ showClearConfirm && <Popover
							noArrow={ false }
							position="bottom"
						>
							<div style={ { padding: '12px', width: '200px' } }>
								<div>Are you sure you want to clear the conversation? All the context provided before will be lost.</div>
								<div style={ { display: 'flex', justifyContent: 'space-between', gap: '8px', marginTop: '8px' } }>
									<a
										href="#"
										onClick={ ( e ) => {
											e.preventDefault();
											e.stopPropagation();
											clearConversation();
											setShowClearConfirm( false );
										} }
									>Yes, clear</a>
									<a
										href="#"
										onClick={ ( e ) => {
											e.preventDefault();
											e.stopPropagation();
											setShowClearConfirm( false );
										} }
									>Cancel</a>
								</div>
							</div>
						</Popover> }
					</Button>
				</div>
				<div className="cmd-cnt-body">
					<div className="cmd-cnt-messages">
						{ messages.map( ( msg, index ) => {
							const key = `msg-${ index }`;
							if ( msg.role === 'user' ) {
								return (
									<UserMessage
										key={ key }
										text={ msg.content ?? '' }
									/>
								);
							}
							return <AssistantMessage key={ key } message={ msg } />;
						} ) }
						{ isLoading && <PendingAssistantMessage /> }
						<div className="cmd-cnt-messages-end" ref={ messagesEndRef } />
					</div>
					<div className="cmd-cnt-input">
						<TextareaControl
							value={ input }
							onChange={ setInput }
							placeholder="Type your message..."
							onKeyDown={ handleKeyDown }
							disabled={ isLoading }
							className="cmd-cnt-input-textarea"
							rows={ 1 }
						/>
						<Button
							onClick={ handleSend }
							disabled={ isLoading || ! input.trim() }
							className="cmd-cnt-input-submit"
							label="Send Message"
							icon={ <Icon icon={ arrowUp } /> }
							variant="primary"
						/>
					</div>
				</div>
			</PanelBody>
		</Panel>
	);
};
