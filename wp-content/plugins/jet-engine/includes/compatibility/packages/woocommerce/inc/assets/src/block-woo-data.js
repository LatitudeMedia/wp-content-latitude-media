const {
	ServerSideRender
} = window.JetEngineBlocksComponents;

const { __ } = wp.i18n;

const {
	registerBlockType
} = wp.blocks;

const {
	InspectorControls,
	useBlockProps,
} = wp.blockEditor;

const {
	SelectControl,
	ToggleControl,
	PanelBody,
	Disabled,
	Path,
	SVG,
} = wp.components;

const Icon = <SVG width="24" height="24" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><Path fill-rule="evenodd" clip-rule="evenodd" d="M24.375 9C24.703 9 24.9727 9.10599 25.1836 9.31836C25.3945 9.53086 25.5 9.80323 25.5 10.1338C25.5001 11.385 25.9337 12.4593 26.8008 13.3564C27.6914 14.23 28.7579 14.667 30 14.667C31.2421 14.667 32.2969 14.23 33.1641 13.3564C34.0546 12.4593 34.4999 11.385 34.5 10.1338C34.5 9.80323 34.6055 9.53086 34.8164 9.31836C35.0273 9.10599 35.297 9 35.625 9H41.25C41.3672 9 41.4727 9.02407 41.5664 9.07129C41.6835 9.09489 41.7656 9.10645 41.8125 9.10645L47.4375 12.6123C47.6016 12.7304 47.7305 12.8844 47.8242 13.0732C47.9413 13.2385 48 13.4272 48 13.6396V21.6553C48 21.9857 47.8944 22.2573 47.6836 22.4697C47.4727 22.6822 47.2031 22.7891 46.875 22.7891H42.375V34.0029C44.8739 34.0348 47.203 34.3838 48.9961 34.9814C49.9268 35.2917 50.8244 35.7054 51.5303 36.2617C52.2136 36.8003 53 37.7103 53 39V51C53 52.2897 52.2136 53.1997 51.5303 53.7383C50.8244 54.2946 49.9268 54.7083 48.9961 55.0186C47.1136 55.646 44.6403 56 42 56C39.3597 56 36.8864 55.646 35.0039 55.0186C34.0732 54.7083 33.1756 54.2946 32.4697 53.7383C31.7864 53.1997 31 52.2897 31 51V43H18.75C18.422 43 18.1523 42.894 17.9414 42.6816C17.7305 42.4691 17.625 42.1968 17.625 41.8662V22.7891H13.125C12.7969 22.7891 12.5273 22.6822 12.3164 22.4697C12.1056 22.2573 12 21.9857 12 21.6553V13.6396C12 13.4273 12.047 13.2385 12.1406 13.0732C12.2578 12.8844 12.3984 12.7304 12.5625 12.6123L18.1875 9.10645C18.2344 9.10645 18.3047 9.09488 18.3984 9.07129C18.5156 9.02407 18.6328 9 18.75 9H24.375ZM51 48.1094C50.3985 48.4843 49.7069 48.7816 48.9961 49.0186C47.1136 49.646 44.6403 50 42 50C39.3597 50 36.8864 49.646 35.0039 49.0186C34.2931 48.7816 33.6015 48.4843 33 48.1094V51C33 52.6569 37.0294 54 42 54C46.9706 54 51 52.6569 51 51V48.1094ZM51 42.1094C50.3985 42.4843 49.7069 42.7816 48.9961 43.0186C47.1136 43.646 44.6403 44 42 44C39.3597 44 36.8864 43.646 35.0039 43.0186C34.2931 42.7816 33.6015 42.4843 33 42.1094V45C33 45.7684 33.8669 46.4692 35.292 47C36.9399 47.6137 39.3346 48 42 48C44.6654 48 47.0601 47.6137 48.708 47C48.7887 46.97 48.8683 46.9393 48.9453 46.9082C50.229 46.3897 51 45.7248 51 45V42.1094ZM42 36C37.0294 36 33 37.3431 33 39C33 39.7248 33.771 40.3897 35.0547 40.9082C35.1317 40.9393 35.2113 40.97 35.292 41C36.9399 41.6137 39.3346 42 42 42C44.6654 42 47.0601 41.6137 48.708 41C50.1331 40.4692 51 39.7684 51 39C51 37.3431 46.9706 36 42 36ZM14 14.0732V20.7891H19.625V41H31V39C31 37.7103 31.7864 36.8003 32.4697 36.2617C33.1756 35.7054 34.0731 35.2917 35.0039 34.9814C36.4941 34.4847 38.3545 34.1599 40.375 34.0459V20.7891H46V14.0732L41.0957 11.0166L41.0156 11H36.4463C36.2662 12.4314 35.6336 13.7073 34.583 14.7656C33.3366 16.0211 31.7695 16.667 30 16.667C28.2316 16.667 26.6631 16.0227 25.4004 14.7842L25.3809 14.7656L25.3623 14.7461C24.3383 13.6865 23.7278 12.4171 23.5527 11H18.9307L14 14.0732Z" fill="currentColor"/></SVG>;

