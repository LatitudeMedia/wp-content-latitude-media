import * as advancedDateField from './block';

const {
	addFilter
} = wp.hooks;

addFilter( 'jet.fb.register.fields', 'jet-form-builder', blocks => {
	blocks.push( advancedDateField );
	return blocks;
} );