
'use strict';

window.JetQueryTabInUseMixin = {
	methods: {
		isInUseMark: function( fieldSet ) {

			for ( const [ key, value ] of Object.entries( this.query ) ) {
				if ( fieldSet.includes( key ) && this.valueNotEmpty( value ) ) {
					return '• ';
				}
			}

			for ( const [ dynamicKey, dynamicValue ] of Object.entries( this.dynamicQuery ) ) {
				if ( fieldSet.includes( dynamicKey ) && this.valueNotEmpty( dynamicValue ) ) {
					return '• ';
				}
			}

			return '';
		},
		valueNotEmpty: function( value ) {
			if ( Array.isArray( value ) ) {
				return 0 < value.length;
			} else if ( typeof value === 'object' && value !== null ) {
				let keys = Object.keys( value );
				return 0 < keys.length;
			} else {
				return '' !== value && undefined !== value;
			}
		},
	}
}

window.JetQueryRepeaterMixin = {
	methods: {
		addNewField: function( event, props, parent, callback, defaultData ) {

			props = props || [];

			var field = {};

			for (var i = 0; i < props.length; i++) {
				if ( props[ i ] && 'object' === typeof props[ i ] ) {
					field[ props[ i ].prop ] = props[ i ].value || '';
				} else {
					field[ props[ i ] ] = '';
				}

			}

			field._id = Math.round( Math.random() * 1000000 );
			field.collapsed = false;

			if ( defaultData ) {
				field = { ...defaultData, ...field };
			}

			parent.push( field );

			if ( callback && 'function' === typeof callback ) {
				callback( field, parent );
			}
		},
		setFieldProp: function( id, key, value, parent ) {

			let index = this.searchByID( id, parent );

			if ( false === index ) {
				return;
			}

			let field = parent[ index ];

			field[ key ] = value;

			parent.splice( index, 1, field );

		},
		setFieldData: function( id, data, parent ) {

			let index = this.searchByID( id, parent );

			if ( false === index ) {
				return;
			}

			let field = parent[ index ];

			field = { ...field, ...data };

			parent.splice( index, 1, field );
		},
		cloneField: function( index, id, parent, callback ) {

			let field = JSON.parse( JSON.stringify( parent[ index ] ) );

			field.collapsed = false;
			field._id = Math.round( Math.random() * 1000000 );

			parent.splice( index + 1, 0, field );

			if ( callback && 'function' === typeof callback ) {
				callback( field, parent, id );
			}
		},
		deleteField: function( index, id, parent, callback ) {

			index = this.searchByID( id, parent );

			if ( false === index ) {
				return;
			}

			parent.splice( index, 1 );

			if ( callback && 'function' === typeof callback ) {
				callback( id, index, parent );
			}
		},
		isCollapsed: function( parent ) {
			if ( undefined === parent.collapsed || true === parent.collapsed ) {
				return true;
			} else {
				return false;
			}
		},
		searchByID: function( id, list ) {

			for ( var i = 0; i < list.length; i++ ) {
				if ( id == list[ i ]._id ) {
					return i;
				}
			}

			return false;

		}
	}
}

window.JetEngineQueryMetaField = {
	template: '#jet-meta-field',
	props: [ 'field', 'metaQuery', 'dynamicQuery' ],
	mixins: [ window.JetQueryRepeaterMixin ],
	data: function() {
		return {
			operators: window.JetEngineQueryConfig.operators_list,
			dataTypes: window.JetEngineQueryConfig.data_types,
			showPopup: false,
			currentField: { ...this.field },
		};
	},
	methods: {
		switchPopup: function( newVal ) {

			this.showPopup = newVal;

			if ( this.showPopup ) {
				document.body.style.overflow = 'hidden';
			} else {
				document.body.style.overflow = '';
			}
		},
		setDynamicMeta: function( id, data ) {
			this.dynamicQuery[ id ] = data;
			this.emitDynamicQuery( this.dynamicQuery );
		},
		setFieldProp: function( key, value ) {

			this.currentField = {
				...this.currentField,
				[ key ]: value,
			};

			this.$emit( 'input', this.currentField );
		},
		getDynamicValue: function() {
			return this.dynamicQuery.value;
		},
		setDynamicQueryProp: function( key, value ) {

			this.$set( this.dynamicQuery, key, value );

			this.emitDynamicQuery( {
				...this.dynamicQuery,
				[ key ]: value,
			} );

			this.$forceUpdate();
		},
		emitDynamicQuery: function( query ) {
			this.$emit( 'dynamic-input', query );
		},
		onAddNewField: function( newClause, metaQuery, prevID ) {

			this.$emit( 'input', this.currentField );

			let newItem = {};
			let args = this.dynamicQuery || { isDynamic: true };

			if ( prevID && args[ prevID ] ) {
				newItem = { ...args[ prevID ] };
			}

			args[ newClause._id ] = newItem;

			this.emitDynamicQuery( args );
		},
		onDeleteField: function( id ) {

			this.$emit( 'input', this.currentField );

			let args = {};

			if ( this.dynamicQuery[ id ] ) {
				args = this.dynamicQuery || {};
				this.$delete( this.dynamicQuery, id );
			} else {
				args = this.dynamicQuery.args || {};

				if (
					this.dynamicQuery
					&& this.dynamicQuery[ args ]
					&& this.dynamicQuery[ args ][ id ]
				) {
					this.$delete( this.dynamicQuery[ args ], id );
				}
			}

			delete args[ id ];

			this.emitDynamicQuery( args );
		}
	},
};

