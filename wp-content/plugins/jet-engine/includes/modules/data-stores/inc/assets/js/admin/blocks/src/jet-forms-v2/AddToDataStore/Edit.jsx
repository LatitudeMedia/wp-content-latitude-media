import { Flex } from '@wordpress/components';
import DataStoresRow from './DataStoresRow';
import FieldsMapRow from './FieldsMapRow';
import {
	WideLine,
} from 'jet-form-builder-components';

function EditAddToStore( props ) {
	return <Flex direction="column">
		<DataStoresRow { ...props }/>
		<WideLine/>
		<FieldsMapRow { ...props }/>
	</Flex>;
}

export default EditAddToStore;