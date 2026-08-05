import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, SelectControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';

export default function Edit( { attributes, setAttributes, name } ) {
	const { title, disclaimer, embedCode, layout } = attributes;
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Subscriber Form', 'ltm' ) }>
					<TextControl
						label={ __( 'Title', 'ltm' ) }
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
					/>
					<TextareaControl
						label={ __( 'Disclaimer', 'ltm' ) }
						value={ disclaimer }
						onChange={ ( value ) => setAttributes( { disclaimer: value } ) }
					/>
					<TextareaControl
						label={ __( 'Embed Code', 'ltm' ) }
						help={ __( 'Raw HTML/script embed code, e.g. a HubSpot form.', 'ltm' ) }
						value={ embedCode }
						onChange={ ( value ) => setAttributes( { embedCode: value } ) }
					/>
					<SelectControl
						label={ __( 'Layout', 'ltm' ) }
						value={ layout }
						options={ [ { label: __( 'Square', 'ltm' ), value: 'square' } ] }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender block={ name } attributes={ attributes } />
			</div>
		</>
	);
}
