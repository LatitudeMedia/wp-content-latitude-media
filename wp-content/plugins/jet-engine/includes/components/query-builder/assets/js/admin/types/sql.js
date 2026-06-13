(function( $ ) {

	'use strict';

	Vue.component( 'jet-engine-sql-query-field', {
		extends: window.JetEngineQueryMetaField,
		template: '#jet-engine-sql-query-field',
		props: [ 'field', 'metaQuery', 'dynamicQuery', 'availableColumns' ],
		methods: {
			setDynamicWhere: function( id, data ) {
				this.setDynamicMeta( id, data );
			}
		}
	} );

	Vue.component( 'jet-sql-query', {
		template: '#jet-sql-query',
		mixins: [
			window.JetQueryWatcherMixin,
			window.JetQueryRepeaterMixin,
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				tablesList: window.jet_query_component_sql.tables,
				castObjectsList: window.jet_query_component_sql.cast_objects,
				operators: window.JetEngineQueryConfig.operators_list,
				dataTypes: window.JetEngineQueryConfig.data_types,
				query: {},
				dynamicQuery: {},
				codeEditors: {},
				fetchedControls: {},
			};
		},
		created: function() {

			this.query = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			if ( ! this.query.custom_aliases ) {
				this.query.custom_aliases = {};
			}

			this.presetJoin();
			this.presetWhere();
			this.presetOrder();
			this.presetCols();
			this.presetColAliases();
		},
		computed: {
			fromTableValue: {
				get() {
					return this.normalizeSelectValue(this.query.table);
				},
				set(value) {
					const normalized = this.denormalizeSelectValue(value);

					this.$set(this.query, 'table', normalized);
				}
			},

			columnSchema: function() {

				var result = [];

				if ( this.query.table ) {

					let columns = this.getColumns( this.query.table );

					result.push( {
						table: this.query.table,
						columns: [ ...columns ],
					} );

				}

				if ( this.query.use_join && this.query.join_tables.length ) {

					let processedTables = { [ this.query.table ]: 1 };

					for ( var i = 0; i < this.query.join_tables.length; i++ ) {

						let joinTable = this.query.join_tables[ i ].table;
						let preparedJoinTable = joinTable;

						if ( joinTable && processedTables[ joinTable ] ) {
							processedTables[ joinTable ]++;
							preparedJoinTable = joinTable + processedTables[ joinTable ];
						} else if ( joinTable ) {
							processedTables[ joinTable ] = 1;
						}

						if ( preparedJoinTable ) {

							let joinColumns = this.getColumns( joinTable );
							let preparedColumns = [];

							joinColumns = [ ...joinColumns ];

							for ( var j = 0; j < joinColumns.length; j++ ) {
								preparedColumns.push( {
									value: preparedJoinTable + '.' + joinColumns[ j ].value,
									label: preparedJoinTable + '.' + joinColumns[ j ].label,
								} )
							}

							result.push( {
								table: preparedJoinTable,
								columns: preparedColumns,
							} );
						}

					}

				}

				return result;

			},
			availableColumns: function() {

				var result = [];
				var schema = JSON.parse( JSON.stringify( this.columnSchema ) );

				for ( var i = 0; i < schema.length; i++ ) {

					let addPrefix = false;

					if ( 0 === i && 1 < schema.length ) {
						addPrefix = true;
					}

					for ( var j = 0; j < schema[ i ].columns.length; j++ ) {
						if ( addPrefix ) {
							schema[ i ].columns[ j ].value = schema[ i ].table + '.' + schema[ i ].columns[ j ].value;
							schema[ i ].columns[ j ].label = schema[ i ].table + '.' + schema[ i ].columns[ j ].label;
						}

						result.push( schema[ i ].columns[ j ] );
					}

				}

				this.$set( this.query, 'columns_for_alias', result.map( i => i.value ) );

				if ( this?.query?.include_columns?.length ) {
					const currentValue = this.query.include_columns.values();
					const valuesList   = result.map( op => op.value );
					
					for ( const val of currentValue ) {
						if ( ! valuesList.includes( val ) ) {
							result.push( {
								'value': val,
								'label': val + ' (missing column)',
							} );
						}
					}
				}

				return result;

			},
			availableColumnsForAlias: function() {
				let result = [];

				if ( this?.query?.include_columns?.length ) {
					const calc = this.query.include_columns;

					result = this.availableColumns.map( ( item ) => {
						return {
							value: item.value,
							label: calc.includes( item.value ) ? item.label : item.label + ' (not included)',
						};
					} );
				} else {
					result = this.availableColumns;
				}

				return result;
			},
			availableOrderByColumns: function() {
				var columns = JSON.parse( JSON.stringify( this.availableColumns ) );
				
				if ( this.query?.include_calc && this.query?.calc_cols?.length ) {

					for ( var i = 0; i < this.query.calc_cols.length; i++ ) {
						if ( this.query.calc_cols[ i ]?.column && this.query.calc_cols[ i ]?.function ) {
							let col = this.query.calc_cols[ i ];
							let colName = col.function + '(' + col.column + ')';
							let colLabel = col?.column_alias ? col.column_alias + ' (calculated)' : colName;
							columns.push( {
								label: colLabel,
								value: colName,
							} );
						}
					}

				}

				return columns;

			},
		},
		methods: {
			initEditor: function( controlName, prop ) {
				if ( ! wp.codeEditor || ! JetEngineQueryConfig.use_CodeMirror ) {
					return;
				}

				let codeEditorArea = this.$refs?.[ controlName ]?.$el?.querySelector( 'textarea' );

				if ( ! codeEditorArea ) {
					return;
				}

				const codeMirrorConfig = JetEngineQueryConfig.CodeMirror_config;

				if ( codeMirrorConfig.jeReplaceTabs ) {
					codeMirrorConfig.extraKeys ??= {};
					codeMirrorConfig.extraKeys.Tab = function( cm ) {
						let spaces = Array( cm.getOption( "tabSize" ) + 1 ).join( " " );
						cm.replaceSelection( spaces, "end", "+input" );
					};
				}

				const wpCodeMirror = wp.codeEditor.initialize(
					codeEditorArea,
					{
						codemirror: codeMirrorConfig,
					}
				);

				const codeMirror = wpCodeMirror.codemirror;

				if ( codeMirrorConfig.jeCustomHeight ) {
					let size = codeMirrorConfig.jeCustomHeight.toString().match( /(^\d+)(px)?/ );

					if ( size ) {
						let lineHeight = codeMirror.defaultTextHeight();
						let height = size[ 2 ] ? size[ 1 ] : size[ 1 ] * lineHeight;
						codeMirror.setSize( null, height );
					}
				}

				this.codeEditors[ controlName ] = codeMirror;

				codeMirror.on( 'change', ( event ) => {
					this.$set( this.query, prop, event.getValue() );
				} )
			},
			setEditorValue: function( completion, controlName ) {
				if ( this?.codeEditors?.[ controlName ] ) {
					this.codeEditors[ controlName ].setValue( completion );
				} else {
					this.$set( this.query, controlName.replace( /^query_/, '' ), completion );
				}
			},
			isFetchingControl: function( controlName ) {
				return this.fetchedControls[ controlName ] ?? false;
			},
			setControlFetch: function( controlName, isFetching = true ) {
				this.$set( this.fetchedControls, controlName, isFetching );
			},
			prettifySQL( sql = '' ) {
				if ( ! window.sqlFormatter || ! sql.length ) {
					return sql;
				}
				
				let result;
				
				result = sql.replaceAll( /%[a-z-_]+?%({.+?})?/g, '"$&"' );
				result = window.sqlFormatter.format( result, { language: 'mysql' } );
				
				result = result.replaceAll(
					/"(%[a-z-_]+?%)({[^{}]+?})?"/g,
					function( match, macroBody, macroArgs ) {
						if ( ! macroArgs ) {
							return match;
						}

						return macroBody + macroArgs.replaceAll( /"\s+(.+?)\s+":"\s+(.+?)\s+"/g, '"$1":"$2"' );
					}
				);

				return result;
			},
			maybeConvertToAdvanced: function( controlName ) {
				const query = this.query;
				
				if ( query?.manual_query?.length || ! query.advanced_mode || ! query.table ) {
					return;
				}

				const dynamicQuery = this.dynamicQuery;
				const self = this;

				self.setControlFetch( controlName );

				self.$CXNotice.add( {
					message: 'Converting simple query to advanced...',
					type: 'info',
					duration: 70000,
				}, 'sqlConvert' );

				wp.apiFetch( {
					method: 'post',
					path: JetEngineQueryConfig.api_path_convert_sql,
					data: {
						query_type: 'sql',
						query: query,
						dynamic_query: dynamicQuery,
					}
				} ).then( function( response ) {
					self.$CXNotice.close( 'sqlConvert' );

					if ( response.success ) {
						let result = self.prettifySQL( response.data );

						if ( result ) {
							self.setEditorValue( result, 'query_manual_query' );

							self.$CXNotice.add( {
								message: 'Simple query converted to advanced',
								type: 'success',
								duration: 7000,
							} );
						}
					} else {
						self.$CXNotice.add( {
							message: 'Conversion failed',
							type: 'error',
							duration: 7000,
						} );
					}

					self.setControlFetch( controlName, false );
				} ).catch( function( response ) {
					self.$CXNotice.close( 'sqlConvert' );

					self.$CXNotice.add( {
						message: 'Conversion failed, check if Simple Mode forms a valid SQL query',
						type: 'error',
						duration: 7000,
					} );

					window.JetEngineSQLConverterError = response;

					self.setControlFetch( controlName, false );
				} );
			},
			presetJoin: function() {
				if ( ! this.query.join_tables ) {
					this.$set( this.query, 'join_tables', [] );
				}

				if ( ! this.dynamicQuery.join_tables ) {
					this.$set( this.dynamicQuery, 'join_tables', {} );
				} else if ( 'object' !== typeof this.dynamicQuery.join_tables || undefined !== this.dynamicQuery.join_tables.length ) {
					this.$set( this.dynamicQuery, 'join_tables', {} );
				}
			},
			randID: function() {
				return Math.round( Math.random() * 1000000 )
			},
			newDynamicJoin: function( newClause, metaQuery, prevID ) {

				let newItem = {};

				if ( prevID && this.dynamicQuery.join_tables[ prevID ] ) {
					newItem = { ...this.dynamicQuery.join_tables[ prevID ] };
				}

				this.$set( this.dynamicQuery.join_tables, newClause._id, newItem );

			},
			deleteDynamicJoin: function( id ) {
				this.$delete( this.dynamicQuery.join_tables, id );
			},
			presetWhere: function() {
				if ( ! this.query.where ) {
					this.$set( this.query, 'where', [] );
				}

				if ( ! this.dynamicQuery.where ) {
					this.$set( this.dynamicQuery, 'where', {} );
				} else if ( 'object' !== typeof this.dynamicQuery.where || undefined !== this.dynamicQuery.where.length ) {
					this.$set( this.dynamicQuery, 'where', {} );
				}

				for ( var itemID in this.dynamicQuery.where ) {
					if ( 'object' !== typeof this.dynamicQuery.where[ itemID ] || undefined !== this.dynamicQuery.where[ itemID ].length ) {
						this.$set( this.dynamicQuery.where, itemID, {} );
					}
				}
			},
			presetCols: function() {
				if ( ! this.query.calc_cols ) {
					this.$set( this.query, 'calc_cols', [] );
				}
			},
			presetColAliases: function() {
				if ( ! this.query.column_aliases ) {
					this.$set( this.query, 'column_aliases', [] );
				}
			},
			newDynamicWhere: function( newClause, metaQuery, prevID ) {

				let newItem = {};

				if ( prevID && this.dynamicQuery.where[ prevID ] ) {
					newItem = { ...this.dynamicQuery.where[ prevID ] };
				}

				this.$set( this.dynamicQuery.where, newClause._id, newItem );
			},
			deleteDynamicWhere: function( id ) {
				this.$delete( this.dynamicQuery.where, id );
			},
			setDynamicWhere: function( id, data ) {
				this.$set( this.dynamicQuery.where, id, data );
			},
			addNewWhereGroup( event ) {
				this.addNewField( event, [], this.query.where, this.newDynamicWhere, {
					is_group: true,
					relation: 'or',
					args: [],
				} );
			},
			getColumns: function( table ) {
				return window.jet_query_component_sql.columns[ table ] || [];
			},
			getJoinTitle( item, currentIndex ) {

				const allColumns = [ ...this.columnSchema ];

				currentIndex++;

				for ( var i = 0; i < allColumns.length; i++ ) {

					if ( i === currentIndex ) {
						return allColumns[ i ].table;
					}

				}

			},
			getJoinColumns( currentIndex ) {

				const allColumns = [ ...this.columnSchema ];
				const result = [];

				currentIndex++;

				for ( var i = 0; i < allColumns.length; i++ ) {

					if ( i !== currentIndex ) {
						result.push( {
							label: allColumns[ i ].table,
							options: allColumns[ i ].columns,
						} );
					}

				}

				return result;

			},
			presetOrder: function() {
				if ( ! this.query.orderby ) {
					this.$set( this.query, 'orderby', [] );
				}
			},
			normalizeSelectValue(value) {
				if (!value) return [];

				return Array.isArray(value) ? value : [value];
			},
			denormalizeSelectValue(value) {
				return Array.isArray(value) ? value[0] || '' : value;
			},
			getJoinTableValue( index ) {
				return this.normalizeSelectValue(
					this.query.join_tables[ index ].table
				);
			},
			setJoinTableValue( index, value ) {
				const normalized = this.denormalizeSelectValue( value );

				this.setFieldProp(
					this.query.join_tables[ index ]._id,
					'table',
					normalized,
					this.query.join_tables
				);
			}
		}
	} );

})( jQuery );
