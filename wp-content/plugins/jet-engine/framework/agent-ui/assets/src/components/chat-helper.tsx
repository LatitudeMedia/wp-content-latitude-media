import {
	Button,
	Panel,
	PanelBody,
	Modal,
	SelectControl,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import { useConversation } from '../hooks/use-conversation';
import { JetEngineContextForm } from './jet-engine-context-form';
import { WebsiteContextForm } from './website-context-form';
import { MacrosContextForm } from './macros-context-form';

import type { Tool } from '../types/messages';
import { ToolParameterInfo } from './tool-parameter-info';

export const ChatHelper = () => {

	const { toolExecutor, model, setModel, clearLocalStorage } = useConversation();
	const [ showJEContext, setShowJEContext ] = useState( false );
	const [ showSiteContext, setShowSiteContext ] = useState( false );
	const [ showMacrosContext, setShowMacrosContext ] = useState( false );
	const [ toolInfo, setToolInfo ] = useState<Tool | null>( null );
	const [ modelsList, setModelsList ] = useState< Array<{ label: string; value: string }> >( [] );
	const [ savingModel, setSavingModel ] = useState( false );

	useEffect( () => {
		apiFetch( {
			url: window.crocoAgentUI.models_endpoint,
			method: 'GET',
		} ).then( ( response: any ) => {
			if ( Array.isArray( response ) ) {
				const options = response.map( ( model: string ) => ( {
					label: model,
					value: model,
				} ) );
				setModelsList( options );
			}
		} ).catch( ( error: any ) => {
			console.error( error );
		} );
	}, [] );

	if ( ! toolExecutor ) {
		return null;
	}

	return (
		<Panel header="Advanced Helper" className="cmd-cnt-chat-helper">
			<PanelBody>
				<h4 className="cmd-cnt-helper-header">Add Context:</h4>
				<div className="cmd-cnt-helper-section">
					<Button
						variant="secondary"
						size="small"
						onClick={ () => {
							setShowJEContext( true );
						} }
					>
						Add JetEngine Configuration Context
					</Button>
					<Button
						variant="secondary"
						size="small"
						onClick={ () => {
							setShowSiteContext( true );
						} }
					>
						Add Website Configuration Context
					</Button>
					<Button
						variant="secondary"
						size="small"
						onClick={ () => {
							setShowMacrosContext( true );
						} }
					>
						Add Allowed Macros Context
					</Button>
					<div style={ { marginTop: '8px' } }>
						<b>Note:</b> If you often use adding context tools it may cause increased token usage and high costs. So use them wisely and monitor your usage at the Open AI dashboard.
					</div>
				</div>
				<h4 className="cmd-cnt-helper-header">Registered Tools/Resources:</h4>
				<ul className="cmd-cnt-tool-list">
					{ toolExecutor.listTools().map( ( tool ) => (
						<li key={ tool.name } className="cmd-cnt-tool-item">
							<a
								href="#"
								onClick={ ( e ) => {
									e.preventDefault();
									setToolInfo( tool );
								} }
								title={ tool.label }
							>
								{ tool.label }
							</a>
						</li>
					) ) }
				</ul>
				<h4 className="cmd-cnt-helper-header">Settings:</h4>
				<div className="cmd-cnt-helper-section">
					<div className="cmd-cnt-helper-row">
						<SelectControl
							label="Select Language Model:"
							value={ model }
							options={ modelsList }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							onChange={ ( value ) => {
								setModel( value );
							} }
						/>
						<Button
							variant="secondary"
							__next40pxDefaultSize
							isBusy={ savingModel }
							disabled={ savingModel }
							onClick={ () => {

								if ( savingModel ) {
									return;
								}

								setSavingModel( true );

								apiFetch( {
									url: window.crocoAgentUI.save_model_endpoint,
									method: 'POST',
									data: {
										model: model,
										nonce: window.crocoAgentUI.nonce,
									}
								} ).then( () => {
									window.location.reload();
								} ).catch( ( error: any ) => {
									console.error( error );
								} );
							} }
						>
							Save & Clear Context
						</Button>
					</div>
					<div>
						<Button
							variant="secondary"
							isDestructive
							__next40pxDefaultSize
							onClick={ () => {
								if ( confirm( 'Are you sure you want to reset the API connection? This will remove your API key and all saved context.' ) ) {

									apiFetch( {
										url: window.crocoAgentUI.api_key_endpoint,
										method: 'POST',
										data: {
											api_key: 'reset',
											nonce: window.crocoAgentUI.nonce,
										}
									} ).then( () => {
										clearLocalStorage();
										window.location.reload();
									} ).catch( ( error: any ) => {
										console.error( error );
									} );
								}
							} }
						>
							Reset API connection
						</Button>
					</div>
				</div>
			</PanelBody>
			{ showJEContext && <Modal
				title="Provide JetEngine Configuration Context"
				onRequestClose={ () => setShowJEContext( false ) }
				size="large"
				className="cmd-cnt-modal"
			>
				<JetEngineContextForm
					onClose={ () => setShowJEContext( false ) }
				/>
			</Modal> }
			{ showSiteContext && <Modal
				title="Provide Website Configuration Context"
				onRequestClose={ () => setShowSiteContext( false ) }
				size="large"
				className="cmd-cnt-modal"
			>
				<WebsiteContextForm
					onClose={ () => setShowSiteContext( false ) }
				/>
			</Modal> }
			{ showMacrosContext && <Modal
				title="Provide Allowed Macros Context"
				onRequestClose={ () => setShowMacrosContext( false ) }
				size="large"
				className="cmd-cnt-modal"
			>
				<MacrosContextForm
					onClose={ () => setShowMacrosContext( false ) }
				/>
			</Modal> }
			{ null !== toolInfo && <Modal
				title={ toolInfo.label || 'Tool/Resource Info' }
				onRequestClose={ () => setToolInfo( null ) }
				size="large"
				className="cmd-cnt-modal"
			>
				<div className="cmd-cnt-note">
					{ toolInfo.description }
				</div>
				<div className="cmd-cnt-tool-params">
					{ toolInfo.parameters && (
						<>
							<h4>Parameters:</h4>
							{ Object.keys( toolInfo.parameters.properties ).length > 0 && (
								<ul>
									{ Object.entries( toolInfo.parameters.properties ).map( ( [ key, value ] ) => (
										<ToolParameterInfo
											key={ key }
											name={ key }
											schema={ value as object }
										/>
									) ) }
								</ul>
							) }
							{ Object.keys( toolInfo.parameters.properties ).length === 0 && (
								<p>No parameters defined.</p>
							) }
						</>
					) }
				</div>
			</Modal> }
		</Panel>
	);
};
