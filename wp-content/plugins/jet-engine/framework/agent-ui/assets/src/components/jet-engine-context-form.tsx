import {
	Button,
	ToggleControl
} from '@wordpress/components';

import { useState } from '@wordpress/element';

import { useConversation } from '../hooks/use-conversation';

type Props = {
	onClose: () => void;
};

const JET_ENGINE_COMPONENTS = [
	{
		name: 'post_types',
		label: 'Post Types',
		description: 'Provides information about custom post types registered via JetEngine.',
	},
	{
		name: 'taxonomies',
		label: 'Taxonomies',
		description: 'Provides information about custom taxonomies registered via JetEngine.',
	},
	{
		name: 'meta_boxes',
		label: 'Meta Boxes',
		description: 'Provides information about meta boxes and their fields registered via JetEngine.',
	},
	{
		name: 'options_pages',
		label: 'Options Pages',
		description: 'Provides information about options pages registered via JetEngine.',
	},
	{
		name: 'queries',
		label: 'Query Builder',
		description: 'Provides information about queries created with Query Builder.',
	},
	{
		name: 'relations',
		label: 'Relations',
		description: 'Provides information about relations registered with Relations module.',
	},
	{
		name: 'content_types',
		label: 'Custom Content Types',
		description: 'Provides information about custom content types registered with Content Types module.',
	},
	{
		name: 'rest_api_endpoints',
		label: 'REST API Endpoints',
		description: 'Provides information about REST API endpoints registered with REST API listings module.',
	},
	{
		name: 'glossaries',
		label: 'Glossaries',
		description: 'Provides information about glossaries created with JetEngine (up to 100 items per each glossary).',
	},
]

export const JetEngineContextForm = ({ onClose }: Props) => {

	const getDefaultSelectedState = () => {
		return JET_ENGINE_COMPONENTS.reduce( ( acc, component ) => {
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
				Select the relevant JetEngine configuration options you want to provide as context for the AI agent. This will help the agent understand your setup and provide more accurate assistance. You can repeat this process any time you want. Previously provided context will be forgotten when the conversation is cleared.
			</div>
			<div className="cmd-cnt-je-context-form">
				{ JET_ENGINE_COMPONENTS.map( ( component ) => (
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
							'resource-get-configuration',
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
