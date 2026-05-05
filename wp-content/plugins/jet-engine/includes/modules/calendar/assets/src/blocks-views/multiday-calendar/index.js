const { __ } = wp.i18n;
const {
	registerBlockType
} = wp.blocks;

const {
	InspectorControls,
	useBlockProps,
} = wp.blockEditor;

const {
	TextControl,
	TextareaControl,
	SelectControl,
	ToggleControl,
	PanelBody,
	Disabled,
	Path,
	SVG
} = wp.components;

const {
	ServerSideRender
} = window.JetEngineBlocksComponents;

const GIcon = <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M47 47C47.5523 47 48 47.4477 48 48V54C48 54.5523 47.5523 55 47 55H17C16.4477 55 16 54.5523 16 54V48C16 47.4477 16.4477 47 17 47H47ZM18 53H46V49H18V53Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M23 36C23.5523 36 24 36.4477 24 37V43C24 43.5523 23.5523 44 23 44H17C16.4477 44 16 43.5523 16 43V37C16 36.4477 16.4477 36 17 36H23ZM18 42H22V38H18V42Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M35 36C35.5523 36 36 36.4477 36 37V43C36 43.5523 35.5523 44 35 44H29C28.4477 44 28 43.5523 28 43V37C28 36.4477 28.4477 36 29 36H35ZM30 42H34V38H30V42Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M47 36C47.5523 36 48 36.4477 48 37V43C48 43.5523 47.5523 44 47 44H41C40.4477 44 40 43.5523 40 43V37C40 36.4477 40.4477 36 41 36H47ZM42 42H46V38H42V42Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M23 25C23.5523 25 24 25.4477 24 26V32C24 32.5523 23.5523 33 23 33H17C16.4477 33 16 32.5523 16 32V26C16 25.4477 16.4477 25 17 25H23ZM18 31H22V27H18V31Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M47 25C47.5523 25 48 25.4477 48 26V32C48 32.5523 47.5523 33 47 33H29C28.4477 33 28 32.5523 28 32V26C28 25.4477 28.4477 25 29 25H47ZM30 31H46V27H30V31Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M46 5C46.5523 5 47 5.44772 47 6V8H52C54.2091 8 56 9.79086 56 12V56C56 58.2091 54.2091 60 52 60H12C9.79086 60 8 58.2091 8 56V12C8 9.79086 9.79086 8 12 8H17V6C17 5.44772 17.4477 5 18 5C18.5523 5 19 5.44772 19 6V8H45V6C45 5.44772 45.4477 5 46 5ZM10 56C10 57.1046 10.8954 58 12 58H52C53.1046 58 54 57.1046 54 56V22H10V56ZM12 10C10.8954 10 10 10.8954 10 12V20H54V12C54 10.8954 53.1046 10 52 10H47V12C47 12.5523 46.5523 13 46 13C45.4477 13 45 12.5523 45 12V10H19V12C19 12.5523 18.5523 13 18 13C17.4477 13 17 12.5523 17 12V10H12Z" fill="currentColor"/></svg>;

const blockAttributes = window.JetEngineListingData.atts.listingMultidayCalendar;

