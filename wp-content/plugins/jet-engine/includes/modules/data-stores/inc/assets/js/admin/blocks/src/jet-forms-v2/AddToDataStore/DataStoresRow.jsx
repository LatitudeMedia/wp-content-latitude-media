/* eslint-disable import/no-extraneous-dependencies */
import {
	Label,
	RowControl,
	RowControlEnd,
} from 'jet-form-builder-components';
import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';
import { Fragment } from "@wordpress/element";

// eslint-disable-next-line max-lines-per-function
function DataStoresRow( { settings, onChangeSettingObj, source } ) {
	return <Fragment>
		<RowControl>
			<Label>{ __( 'Data Stores', 'jet-engine' ) }</Label>
			<RowControlEnd>
				<SelectControl
					value={ settings.slug }
					onChange={ slug => onChangeSettingObj( { slug } ) }
					options={ [
						{ value: '', label: '--' },
						...source.data_stores,
					] }
				/>
			</RowControlEnd>
		</RowControl>
		<p style={ { fontSize: '12px', color: 'rgb(117, 117, 117)', margin: 0 } }><b>{ __( 'Note:', 'jet-engine' ) }</b> { __( 'Data Stores with the Local Storage type are not supported in this action.', 'jet-engine' ) }</p>
	</Fragment>;
}

export default DataStoresRow;
