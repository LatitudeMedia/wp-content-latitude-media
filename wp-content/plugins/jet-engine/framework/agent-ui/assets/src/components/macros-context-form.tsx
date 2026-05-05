import {
	Button
} from '@wordpress/components';

import { useState } from '@wordpress/element';

import { useConversation } from '../hooks/use-conversation';

type Props = {
	onClose: () => void;
};

export const MacrosContextForm = ({ onClose }: Props) => {

	const [ processing, setProcessing ] = useState( false );
	const [ agentResponse, setAgentResponse ] = useState<string | null>( null );

	const { toolExecutor } = useConversation();

	if ( ! toolExecutor ) {
		return null;
	}

	return (
		<div className="cmd-cnt-modal-content">
			<div className="cmd-cnt-note">
				Click the button below to provide the AI agent with the list of allowed macros that can be used in the tools called in current conversation. You can repeat this process any time you want. Previously provided context will be forgotten when the conversation is cleared.
			</div>
			<div className="cmd-cnt-modal-actions">
				<Button
					variant="primary"
					disabled={ processing }
					isBusy={ processing }
					onClick={ () => {
						setProcessing( true );
						toolExecutor?.executeTool(
							'resource-get-macros',
							{}
						).then( ( result ) => {
							setProcessing( false );

							if ( result && result.content ) {
								setAgentResponse( 'Done!' );
							} else if ( result && result.error ) {
								// check if result.error is string
								if ( typeof result.error === 'string' ) {
									setAgentResponse( result.error );
								} else if ( result.error.message ) {
									setAgentResponse( result.error.message );
								} else {
									setAgentResponse( 'Unknown error occurred' );
								}
							}
						} ).catch( ( result ) => {

							setProcessing( false );

							if ( result && result.error ) {
								// check if result.error is string
								if ( typeof result.error === 'string' ) {
									setAgentResponse( result.error );
								} else if ( result.error.message ) {
									setAgentResponse( result.error.message );
								} else {
									setAgentResponse( 'Unknown error occurred' );
								}
							}
						} );
					} }
				>
					Provide Macros Context
				</Button>
				<Button
					variant="secondary"
					onClick={ onClose }
					disabled={ processing }
				>
					Close
				</Button>
				{ agentResponse && (
					<div className="cmd-cnt-agent-response">
						Agent response: { agentResponse }
					</div>
				) }
			</div>
		</div>
	);
};
