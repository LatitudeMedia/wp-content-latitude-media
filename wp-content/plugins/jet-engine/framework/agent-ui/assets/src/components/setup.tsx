import {
	Panel,
	PanelBody,
	TextControl,
	Flex,
	FlexBlock,
	Button
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

import {
	useState
} from '@wordpress/element';

export const Setup = () => {

	const [ apiKey, setApiKey ] = useState( '' );
	const [ isSaving, setIsSaving ] = useState( false );

	const saveAPIKey = ( key: string ) => {

		if ( isSaving ) {
			return;
		}

		setIsSaving( true );

		apiFetch( {
			url: window.crocoAgentUI.api_key_endpoint,
			method: 'POST',
			data: {
				api_key: key,
				nonce: window.crocoAgentUI.nonce,
			}
		} ).then( ( response: any ) => {
			window.location.reload();
			setIsSaving( false );
		} ).catch( ( error: any ) => {
			console.error( error );
			setIsSaving( false );
		} );
	};

	return (
		<Panel header="Setup">
			<PanelBody>
				<Flex
					align="flex-start"
				>
					<FlexBlock>
						<TextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label="Please enter your Open AI API key to get started:"
							value={ apiKey }
							onChange={ ( value ) => setApiKey( value ) }
							type="password"
							help="You can create an API key in your Open AI account settings. This API key is stored as an encrypted string so can't be retrieved directly from the DB."
						/>
					</FlexBlock>
					<Button
						__next40pxDefaultSize
						variant="primary"
						onClick={ () => {
							saveAPIKey( apiKey );
						} }
						disabled={ ! apiKey || isSaving }
						isBusy={ isSaving }
						style={ { marginTop: '23px' } }
					>
						Set API Key
					</Button>
				</Flex>
			</PanelBody>
		</Panel>
	);
};

