import { __ } from '@wordpress/i18n';
import {
	Label,
	RowControl,
	RowControlEnd,
} from 'jet-form-builder-components';
import { useFields } from 'jet-form-builder-blocks-to-actions';
import { SelectControl } from "@wordpress/components";
import { Fragment } from "@wordpress/element";

function FieldsMapRow( { settings, onChangeSettingObj } ) {

	const formFields = useFields( { withInner: false } );

	return <Fragment>
		<RowControl>
			<Label>{ __( 'Get Post ID from field', 'jet-engine' ) }</Label>
			<RowControlEnd>
				<SelectControl
					value={ settings.field }
					onChange={ field => onChangeSettingObj( { field } ) }
					options={ [
						{ value: '', label: '--' },
						...formFields,
					] }
				/>
			</RowControlEnd>
		</RowControl>
		<p style={ { fontSize: '12px', color: 'rgb(117, 117, 117)', margin: 0 } }><b>{ __( 'Note:', 'jet-engine' ) }</b> { __( 'The Insert/Update Post action must be placed before this action to use its Post ID here.', 'jet-engine' ) }</p>
	</Fragment>;
}
export default FieldsMapRow;
