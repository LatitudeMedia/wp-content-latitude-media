import metadata from '../../../advanced-date-field/form-builder/advanced-date-field/block.json';
import AdvancedDateFieldEdit from './edit';
const {
	__,
} = wp.i18n;

const { name, icon } = metadata;

metadata.attributes.isPreview = {
	type: 'boolean',
	default: false,
};

const settings = {
	title: __( 'Advanced Date Field', 'jet-form-builder' ),
	className: name.replace( '/', '-' ),
	icon: <span dangerouslySetInnerHTML={ { __html: icon } }></span>,
	edit: AdvancedDateFieldEdit,
	useEditProps: [ 'uniqKey', 'blockName', 'attrHelp' ],
	example: {
		attributes: {
			isPreview: true,
		},
	},
};

export {
	metadata,
	name,
	settings,
};
