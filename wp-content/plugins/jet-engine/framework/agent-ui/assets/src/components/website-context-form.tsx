import {
	Button,
	ToggleControl
} from '@wordpress/components';

import { useState } from '@wordpress/element';

import { useConversation } from '../hooks/use-conversation';

type Props = {
	onClose: () => void;
};

const WEBSITE_COMPONENTS = [
	{
		name: 'post_types',
		label: 'Post Types',
		description: 'Provides information about all custom post types registered on your website.',
	},
	{
		name: 'taxonomies',
		label: 'Taxonomies',
		description: 'Provides information about all custom taxonomies registered on your website.',
	},
	{
		name: 'plugins',
		label: 'Plugins',
		description: 'Provides information about all plugins installed and activated on your website.',
	},
]

export const WebsiteContextForm = ({ onClose }: Props) => {

	const getDefaultSelectedState = () => {
		return WEBSITE_COMPONENTS.reduce( ( acc, component ) => {
			acc[component.name] = true;
			return acc;
		}, {} as Record<string, boolean> );
	};

	const [ processing, setProcessing ] = useState( false );
	const [ agentResponse, setAgentResponse ] = useState<string | null>( null );

	const { toolExecutor } = useConversation();

	if ( ! toolExecutor ) {
		return null;
	}

	const [
		selectedComponents,
		setSelectedComponents
	] = useState<Record<string, boolean>>( getDefaultSelectedState() );

	return (
		<div className="cmd-cnt-modal-content">
			<div className="cmd-cnt-note">
				Select the relevant website configuration options you want to provide as context for the AI agent. This will help the agent understand your setup and provide more accurate assistance. You can repeat this process any time you want. Previously provided context will be forgotten when the conversation is cleared.
			</div>
			<div className="cmd-cnt-je-context-form">
				{ WEBSITE_COMPONENTS.map( ( component ) => (
					<ToggleControl
						key={ component.name }
						label={ component.label }
						checked={ selectedComponents[ component.name ] }
						onChange={ ( value ) => {
							setSelectedComponents( {
								...selectedComponents,
								[ component.name ]: value,
							} );
						} }
						help={ component.description }
					/>
				) ) }
			</div>
			<div className="cmd-cnt-modal-actions">
				<Button
					variant="primary"
					disabled={ processing }
					isBusy={ processing }
					onClick={ () => {
						setProcessing( true );
						toolExecutor?.executeTool(
							'resource-get-website-config',
							{ parts: selectedComponents }
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
					Provide Selected Context
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