registerBlockType( 'jet-engine/woo-data', {
	apiVersion: "3",
	title: __( 'Woo Data' ),
	icon: Icon,
	category: 'jet-engine',
	attributes: {
		data_type: {
			type: 'string',
			default: 'hook',
		},
		hook_name: {
			type: 'string',
			default: 'woocommerce_after_shop_loop_item_title',
		},
		core_callbacks: {
			type: 'boolean',
			default: true,
		},
		template_function: {
			type: 'string',
			default: 'woocommerce_template_loop_add_to_cart',
		},
		add_link: {
			type: 'boolean',
			default: false,
		},
	},
	className: 'jet-listing-dynamic-field',
	usesContext: [ 'postId', 'postType', 'queryId' ],
	edit: function( props ) {

		const blockProps       = useBlockProps();
		const allowedTypes     = window.JetEngineWooData.allowed_types;
		const allowedHooks     = window.JetEngineWooData.allowed_hooks;
		const allowedFunctions = window.JetEngineWooData.allowed_functions;
		const allowLinks	   = window.JetEngineWooData.allow_links;
		const attributes       = props.attributes;
		const currentObject    = window.JetEngineListingData.object_id;
		const listing          = window.JetEngineListingData.settings;

		return [
			props.isSelected && (
					<InspectorControls
						key={ 'inspector' }
					>
						<PanelBody title={ __( 'General' ) }>
							<SelectControl
								label={ __( 'Data Type' ) }
								value={ attributes.data_type }
								options={ allowedTypes }
								onChange={ newValue => {
									props.setAttributes({ data_type: newValue });
								}}
							/>
							{ 'hook' === attributes.data_type &&
								<>
									<SelectControl
										label={ __( 'Hook Name' ) }
										value={ attributes.hook_name }
										options={ allowedHooks }
										help={ __( 'Woo hook to call. All callbacks attached to this hook will also be called.' ) }
										onChange={ newValue => {
											props.setAttributes({
												hook_name: newValue,
											});
										}}
									/>
									<ToggleControl
										label={ __( 'Core Callbacks' ) }
										help={ __( 'Disable this toggle to prevent default Woo callbacks from being called on this hook.' ) }
										checked={ attributes.core_callbacks }
										onChange={ () => {
											props.setAttributes({ core_callbacks: ! attributes.core_callbacks });
										} }
									/>
								</>
							}
							{ 'template_function' === attributes.data_type &&
								<>
									<SelectControl
										label={ __( 'Template Function' ) }
										value={ attributes.template_function }
										options={ allowedFunctions }
										onChange={ newValue => {
											props.setAttributes({
												template_function: newValue,
											});
										}}
									/>
									{ allowLinks.includes( attributes.template_function ) &&
										<ToggleControl
											label={ __( 'Add Link' ) }
											help={ __( 'Wrap the function result with a link to the single product page.' ) }
											checked={ attributes.add_link }
											onChange={ () => {
												props.setAttributes({ add_link: ! attributes.add_link });
											} }
										/>
									}
								</>
							}
						</PanelBody>
					</InspectorControls>
			),
			<div key={ 'block_render' } { ...blockProps }>
				<ServerSideRender
					block="jet-engine/woo-data"
					attributes={ attributes }
					urlQueryArgs={ {
						object: currentObject,
						listing: listing
					} }
				/>
			</div>
		];
	},
	save: props => {
		return null;
	}
} );
