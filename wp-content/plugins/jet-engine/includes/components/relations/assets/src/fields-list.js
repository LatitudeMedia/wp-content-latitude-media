import MediaControl from 'media-control';
import CheckboxGroupControl from 'checkbox-group-control';
import validators from './control-validators';

const {
	TextControl,
	TextareaControl,
	RadioControl,
	SelectControl
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

const {
	assign
} = window.lodash;

class FieldsList extends Component {

	constructor( props ) {

		super( props );

		if ( this.props.values ) {
			this.state = {
				...this.props.values,
				errors: {}
			};
		} else {
			this.state = {
				errors: {}
			};
		}

	}

	onChange( fieldName, value ) {

		this.setState( ( state ) => {
			const newState = assign( {}, state, { [ fieldName ]: value } );
			this.props.onChange( newState );
			return newState;
		} );

	}

	fieldTemplate( field ) {

		const commonProps = {
			key: 'field_' + field.type + field.name,
			label: field.title + ( field.required ? ' *' : '' ),
			help: field.description ? <span dangerouslySetInnerHTML={{ __html: field.description }}/> : '',
			value: this.state[ field.name ],
			onChange: ( newVal ) => {
				this.onChange( field.name, newVal );
			}
		}

		const unfilteredProps = assign( {}, field );

		if ( unfilteredProps.class ) {
			unfilteredProps.className = unfilteredProps.class;
			delete( unfilteredProps.class )
		}

		let type = field.type;

		if ( 'text' === type && field.input_type ) {
			type = field.input_type;
		}

		let groupLayout;

		if ( -1 !== ['checkbox', 'radio'].indexOf( field.type ) ) {
			groupLayout = field.layout || 'vertical';
		}

		switch ( type ) {

			case 'select':
				return <SelectControl
					{ ...commonProps }
					options={ field.options }
					multiple={ field.multiple }
				/>;

			case 'stepper':
				return <TextControl
					type="number"
					{ ...commonProps }
					min={ field.min_value ? field.min_value : '' }
					max={ field.max_value ? field.max_value : '' }
					step={ field.step_value ? field.step_value : 1 }
				/>;

			case 'radio':

				if ( parseInt( commonProps.value, 10 ) == commonProps.value ) {
					commonProps.selected = parseInt( commonProps.value, 10 );
				} else {
					commonProps.selected = commonProps.value;
				}

				delete( commonProps.value );

				return <RadioControl
					className={ 'je-radio-group-' + groupLayout }
					{ ...commonProps }
					options={ field.options }
				/>;

			case 'checkbox':
				return <CheckboxGroupControl
					{ ...commonProps }
					options={ field.options }
					layout={ groupLayout }
				/>;

			case 'media':

				if ( field.multi_upload ) {
					return <p><i>{ 'Gallery field type is not supported' }</i></p>;
				} else {
					return <MediaControl
						{ ...commonProps }
						{ ...field }
					/>;
				}

			case 'date':
				return <TextControl
					{ ...commonProps }
					type="date"
				/>;

			case 'time':
				return <TextControl
					{ ...commonProps }
					type="time"
				/>;

			case 'textarea':
			case 'wysiwyg':
				return <TextareaControl
					{ ...commonProps }
				/>;

			case 'datetime-local':
				return <TextControl
					{ ...commonProps }
					type="datetime-local"
				/>;

			case 'switcher':
			case 'iconpicker':
			case 'repeater':
			case 'colorpicker':
			case 'posts':
			case 'html':
				return <p><i>{ type + ' field type is not supported' }</i></p>;

			default:
				return <TextControl
					{ ...commonProps }
				/>;
		}

	}

	validateValue = ( field, value ) => {
		const validator = validators[ field.type ];
		if ( typeof validator !== 'function' ) return '';
		return validator( field, value );
	}

	validateFields = () => {
		const errors = {};

		this.props.fields.forEach( ( field ) => {
			const fieldError = this.validateValue( field, this.state[ field.name ] );
			if ( fieldError ) {
				errors[ field.name ] = fieldError;
			}
		} );

		this.setState( { errors } ); // store errors in state for displaying

		return Object.keys( errors ).length === 0;
	}

	render() {

		return (
			<Fragment>
				{ this.props.fields.map( ( field ) => (
					<Fragment key={ 'field_' + field.name }>
						{/*Render control*/}
						{ this.fieldTemplate( field ) }

						{/*Render error*/}
						{ this.state.errors && this.state.errors[ field.name ] && (
							<p className="error-message">
								{ this.state.errors[ field.name ] }
							</p>
						) }
					</Fragment>
				) ) }
			</Fragment>
		);
	}

}

export default FieldsList;
