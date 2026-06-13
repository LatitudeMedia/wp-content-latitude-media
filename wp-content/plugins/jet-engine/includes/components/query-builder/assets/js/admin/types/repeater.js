(function( $ ) {

	'use strict';

	Vue.component( 'jet-repeater-query', {
		template: '#jet-repeater-query',
		mixins: [
			window.JetQueryWatcherMixin,
			window.JetQueryRepeaterMixin,
			window.JetQueryMetaParamsMixin,
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				sourcesList: window.jet_query_component_repeater.sources,
				metaFields: window.jet_query_component_repeater.meta_fields,
				optionsFields: window.jet_query_component_repeater.options_fields,
				taxonomies: window.JetEngineQueryConfig.taxonomies,
				operators: window.JetEngineQueryConfig.operators_list,
				dataTypes: window.JetEngineQueryConfig.data_types,
				query: {},
				dynamicQuery: {},
			};
		},
		computed: {
			metaClauses: function() {

				let result = [];

				for ( var i = 0; i < this.query.meta_query.length; i++ ) {
					if ( this.query.meta_query[ i ].clause_name ) {
						result.push( {
							value: this.query.meta_query[ i ].clause_name,
							label: this.query.meta_query[ i ].clause_name,
						} )
					}
				}

				return result;
			},
		},
		created: function() {

			this.query = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			if ( ! this.query.orderby ) {
				this.$set( this.query, 'orderby', [] );
			}

			this.presetMeta();

			let allFields = [];
			
			allFields = window.jet_query_component_repeater.meta_fields
				.reduce( ( acc, cur ) => acc.concat( cur.options.map( op => op.value ) ), allFields );

			let currentEngineField = this?.query?.jet_engine_field;

			if ( currentEngineField?.length && ! allFields.includes( currentEngineField ) ) {
				let userFields = [];
			
				userFields = window.jet_query_component_repeater.meta_fields
					.filter( o => o?.for === 'user' )
					.reduce( ( acc, cur ) => acc.concat( cur.options.map( op => op.value ) ), userFields );

				for ( const name of userFields ) {
					if ( name.includes( currentEngineField ) ) {
						this.$set( this.query, 'jet_engine_field', name );
						break;
					}
				}
			}

			// if ( undefined === this.query.hide_empty ) {
			// 	this.$set( this.query, 'hide_empty', true );
			// }

		},
	} );

})( jQuery );