Vue.component( 'jet-engine-query-meta-field', window.JetEngineQueryMetaField );

window.JetQueryMetaParamsMixin = {
	methods: {
		presetMeta: function() {
			if ( ! this.query.meta_query ) {
				this.$set( this.query, 'meta_query', [] );
			}

			if ( ! this.dynamicQuery.meta_query ) {
				this.$set( this.dynamicQuery, 'meta_query', {} );
			} else if ( 'object' !== typeof this.dynamicQuery.meta_query || undefined !== this.dynamicQuery.meta_query.length ) {
				this.$set( this.dynamicQuery, 'meta_query', {} );
			}

			for ( var itemID in this.dynamicQuery.meta_query ) {
				if ( 'object' !== typeof this.dynamicQuery.meta_query[ itemID ] || undefined !== this.dynamicQuery.meta_query[ itemID ].length ) {
					this.$set( this.dynamicQuery.meta_query, itemID, {} );
				}
			}
		},
		newDynamicMeta: function( newClause, metaQuery, prevID ) {

			let newItem = {};

			if ( prevID && this.dynamicQuery.meta_query[ prevID ] ) {
				newItem = { ...this.dynamicQuery.meta_query[ prevID ] };
			}

			this.$set( this.dynamicQuery.meta_query, newClause._id, newItem );
		},
		setDynamicMeta: function( id, data ) {
			this.$set( this.dynamicQuery.meta_query, id, data );
		},
		deleteDynamicMeta: function( id ) {
			this.$delete( this.dynamicQuery.meta_query, id );
		},
		addNewMetaGroup( event ) {
			this.addNewField( event, [], this.query.meta_query, this.newDynamicMeta, {
				is_group: true,
				relation: 'and',
				args: [],
			} );
		}
	}
}

window.JetQueryWatcherMixin = {
	watch: {
		query: {
			handler: function ( newVal, oldVal ) {
				this.$emit( 'input', newVal );
			},
			deep: true,
		},
		dynamicQuery: {
			handler: function ( newVal, oldVal ) {
				this.$emit( 'dynamic-input', newVal );
			},
			deep: true,
		},
	},
}

window.JetQueryDateParamsMixin = {
	methods: {
		presetDate: function() {
			if ( ! this.query.date_query ) {
				this.$set( this.query, 'date_query', [] );
			}

			if ( ! this.dynamicQuery.date_query ) {
				this.$set( this.dynamicQuery, 'date_query', {} );
			} else if ( 'object' !== typeof this.dynamicQuery.date_query || undefined !== this.dynamicQuery.date_query.length ) {
				this.$set( this.dynamicQuery, 'date_query', {} );
			}

			for ( var itemID in this.dynamicQuery.date_query ) {
				if ( 'object' !== typeof this.dynamicQuery.date_query[ itemID ] || undefined !== this.dynamicQuery.date_query[ itemID ].length ) {
					this.$set( this.dynamicQuery.date_query, itemID, {} );
				}
			}
		},
		newDynamicDate: function( newClause, metaQuery, prevID ) {

			let newItem = {};

			if ( prevID && this.dynamicQuery.date_query[ prevID ] ) {
				newItem = { ...this.dynamicQuery.date_query[ prevID ] };
			}

			this.$set( this.dynamicQuery.date_query, newClause._id, newItem );
		},
		deleteDynamicDate: function( id ) {
			this.$delete( this.dynamicQuery.date_query, id );
		},
	}
}

window.JetQueryTaxParamsMixin = {
	methods: {
		presetTax: function() {
			if ( ! this.query.tax_query ) {
				this.$set( this.query, 'tax_query', [] );
			}

			if ( ! this.dynamicQuery.tax_query ) {
				this.$set( this.dynamicQuery, 'tax_query', {} );
			} else if ( 'object' !== typeof this.dynamicQuery.tax_query || undefined !== this.dynamicQuery.tax_query.length ) {
				this.$set( this.dynamicQuery, 'tax_query', {} );
			}

			for ( var itemID in this.dynamicQuery.tax_query ) {
				if ( 'object' !== typeof this.dynamicQuery.tax_query[ itemID ] || undefined !== this.dynamicQuery.tax_query[ itemID ].length ) {
					this.$set( this.dynamicQuery.tax_query, itemID, {} );
				}
			}
		},
		newDynamicTax: function( newClause, metaQuery, prevID ) {

			let newItem = {};

			if ( prevID && this.dynamicQuery.tax_query[ prevID ] ) {
				newItem = { ...this.dynamicQuery.tax_query[ prevID ] };
			}

			this.$set( this.dynamicQuery.tax_query, newClause._id, newItem );

		},
		deleteDynamicTax: function( id ) {
			this.$delete( this.dynamicQuery.tax_query, id );
		},
	}
}
