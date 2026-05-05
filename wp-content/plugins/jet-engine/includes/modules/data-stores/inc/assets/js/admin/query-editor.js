(function( $ ) {

	'use strict';

	Vue.component( 'jet-data-stores-query', {
		template: '#jet-data-stores-query',
		mixins: [
			window.JetQueryWatcherMixin,
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				stores: window.jet_query_component_data_stores_query.stores,
				frontStores: window.jet_query_component_data_stores_query.front_stores,
				query: {},
				dynamicQuery: {}
			};
		},
		created: function() {
			this.query        = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };
		}
	} );

})( jQuery );
