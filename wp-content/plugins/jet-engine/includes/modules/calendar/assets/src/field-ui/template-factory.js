import JetEngineAdvancedDateFieldTemplate from './template';

const createTemplateEngine = function() {

	const templateEngine = new JetEngineAdvancedDateFieldTemplate();
	const templateEngineFunc = function() {}

	Object.setPrototypeOf( templateEngineFunc, templateEngine );

	return new Proxy( templateEngineFunc, {
		apply: function( target, thisArg, args ) {
			const [context] = args;

			switch ( context ) {
				case 'rrule':
					return templateEngine.getRecurringFieldTemplate.bind( templateEngine );
				case 'base-date':
					return templateEngine.getBaseDateFieldTemplate.bind( templateEngine );
				default:
					return templateEngine.render.bind( templateEngine );
			}
		}
	} );
};

export default createTemplateEngine;