registerBlockType( 'jet-engine/listing-multiday-calendar', {
	title: __( 'Multi-Day Calendar' ),
	apiVersion: 3,
	icon: GIcon,
	category: 'jet-engine',
	attributes: blockAttributes,
	className: 'jet-multiday-listing-calendar',
	edit: ( props ) => {

		const attributes       = props.attributes;
		const listingOptions   = window.JetEngineListingData.listingOptions;
		const hideOptions      = window.JetEngineListingData.hideOptions;

		return [
			props.isSelected && (
				<InspectorControls
					key={ 'inspector' }
				>
					<PanelBody title={ __( 'General' ) }>
						<SelectControl
							label={ __( 'Listing' ) }
							value={ attributes.lisitng_id }
							options={ listingOptions }
							onChange={ newValue => {
								props.setAttributes( { lisitng_id: newValue } );
							} }
						/>
						<SelectControl
							label={ __( 'Group posts by' ) }
							value={ attributes.group_by }
							options={ blockAttributes.group_by.options }
							onChange={ newValue => {
								props.setAttributes( { group_by: newValue } );
							} }
						/>
						{ 'meta_date' === attributes.group_by && <>
							<TextControl
								type="text"
								label={ __( 'Meta field name' ) }
								help={ __( 'This field must contain date to group posts by. Works only if "Save as timestamp" option for meta field is active' ) }
								value={ attributes.group_by_key }
								onChange={ newValue => {
									props.setAttributes( { group_by_key: newValue } );
								} }
							/>
							<TextControl
								type="text"
								label={ __( 'End date field name' ) }
								help={ __( 'This field must contain date when event ends. Works only if "Save as timestamp" option for meta field is active' ) }
								value={ attributes.end_date_key }
								onChange={ newValue => {
									props.setAttributes( { end_date_key: newValue } );
								} }
							/>
						</> }
						<hr/>
						<SelectControl
							label={ __( 'Week days format' ) }
							value={ attributes.week_days_format }
							options={ [
								{
									value: 'full',
									label: __( 'Full' ),
								},
								{
									value: 'short',
									label: __( 'Short' ),
								},
								{
									value: 'initial',
									label: __( 'Initial letter' ),
								},
							] }
							onChange={ newValue => {
								props.setAttributes( { week_days_format: newValue } );
							} }
						/>
						<ToggleControl
							label={ __( 'Start from custom month' ) }
							checked={ attributes.custom_start_from }
							onChange={ () => {
								props.setAttributes( { custom_start_from: ! attributes.custom_start_from } );
							} }
						/>
						{ attributes.custom_start_from &&
							<SelectControl
								label={ __( 'Start from month' ) }
								value={ attributes.start_from_month }
								options={ [
									{
										value: 'January',
										label: __( 'January' ),
									},
									{
										value: 'February',
										label: __( 'February' ),
									},
									{
										value: 'March',
										label: __( 'March' ),
									},
									{
										value: 'April',
										label: __( 'April' ),
									},
									{
										value: 'May',
										label: __( 'May' ),
									},
									{
										value: 'June',
										label: __( 'June' ),
									},
									{
										value: 'July',
										label: __( 'July' ),
									},
									{
										value: 'August',
										label: __( 'August' ),
									},
									{
										value: 'September',
										label: __( 'September' ),
									},
									{
										value: 'October',
										label: __( 'October' ),
									},
									{
										value: 'November',
										label: __( 'November' ),
									},
									{
										value: 'December',
										label: __( 'December' ),
									},
								] }
								onChange={ newValue => {
									props.setAttributes( { start_from_month: newValue } );
								} }
							/>
						}
						{ attributes.custom_start_from &&
							<TextControl
								type="text"
								label={ __( 'Start from year' ) }
								value={ attributes.start_from_year }
								onChange={ newValue => {
									props.setAttributes( { start_from_year: newValue } );
								} }
							/>
						}
						<ToggleControl
							label={ __( 'Show posts from the nearby months' ) }
							checked={ attributes.show_posts_nearby_months }
							onChange={ () => {
								props.setAttributes( { show_posts_nearby_months: ! attributes.show_posts_nearby_months } );
							} }
						/>
						<ToggleControl
							label={ __( 'Hide past events' ) }
							checked={ attributes.hide_past_events }
							onChange={ () => {
								props.setAttributes( { hide_past_events: ! attributes.hide_past_events } );
							} }
						/>
						<ToggleControl
							label={ __( 'Allow date select' ) }
							checked={ attributes.allow_date_select }
							onChange={ () => {
								props.setAttributes( { allow_date_select: ! attributes.allow_date_select } );
							} }
						/>
						{ attributes.allow_date_select && ! attributes.hide_past_events &&
							<TextControl
								type="text"
								label={ __( 'Min select year' ) }
								value={ attributes.start_year_select }
								onChange={ newValue => {
									props.setAttributes( { start_year_select: newValue } );
								} }
							/>
						}
						{ attributes.allow_date_select &&
							<TextControl
								type="text"
								label={ __( 'Max select year' ) }
								help={ __( 'You may use JetEngine macros in min/max select year. Also, you may use strings like \'+3years\', \'-1year\', \'this year\' to set year value relative to the curent year.' ) }
								value={ attributes.end_year_select }
								onChange={ newValue => {
									props.setAttributes( { end_year_select: newValue } );
								} }
							/>
						}
						<ToggleControl
							label={ __( 'Cache Calendar' ) }
							checked={ attributes.cache_enabled }
							onChange={ () => {
								props.setAttributes( { cache_enabled: ! attributes.cache_enabled } );
							} }
						/>
						{ attributes.cache_enabled &&
						<TextControl
							type="number"
							label={ __( 'Cache Timeout' ) }
							help={ __( 'Cache timeout in seconds. Set -1 for unlimited.' ) }
							value={ attributes.cache_timeout }
							min="-1"
							max="86400"
							onChange={ newValue => {
								props.setAttributes( { cache_timeout: newValue } );
							} }
						/> }
						{ attributes.cache_enabled &&
						<TextControl
							type="number"
							label={ __( 'Maximum Cache Size' ) }
							help={ __( 'Maximum cache size (months). If number of cached month exceeds this number - the oldest month will be deleted from cache.' ) }
							value={ attributes.max_cache }
							min="1"
							max="120"
							onChange={ newValue => {
								props.setAttributes( { max_cache: newValue } );
							} }
						/> }
						<SelectControl
							label={ __( 'Caption Layout' ) }
							value={ attributes.caption_layout }
							options={ [
								{
									value: 'layout-1',
									label: __( 'Layout 1' ),
								},
								{
									value: 'layout-2',
									label: __( 'Layout 2' ),
								},
								{
									value: 'layout-3',
									label: __( 'Layout 3' ),
								},
								{
									value: 'layout-4',
									label: __( 'Layout 4' ),
								},

							] }
							onChange={ newValue => {
								props.setAttributes( { caption_layout: newValue } );
							} }
						/>
					</PanelBody>

					<PanelBody
						title={ __( 'Event Badge Content' ) }
						initialOpen={ false }
					>
						<TextareaControl
							label={ __( 'Badge Content' ) }
							help={ __( 'Supports HTML tags, JetEngine macros and shortcodes.' ) }
							value={ attributes.event_content }
							onChange={ newValue => {
								props.setAttributes( { event_content: newValue } );
							} }
						/>
						<ToggleControl
							label={ __( 'Badge Marker' ) }
							checked={ attributes.event_marker }
							help={ __( 'Show event badge dot marker for each event in the calendar' ) }
							onChange={ () => {
								props.setAttributes( { event_marker: ! attributes.event_marker } );
							} }
						/>
						<ToggleControl
							label={ __( 'Use Dynamic Styles' ) }
							checked={ attributes.use_dynamic_styles }
							help={ __( 'Allows setting badge color, background, border color and dot color based on the specific event data.' ) }
							onChange={ () => {
								props.setAttributes( { use_dynamic_styles: ! attributes.use_dynamic_styles } );
							} }
						/>
						{ attributes.use_dynamic_styles && (
							<p className="components-base-control__help">
								{ __( 'Specific event badge styles could be set only by using JetEngine macros or shortcodes. Generated macro or shortcode must return a color value.' ) }
							</p>
						) }
						{ attributes.use_dynamic_styles && (
							<TextControl
								type="text"
								label={ __( 'Badge Text Color' ) }
								help={ __( 'Defines the text color for the event badge.' ) }
								value={ attributes.dynamic_badge_color }
								onChange={ newValue => {
								props.setAttributes( { dynamic_badge_color: newValue } );
								} }
							/>
						) }
						{ attributes.use_dynamic_styles && (
							<TextControl
								type="text"
								label={ __( 'Badge Background Color' ) }
								help={ __( 'Defines the background color for the event badge.' ) }
								value={ attributes.dynamic_badge_bg_color }
								onChange={ newValue => {
								props.setAttributes( { dynamic_badge_bg_color: newValue } );
								} }
							/>
						) }
						{ attributes.use_dynamic_styles && (
							<TextControl
								type="text"
								label={ __( 'Badge Border Color' ) }
								help={ __( 'Defines the border color for the event badge.' ) }
								value={ attributes.dynamic_badge_border_color }
								onChange={ newValue => {
								props.setAttributes( { dynamic_badge_border_color: newValue } );
								} }
							/>
						) }
						{ attributes.use_dynamic_styles && (
							<TextControl
								type="text"
								label={ __( 'Badge Dot Color' ) }
								help={ __( 'Defines the dot color for the event badge.' ) }
								value={ attributes.dynamic_badge_dot_color }
								onChange={ newValue => {
								props.setAttributes( { dynamic_badge_dot_color: newValue } );
								} }
							/>
						) }
					</PanelBody>

					<PanelBody
						title={ __( 'Custom Query' ) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __( 'Use Custom Query' ) }
							checked={ attributes.custom_query }
							onChange={ () => {
								props.setAttributes({ custom_query: ! attributes.custom_query });
							} }
						/>
						{ attributes.custom_query && <SelectControl
							multiple={false}
							label={ __( 'Custom Query' ) }
							value={ attributes.custom_query_id }
							options={ window.JetEngineListingData.queriesList }
							onChange={ newValue => {
								props.setAttributes( { custom_query_id: newValue } );
							}}
						/> }
					</PanelBody>
					<PanelBody
						title={ __( 'Block Visibility' ) }
						initialOpen={ false }
					>
						<SelectControl
							label={ __( 'Hide block if' ) }
							value={ attributes.hide_widget_if }
							options={ hideOptions }
							onChange={ newValue => {
								props.setAttributes( { hide_widget_if: newValue } );
							} }
						/>
					</PanelBody>
				</InspectorControls>
			),
			<div { ...useBlockProps() }>
				<Disabled key={ 'block_render' }>
					<ServerSideRender
						block="jet-engine/listing-multiday-calendar"
						attributes={ attributes }
						httpMethod="POST"
					/>
				</Disabled>
			</div>
		];
	},
	save: props => {
		return null;
	}
} );
