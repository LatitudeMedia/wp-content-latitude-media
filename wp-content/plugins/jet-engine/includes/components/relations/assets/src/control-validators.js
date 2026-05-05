const validators = {
	stepper: ( field, value ) => {
		let error = '';

		if ( value === '' ) {
			return '';
		}

		const numValue = parseFloat( value );

		if ( field.min_value !== undefined && field.min_value !== '' ) {
			const min = parseFloat( field.min_value );
			if ( numValue < min ) {
				error = `Please enter a value greater than or equal to ${ min }.`;
				return error;
			}
		}

		if ( field.max_value !== undefined && field.max_value !== '' ) {
			const max = parseFloat( field.max_value );
			if ( numValue > max ) {
				error = `Please enter a value less than or equal to ${ max }.`;
				return error;
			}
		}

		return error;
	},
	// other validators...
};

export default validators;