(function( $ ) {

	'use strict';

	Vue.component( 'jet-posts-query', {
		template: '#jet-posts-query',
		mixins: [
			window.JetQueryWatcherMixin,
			window.JetQueryRepeaterMixin,
			window.JetQueryMetaParamsMixin,
			window.JetQueryTaxParamsMixin,
			window.JetQueryDateParamsMixin,
			window.JetQueryDateParamsMixin,
			window.JetQueryTabInUseMixin,
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				postTypes: window.JetEngineQueryConfig.post_types,
				taxonomies: window.JetEngineQueryConfig.taxonomies,
				postStatuses: window.jet_query_component_posts.posts_statuses,
				operators: window.JetEngineQueryConfig.operators_list,
				dataTypes: window.JetEngineQueryConfig.data_types,
				orderbyOptions: window.JetEngineQueryConfig.orderby_options.posts,
				query: {},
				dynamicQuery: {},
			};
		},
		computed: {
			commentOperators: function() {
				return this.operators.filter( function( item ) {
					const allowed = [ '=', '!=', '>', '>=', '<', '<=' ];
					return allowed.includes( item.value );
				} );
			},
			dateOperators: function() {
				return this.operators.filter( function( item ) {
					const disallowed = [ 'EXISTS', 'NOT EXISTS', 'LIKE', 'NOT LIKE' ];
					return ! disallowed.includes( item.value );
				} );
			},
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
		methods: {
			isShowMultipleMetaOrderNotice: function( clauseId ) {
				let metaOrderCount = 0;
				let foundClause = false;
				let isMultipleMetaOrder = false;

				for ( const clause of this.query.orderby ) {
					if ( clause?.orderby?.includes( 'meta_value' ) ) {
						metaOrderCount++;
					}

					if ( ! foundClause && clauseId === clause._id ) {
						foundClause = clause;
					}

					if ( metaOrderCount > 1 ) {
						isMultipleMetaOrder = true;

						if ( foundClause ) {
							break;
						}
					}
				}

				if ( ! foundClause || ! isMultipleMetaOrder ) {
					return false;
				}

				return foundClause?.orderby?.includes?.( 'meta_value' );
			}
		},
		created: function() {

			this.query = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			if ( ! this.query.orderby ) {
				this.$set( this.query, 'orderby', [] );
			}

			this.presetMeta();
			this.presetTax();
			this.presetDate();
		},
	} );
})( jQuery );
