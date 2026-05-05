(function( $ ) {

	'use strict';

	Vue.component( 'jet-relations-query', {
		template: '#jet-relations-query',
		mixins: [
			window.JetQueryWatcherMixin,
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				relations: window.jet_query_component_relations_query.relations,
				sources: window.jet_query_component_relations_query.sources,
				query: {},
				dynamicQuery: {}
			};
		},
		created: function() {

			this.query        = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			if ( ! this.query.rel_object ) {
				this.$set( this.query, 'rel_object', 'child_object' );
			}

			if ( ! this.query.rel_object_from ) {
				this.$set( this.query, 'rel_object_from', 'current_object' );
			}
		}
	} );

})( jQuery );
