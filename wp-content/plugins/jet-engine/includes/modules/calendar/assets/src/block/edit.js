import preview from './preview';

const {
	AdvancedFields,
	GeneralFields,
	ToolBarFields,
	FieldWrapper,
	StylePanel,
	StyleColorItem,
	StyleColorItemsWrapper,
	StyleSize,
} = JetFBComponents;

const {
	TextControl,
	ToolbarItem,
	ToggleControl,
	SelectControl,
	PanelBody,
} = wp.components;

const {
	__,
} = wp.i18n;

const {
	InspectorControls,
	useBlockProps,
} = wp.blockEditor;

const {
	useJetStyle,
} = JetFBHooks;

function AdvancedDateFieldEdit( props ) {

	const jetStyle = useJetStyle?.( {
		className: [
			'jet-form-builder-row',
			'field-type-switcher',
		].join( ' ' ),
	} ) ?? {};

	const blockProps = useBlockProps( jetStyle );

	const {
		attributes,
		setAttributes,
		isSelected,
		editProps: { uniqKey },
	} = props;

	if ( attributes.isPreview ) {
		return <div style={ {
			width: '100%',
			display: 'flex',
			justifyContent: 'center',
		} }>
			{ preview }
		</div>;
	}

	const formatOptions = [
		{
			value: 'rrule',
			label: __( 'Recurring Rule' ),
		},
		{
			value: 'manual',
			label: __( 'Manual Input' ),
		},
	];

	return [
		<ToolBarFields
			key={ uniqKey( 'ToolBarFields' ) }
			{ ...props }
		>
			<ToggleControl
				checked={ attributes.required && attributes.end_date_required }
				onChange={ ( value ) => {
					setAttributes( { end_date_required: value } );
				} }
				label={ __( 'End Date is Required' ) }
				disabled={ ! attributes.required }
				__nextHasNoMarginBottom
			/>
			<div></div>
		</ToolBarFields>,
		isSelected && <>
			<InspectorControls
				key={ uniqKey( 'InspectorControls' ) }
			>
				<GeneralFields
					key={ uniqKey( 'GeneralFields' ) }
					{ ...props }
				/>
				<PanelBody title={ __( 'Field Render Setting' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Recurrence Format' ) }
						value={ attributes.recurrency_format }
						options={ formatOptions }
						onChange={ ( newValue ) => {
							setAttributes( {
								recurrency_format: newValue
							} );
						}}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						checked={ attributes.allow_timepicker }
						label={ __( 'Allow timepicker' ) }
						onChange={ () => {
							setAttributes( {
								allow_timepicker: ! attributes.allow_timepicker
							} );
						} }
					/>
					<SelectControl
						label={ __( 'Save As', 'jet-engine' ) }
						value={ attributes.save_as }
						options={ [
							{ value: 'date', label: __( 'Date' ) },
							{ value: 'timestamp', label: __( 'Timestamp' ) },
						] }
						onChange={ ( newValue ) => {
							setAttributes( { save_as: newValue } );
						} }
					/>
					{ 'timestamp' !== attributes.save_as && <TextControl
						label={ __( 'Date Format', 'jet-engine' ) }
						help={ __( 'Please note, this defines format of saved date, not the input in the form', 'jet-engine' ) }
						value={ attributes.date_format }
						onChange={ ( newValue ) => {
							setAttributes( { date_format: newValue } );
						} }
					/> }
					{ 'timestamp' !== attributes.save_as && attributes.allow_timepicker && <TextControl
						label={ __( 'Time Format', 'jet-engine' ) }
						value={ attributes.time_format }
						help={ __( 'Please note, this defines format of saved time, not the input in the form', 'jet-engine' ) }
						onChange={ ( newValue ) => {
							setAttributes( { time_format: newValue } );
						} }
					/> }
				</PanelBody>
				<PanelBody
					title={ __( 'Labels' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __( 'Start Date' ) }
						value={ attributes.labels.startDate }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									startDate: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Has End Date' ) }
						value={ attributes.labels.hasEndDate }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									hasEndDate: newValue,
								}
							} );
						}}
					/>
					<TextControl
						value={ attributes.labels.isRecurring }
						label={ __( 'Is Recurring?' ) }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									isRecurring: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Repeat' ) }
						value={ attributes.labels.repeat }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									repeat: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Recurring Label Daily' ) }
						value={ attributes.labels.recurringLabelDaily }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									recurringLabelDaily: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Recurring Label Weekly' ) }
						value={ attributes.labels.recurringLabelWeekly }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									recurringLabelWeekly: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Recurring Label Monthly' ) }
						value={ attributes.labels.recurringLabelMonthly }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									recurringLabelMonthly: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Recurring Label Yearly' ) }
						value={ attributes.labels.recurringLabelYearly }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									recurringLabelYearly: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Every' ) }
						value={ attributes.labels.every }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									every: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Recurring Sub Label Daily' ) }
						value={ attributes.labels.recurringSubLabelDaily }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									recurringSubLabelDaily: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Recurring Sub Label Weekly' ) }
						value={ attributes.labels.recurringSubLabelWeekly }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									recurringSubLabelWeekly: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Recurring Sub Label Monthly' ) }
						value={ attributes.labels.recurringSubLabelMonthly }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									recurringSubLabelMonthly: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Recurring Sub Label Yearly' ) }
						value={ attributes.labels.recurringSubLabelYearly }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									recurringSubLabelYearly: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'On Day' ) }
						value={ attributes.labels.onDay }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									onDay: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'On The' ) }
						value={ attributes.labels.onThe }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									onThe: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'First' ) }
						value={ attributes.labels.first }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									first: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Second' ) }
						value={ attributes.labels.second }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									second: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Third' ) }
						value={ attributes.labels.third }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									third: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Fourth' ) }
						value={ attributes.labels.fourth }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									fourth: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Last' ) }
						value={ attributes.labels.last }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									last: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Day' ) }
						value={ attributes.labels.day }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									day: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Week Day Mon' ) }
						value={ attributes.labels.weekDayMon }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									weekDayMon: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Week Day Tue' ) }
						value={ attributes.labels.weekDayTue }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									weekDayTue: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Week Day Wed' ) }
						value={ attributes.labels.weekDayWed }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									weekDayWed: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Week Day Thu' ) }
						value={ attributes.labels.weekDayThu }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									weekDayThu: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Week Day Fri' ) }
						value={ attributes.labels.weekDayFri }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									weekDayFri: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Week Day Sat' ) }
						value={ attributes.labels.weekDaySat }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									weekDaySat: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Week Day Sun' ) }
						value={ attributes.labels.weekDaySun }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									weekDaySun: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Jan' ) }
						value={ attributes.labels.monthJan }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthJan: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Feb' ) }
						value={ attributes.labels.monthFeb }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthFeb: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Mar' ) }
						value={ attributes.labels.monthMar }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthMar: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Apr' ) }
						value={ attributes.labels.monthApr }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthApr: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month May' ) }
						value={ attributes.labels.monthMay }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthMay: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Jun' ) }
						value={ attributes.labels.monthJun }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthJun: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Jul' ) }
						value={ attributes.labels.monthJul }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthJul: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Aug' ) }
						value={ attributes.labels.monthAug }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthAug: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Sep' ) }
						value={ attributes.labels.monthSep }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthSep: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Oct' ) }
						value={ attributes.labels.monthOct }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthOct: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Nov' ) }
						value={ attributes.labels.monthNov }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthNov: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Month Dec' ) }
						value={ attributes.labels.monthDec }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									monthDec: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'End' ) }
						value={ attributes.labels.end }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									end: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'After' ) }
						value={ attributes.labels.after }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									after: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'On Date' ) }
						value={ attributes.labels.onDate }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									onDate: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Iterations' ) }
						value={ attributes.labels.iterations }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									iterations: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Confirm Delete' ) }
						value={ attributes.labels.confirmDelete }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									confirmDelete: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Confirm Delete Yes' ) }
						value={ attributes.labels.confirmDeleteYes }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									confirmDeleteYes: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Confirm Delete No' ) }
						value={ attributes.labels.confirmDeleteNo }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									confirmDeleteNo: newValue,
								}
							} );
						}}
					/>
					<TextControl
						label={ __( 'Add Date' ) }
						value={ attributes.labels.addDate }
						onChange={ ( newValue ) => {
							setAttributes( {
								labels: {
									...attributes.labels,
									addDate: newValue,
								}
							} );
						}}
					/>
				</PanelBody>
				<AdvancedFields
					key={ uniqKey( 'AdvancedFields' ) }
					{ ...props }
				/>
			</InspectorControls>
			<InspectorControls group="styles">
				<StylePanel
					label={ __( 'Toggles', 'jet-engine' ) }
				>
					<StyleColorItemsWrapper>
						<StyleColorItem
							cssVar="--thumb-bg-color"
							label={ __( 'Thumb Color', 'jet-form-builder' ) }
						/>
						<StyleColorItem
							cssVar="--track-bg-color"
							label={ __( 'Track Background', 'jet-form-builder' ) }
						/>
						<StyleColorItem
							cssVar="--checked-track-bg-color"
							label={ __(
								'Track Checked background',
								'jet-form-builder',
							) }
						/>
					</StyleColorItemsWrapper>
					<StyleSize
						cssVar="--c-thumb-size"
						label={ __( 'Thumb size', 'jet-form-builder' ) }
					/>
					<StyleSize
						cssVar="--c-track-size"
						label={ __( 'Track size', 'jet-form-builder' ) }
					/>
					<StyleSize
						cssVar="--c-track-padding"
						label={ __( 'Track padding', 'jet-form-builder' ) }
					/>
				</StylePanel>
				<StylePanel
					label={ __( 'Weekdays Switcher', 'jet-form-builder' ) }
				>
					<StyleColorItemsWrapper>
						<StyleColorItem
							cssVar="--wd-border-color"
							label={ __( 'Border Color', 'jet-form-builder' ) }
						/>
						<StyleColorItem
							cssVar="--wd-color"
							label={ __( 'Day Text Color', 'jet-form-builder' ) }
						/>
						<StyleColorItem
							cssVar="--wd-bg-color"
							label={ __( 'Day Background', 'jet-form-builder' ) }
						/>
						<StyleColorItem
							cssVar="--wd-checked-bg-color"
							label={ __(
								'Day Checked Background',
								'jet-form-builder',
							) }
						/>
					</StyleColorItemsWrapper>
				</StylePanel>
				<StylePanel
					label={ __( 'Manual Add Date', 'jet-engine' ) }
				>
					<StyleColorItemsWrapper>
						<StyleColorItem
							cssVar="--mn-button-color"
							label={ __( 'Color', 'jet-form-builder' ) }
						/>
						<StyleColorItem
							cssVar="--mn-button-hover-color"
							label={ __( 'Color Hover', 'jet-form-builder' ) }
						/>
					</StyleColorItemsWrapper>
				</StylePanel>
			</InspectorControls>
		</>,
		<div { ...blockProps } key={ uniqKey( 'viewBlock' ) }>
			<FieldWrapper
				key={ uniqKey( 'FieldWrapper' ) }
				{ ...props }
			>
				<div className="jfb-advanced-date-field">
					<TextControl
						onChange={ () => {
						} }
						type="date"
						disabled
						help={ __( '*This is a field placeholder. The actual layout will be rendered on the front end', 'jet-form-builder' ) }
					/>
				</div>
			</FieldWrapper>
		</div>
	];
}

export default AdvancedDateFieldEdit;
