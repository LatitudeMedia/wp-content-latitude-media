import 'components/slider.js';

( function( $ ) {

	"use strict";

	var JetEngine = {

		lazyLoading: false,
		addedScripts: [],
		addedStyles: [],
		addedPostCSS: [],
		assetsPromises: [],

		initDone: false,

		commonInit: function() {
			JetEngine.commonEvents();
			JetEngine.customUrlActions.init();
		},

		commonEvents: function( $scope ) {
			$scope = $scope || $( document );

			$scope
				.on( 'click.JetEngine', '.jet-listing-dynamic-link__link[data-delete-link="1"]', JetEngine.showConfirmDeleteDialog )
				.on( 'click.JetEngine', '.jet-engine-listing-overlay-wrap:not([data-url*="event=hover"])', JetEngine.handleListingItemClick )
				.on( 'click.JetEngine', '.jet-container[data-url]', JetEngine.handleContainerURL )
				.on( 'change.JetEngine', '.jet-listing-dynamic-link .qty', JetEngine.handleProductQuantityChange );

				window.JetPlugins.hooks.doAction(
					'jet-engine.common-events',
					$scope,
					this,
					$
				);
		},

		handleProductQuantityChange: function ( event ) {
			event.preventDefault();
			event.stopPropagation();

			const $this = $( this );

			$this.closest( ".jet-listing-dynamic-link" ).find( ".jet-woo-add-to-cart" ).data( "quantity", $this.val() ).attr( "data-quantity", $this.val() );
		},

		handleContainerURL: function() {
			var $this  = $( this ),
				url    = $this.data( 'url' ),
				target = $this.data( 'target' );

			if ( ! target ) {
				window.location = url;
			} else {
				window.open( url, '_blank' ).focus();
			}

		},

		init: function() {
			var widgets = {
				'jet-listing-dynamic-field.default' : JetEngine.widgetDynamicField,
				'jet-listing-grid.default': JetEngine.widgetListingGrid,
			};

			$.each( widgets, function( widget, callback ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			});

			// Re-init sliders in nested tabs
			window.elementorFrontend.elements.$window.on(
				'elementor/nested-tabs/activate',
				( event, content ) => {
					const $content = $( content );

					setTimeout( () => {
						JetEngine.maybeReinitSlider( event, $content );
						JetEngine.widgetDynamicField( $content );
					} );
				}
			);

			JetEngine.updateAddedStyles();
		},

		initBricks: function( $scope ) {

			if ( window.bricksIsFrontend ) {
				return;
			}

			$scope = $scope || $( 'body' );
			JetEngine.initBlocks( $scope );

		},

		initBlocks: function( $scope ) {

			$scope = $scope || $( 'body' );

			window.JetPlugins.init( $scope, [
				{
					block: 'jet-engine/listing-grid',
					callback: JetEngine.widgetListingGrid
				},
				{
					block: 'jet-engine/dynamic-field',
					callback: JetEngine.widgetDynamicField
				}
			] );

			document.addEventListener('bricks/tabs/changed', (event) => {
				const tabActivePane = event.detail?.activePane;

				if (tabActivePane) {
					const $content = jQuery(tabActivePane);

					setTimeout(() => {
						JetEngine.maybeReinitSlider(event, $content);
						JetEngine.widgetDynamicField($content);
					}, 50);
				}
			});
		},

		showConfirmDeleteDialog: function( event ) {
			event.preventDefault();
			event.stopPropagation();

			var $this = $( this );

			if ( window.confirm( $this.data( 'delete-message' ) ) ) {
				JetEngine.handleDeleteRedirect( $this.attr( 'href' ), this );
			}

		},

		handleDeleteRedirect: function( url, baseElement ) {
			if ( ! window.JetSmartFilters ) {
				window.location.assign( url );
				return;
			}

			const filterGroups = window.JetSmartFilters.filterGroups;

			for ( const groupName in filterGroups ) {
				const filterGroup = filterGroups[ groupName ];

				if ( filterGroup?.$provider?.length && filterGroup.$provider.find( $( baseElement ) ).length ) {
					filterGroup.startAjaxLoading();

					$.ajax({
						url: url,
						type: 'GET',
					}).done(
						function( response ) {
							const redirectURL = new URLSearchParams( url ).get( 'redirect' ).replace(/\/+$/, '');
							const currentURL  = ( window.location.origin + window.location.pathname ).replace(/\/+$/, '');

							let stayAfterFiltering = window.JetPlugins.hooks.applyFilters(
								'jet-engine.dynamic-link.delete-link.stay-after-filtering',
								true,
								baseElement,
								url
							);

							if ( stayAfterFiltering && redirectURL === currentURL ) {
								filterGroup.currentQuery[ '_refresh_listing_' + Date.now() ] = Date.now();
								filterGroup.apply();
							} else {
								window.location.assign( redirectURL.replace( '&#038;', '&' ) );
							}
						}
					)
					
					return;
				}
			}

			window.location.assign( url );
		},

		handleListingItemClick: function( event ) {

			var url    = $( this ).data( 'url' ),
				target = $( this ).data( 'target' ) || false;

			if ( url ) {

				event.preventDefault();

				if ( window.elementorFrontend && window.elementorFrontend.isEditMode() ) {
					return;
				}

				if ( -1 !== url.indexOf( '#jet-engine-action' ) ) {

					JetEngine.customUrlActions.runAction( url );

				} else {

					if ( '_blank' === target ) {
						window.open( url );
						return;
					}

					window.location = url;
				}
			}

		},

		customUrlActions: {
			selectorOnClick: 'a[href^="#jet-engine-action"][href*="event=click"]',
			selectorOnHover: 'a[href^="#jet-engine-action"][href*="event=hover"], [data-url^="#jet-engine-action"][data-url*="event=hover"]',

			init: function() {
				var timeout = null;

				$( document ).on( 'click.JetEngine', this.selectorOnClick, function( event ) {
					event.preventDefault();
					JetEngine.customUrlActions.actionHandler( event )
				} );

				$( document ).on( 'click.JetEngine', this.selectorOnHover, function( event ) {
					if ( 'A' === event.currentTarget.nodeName ) {
						event.preventDefault();
					}
				} );

				$( document ).on( {
					'mouseenter.JetEngine': function( event ) {

						if ( timeout ) {
							clearTimeout( timeout );
						}

						timeout = setTimeout( function() {
							JetEngine.customUrlActions.actionHandler( event )
						}, window.JetEngineSettings.hoverActionTimeout );
					},
					'mouseleave.JetEngine': function() {
						if ( timeout ) {
							clearTimeout( timeout );
							timeout = null;
						}
					},
				}, this.selectorOnHover );
			},

			actions: {},

			addAction: function( name, callback ) {
				this.actions[ name ] = callback;
			},

			actionHandler: function( event ) {
				var url = $( event.currentTarget ).attr( 'href' ) || $( event.currentTarget ).attr( 'data-url' );

				this.runAction( url );
			},

			runAction: function( url ) {
				var queryParts = url.split( '&' ),
					settings = {};

				queryParts.forEach( function( item ) {
					if ( -1 !== item.indexOf( '=' ) ) {
						var pair = item.split( '=' );

						settings[ pair[0] ] = decodeURIComponent( pair[1] );
					}
				} );

				if ( ! settings.action ) {
					return;
				}

				var actionCb = this.actions[ settings.action ];

				if ( ! actionCb ) {
					return;
				}

				actionCb( settings );
			}
		},

		widgetListingGrid: function( $scope ) {

			var widgetID    = $scope.closest( '.elementor-widget' ).data( 'id' ),
				$wrapper    = $scope.find( '.jet-listing-grid' ).first(),
				hasLazyLoad = $wrapper.hasClass( 'jet-listing-grid--lazy-load' ),
				$listing    = $scope.find( '.jet-listing-grid__items' ).first(),
				$slider     = $listing.parent( '.jet-listing-grid__slider' ),
				$masonry    = $listing.hasClass( 'jet-listing-grid__masonry' ) ? $listing : false,
				navSettings = $listing.data( 'nav' ),
				masonryGrid = false,
				listingType = 'elementor';

			if ( ! widgetID ) {
				widgetID    = $scope.data( 'element-id' );
				listingType = $scope.data( 'listing-type' );
			}

			navSettings = JetEngine.ensureJSON( navSettings );

			if ( hasLazyLoad ) {

				var lazyLoadOptions = $wrapper.data( 'lazy-load' ),
					$container = $scope.find( '.elementor-widget-container' ),
					widgetSettings = false;

				if ( ! $container.length ) {
					$container = $scope;
				}

				// Get widget settings from `elementorFrontend` in Editor.
				if ( window.elementorFrontend && window.elementorFrontend.isEditMode()
					&& $wrapper.closest( '.elementor[data-elementor-type]' ).hasClass( 'elementor-edit-mode' )
				) {
					widgetSettings = JetEngine.getEditorElementSettings( $scope.closest( '.elementor-widget' ) );
					widgetID       = false; // for avoid get widget settings from document in editor
				}

				if ( ! widgetSettings ) {
					widgetSettings = $scope.data( 'widget-settings' );
				}

				JetEngine.lazyLoadListing( {
					container:      $container,
					elementID:      widgetID,
					postID:         lazyLoadOptions.post_id,
					queriedID:      lazyLoadOptions.queried_id || false,
					offset:         lazyLoadOptions.offset || '0px',
					query:          lazyLoadOptions.query || {},
					listingType:    listingType,
					widgetSettings: widgetSettings,
					extraProps:     lazyLoadOptions.extra_props || false,
				} );

				return;
			}

			if ( $slider.length ) {
				JetEngine.initSlider( $slider );
			}

			if ( $masonry && $masonry.length ) {

				JetEngine.initMasonry( $masonry );

				/* Keep masonry re-init for Bricks */
				if ( $scope.hasClass( 'brxe-jet-engine-listing-grid' ) ) {
					$( window ).on( 'load', function() {
						JetEngine.runMasonry( $masonry );
					} );
				}

			}

			if ( navSettings && navSettings.enabled ) {

				JetEngine.loadMoreListing( {
					container: $listing,
					settings: navSettings,
					masonry: $masonry,
					slider: $slider,
					elementID: widgetID,
				} );

			}

			// Init elements handlers in editor.
			if ( window.elementorFrontend && window.elementorFrontend.isEditMode()
				&& $wrapper.closest( '.elementor-element-edit-mode' ).length
			) {
				JetEngine.initElementsHandlers( $wrapper );
			}

		},

		initMasonry: function( $masonry, masonrySettings ) {
			imagesLoaded( $masonry, function() {
				JetEngine.runMasonry( $masonry, masonrySettings );
			} );
		},

		runMasonry: function( $masonry, masonrySettings ) {
			var defaultSettings = {
				itemSelector: '> .jet-listing-grid__item',
				columnsKey:   'columns',
			};

			masonrySettings = masonrySettings || {};
			masonrySettings = $.extend( {}, defaultSettings, masonrySettings );

			var $eWidget     = $masonry.closest( '.elementor-widget' ),
				$items       = $( masonrySettings.itemSelector, $masonry ),
				options      = $masonry.data( 'masonry-grid-options' ) || {};

			options = JetEngine.ensureJSON( options );

			// Reset masonry
			$items.css( {
				marginTop: ''
			} );

			// Bricks margin
			const { gap } = options;
			let margin = null;

			if ( gap ) {
				margin = {
					x: +gap.horizontal,
					y: +gap.vertical,
				};
			}

			var args = {
				container: $masonry[0],
				margin: margin ? margin : 0,
			};

			if ( $eWidget.length ) {
				var settings     = JetEngine.getElementorElementSettings( $eWidget ),
					breakpoints  = {},
					eBreakpoints = window.elementorFrontend.config.responsive.activeBreakpoints,
					columnsKey   = masonrySettings.columnsKey;

				args.columns = settings[columnsKey + '_widescreen'] ? +settings[columnsKey + '_widescreen'] : +settings[columnsKey];

				Object.keys( eBreakpoints ).reverse().forEach( function( breakpointName ) {

					if ( settings[columnsKey + '_' + breakpointName] ) {
						if ( 'widescreen' === breakpointName ) {
							breakpoints[eBreakpoints[breakpointName].value - 1] = +settings[columnsKey];
						} else {
							breakpoints[eBreakpoints[breakpointName].value] = +settings[columnsKey + '_' + breakpointName];
						}
					}

				} );

				args.breakAt = breakpoints;

			} else {
				args.columns = options.columns.desktop;
				args.breakAt = {
					1025: options.columns.tablet,
					768:  options.columns.mobile,
				};
			}

			var masonryInstance = Macy( args );

			masonryInstance.runOnImageLoad( function () {
				masonryInstance.recalculate( true );
			}, true );

			// Event to recalculate current masonry listings.
			$masonry.on( 'jet-engine/listing/recalculate-masonry-listing', function() {
				masonryInstance.runOnImageLoad( function () {
					masonryInstance.recalculate( true );
				}, true );
			} );

			// Event to recalculate all masonry listings.
			$( document ).on( 'jet-engine/listing/recalculate-masonry', function() {
				masonryInstance.recalculate( true );
			} );

		},

		ajaxGetListing: function( options, doneCallback, failCallback ) {

			var container = options.container || false,
				handler = options.handler || false,
				masonry = options.masonry || false,
				slider = options.slider || false,
				append = options.append || false,
				query = options.query || {},
				widgetSettings = options.widgetSettings || {},
				postID = options.postID || false,
				queriedID = options.queriedID || false,
				elementID = options.elementID || false,
				page = options.page || 1,
				preventCSS = options.preventCSS || false,
				listingType = options.listingType || false,
				extraProps = options.extraProps || false,
				isEditMode = window.elementorFrontend && window.elementorFrontend.isEditMode();

			doneCallback = doneCallback || function( response ) {};

			if ( ! container|| ! handler ) {
				return;
			}

			if ( ! preventCSS ) {
				container.css({
					pointerEvents: 'none',
					opacity: '0.5',
					cursor: 'default',
				});
			}

			var requestData = {
					action: 'jet_engine_ajax',
					handler: handler,
					query: query,
					widget_settings: widgetSettings,
					page_settings: {
						post_id: postID,
						queried_id: queriedID,
						element_id: elementID,
						page: page,
					},
					listing_type: listingType,
					isEditMode: isEditMode,
					addedPostCSS: JetEngine.addedPostCSS
				};

			if ( extraProps ) {
				Object.assign( requestData, extraProps );
			}

			$.ajax({
				url: JetEngineSettings.ajaxlisting,
				type: 'POST',
				dataType: 'json',
				data: requestData,
				headers: {
					'Cache-Control': 'no-cache, must-revalidate, max-age=0, no-store, private',
				},
			}).done( function( response ) {

				// container.removeAttr( 'style' );

				// Manual reset container style to prevent removal of masonry styles.
				if ( !preventCSS ) {
					container.css( {
						pointerEvents: '',
						opacity: '',
						cursor: '',
					} );
				}

				if ( response.success ) {

					JetEngine.enqueueAssetsFromResponse( response );

					container.data( 'page', page );

					var $html = $( response.data.html );

					if ( slider && slider.length ) {

						var $slider = slider.find( '> .jet-listing-grid__items' );

						if ( ! $slider.hasClass( 'slick-initialized' ) ) {

							if ( append ) {
								container.append( $html );
							} else {
								container.html( $html );
							}

							var itemsCount = container.find( '> .jet-listing-grid__item' ).length;

							slider.addClass( 'jet-listing-grid__slider' );
							JetEngine.initSlider( slider, { itemsCount: itemsCount } );

						} else {
							$html.each( function( index, el ) {
								$slider.slick( 'slickAdd', el );
							});
						}

					} else {

						if ( append ) {
							container.append( $html );
						} else {
							container.html( $html );
						}

						if ( masonry && masonry.length ) {
							//JetEngine.initMasonry( masonry );
							masonry.trigger( 'jet-engine/listing/recalculate-masonry-listing' );
						}

					}

					// Re-init Bricks scripts
					JetEngine.reinitBricksScripts( elementID );

					Promise.all( JetEngine.assetsPromises ).then( function() {
						JetEngine.initElementsHandlers( $html );
						JetEngine.assetsPromises = [];
					} );

					if ( response.data.fragments ) {
						for ( var selector in response.data.fragments ) {
							var $selector = $( selector );

							if ( $selector.length ) {
								$selector.html( response.data.fragments[ selector ] );
							}
						}
					}

					$( document ).trigger( 'jet-engine/listing/ajax-get-listing/done', [ $html, options ] );
				}

			} ).done( doneCallback ).fail( function() {
				container.removeAttr( 'style' );
				if ( failCallback ) {
					failCallback.call();
				}

			} );

		},

		loadMoreListing: function( args ) {

			var instance = {

				setup: function() {
					this.container = args.container;
					this.masonry   = args.masonry;
					this.slider    = args.slider;
					this.settings  = args.settings;
					this.elementID  = args.elementID;

					this.wrapper = this.container.closest( '.jet-listing-grid' );

					this.type      = this.settings.type || 'click';
					this.page      = parseInt( this.container.data( 'page' ), 10 ) || 0;
					this.pages     = parseInt( this.container.data( 'pages' ), 10 ) || 0;
					this.queriedID = this.container.data( 'queried-id' ) || false;
				},

				init: function() {

					this.setup();

					switch ( this.type ) {
						case 'click':

							this.handleMore();

							break;

						case 'scroll':

							if ( ( ! window.elementorFrontend || ! window.elementorFrontend.isEditMode() ) && ! this.slider.length ) {
								this.handleInfiniteScroll();
							}

							break;
					}
				},

				handleMore: function() {

					if ( ! this.settings.more_el ) {
						return;
					}

					var self    = this,
						$button = $( this.settings.more_el );

					if ( ! $button.length ) {
						return;
					}

					if ( ! this.pages || this.page === this.pages && ! window.elementor ) {
						$button.css( 'display', 'none' );
					} else {
						$button.removeAttr( 'style' );
					}

					$( document )
						.off( 'click', this.settings.more_el )
						.on( 'click', this.settings.more_el, function( event ) {
							event.preventDefault();

							if ( ! self.pages || self.page >= self.pages) {
								$button.css( 'display', 'none' );
								return;
							}

							$button.css( {
								pointerEvents: 'none',
								opacity: '0.5',
								cursor: 'default',
							} );

							self.ajaxGetItems( function( response ) {
									$button.removeAttr( 'style' );

									if ( response.success && self.page === self.pages ) {
										$button.css( 'display', 'none' );
									}
								}, function() {
									$button.button.removeAttr( 'style' );
								}
							);
						} );
				},

				handleInfiniteScroll: function() {

					if ( this.container.hasClass( 'jet-listing-not-found' ) ) {
						return;
					}

					if ( ! this.pages || this.page === this.pages ) {
						return;
					}

					var self     = this,
						$trigger = this.wrapper.find( '.jet-listing-grid__loader' ),
						offset   = '0%';

					if ( ! $trigger.length ) {
						$trigger = $( '<div>', {
							class: 'jet-listing-grid__loading-trigger'
						} );

						this.wrapper.append( $trigger );
					}

					// Prepare offset value.
					if ( this.settings.widget_settings && this.settings.widget_settings.load_more_offset ) {
						var offsetValue = this.settings.widget_settings.load_more_offset;

						switch ( typeof offsetValue ) {
							case 'object':
								var size = offsetValue.size ? offsetValue.size : '0',
									unit = offsetValue.unit ? offsetValue.unit : 'px';

								offset = size + unit;
								break;

							case 'number':
							case 'string':
								offset = offsetValue + 'px';
								break;
						}
					}

					var observer = new IntersectionObserver(
						function( entries, observer ) {

							if ( entries[0].isIntersecting ) {

								self.ajaxGetItems( function() {

									// Re-init observer if the last page is not loaded
									if ( self.page !== self.pages ) {
										setTimeout( function() {
											observer.observe( entries[0].target );
										}, 250 );
									}

								} );

								// Detach observer
								observer.unobserve( entries[0].target );
							}
						},
						{
							rootMargin: '0% 0% ' + offset + ' 0%',
						}
					);

					observer.observe( $trigger[0] );
				},

				ajaxGetItems: function( doneCallback, failCallback ) {
					var self = this;

					this.page++;

					this.wrapper.addClass( 'jet-listing-grid-loading' );

					JetEngine.ajaxGetListing( {
							handler:        'listing_load_more',
							container:      this.container,
							masonry:        this.masonry,
							slider:         this.slider,
							append:         true,
							query:          this.settings.query,
							widgetSettings: this.settings.widget_settings,
							page:           this.page,
							elementID:      this.elementID,
							queriedID:      this.queriedID,
							preventCSS:     !! this.wrapper.find( '.jet-listing-grid__loader' ).length, // Prevent CSS if listing has the loader.
						}, function( response ) {

							JetEngine.lazyLoading = false;
							self.wrapper.removeClass( 'jet-listing-grid-loading' );

							if ( doneCallback ) {
								doneCallback( response );
							}

							$( document ).trigger( 'jet-engine/listing-grid/after-load-more', [args, response] );

						}, function() {

							JetEngine.lazyLoading = false;
							self.wrapper.removeClass( 'jet-listing-grid-loading' );

							if ( failCallback ) {
								failCallback();
							}

					} );
				},
			};

			instance.init();
		},

		lazyLoadListing: function( args ) {

			var $wrapper = args.container.find( '.jet-listing-grid' ),
				observer = new IntersectionObserver(
					function( entries, observer ) {

						if ( entries[0].isIntersecting ) {

							JetEngine.lazyLoading = true;

							if ( ! $wrapper.length ) {
								$wrapper = args.container;
							}

							$wrapper.addClass( 'jet-listing-grid-loading' );

							JetEngine.ajaxGetListing( {
								handler: 'get_listing',
								container: args.container,
								masonry: false,
								slider: false,
								append: false,
								elementID: args.elementID,
								postID: args.postID,
								queriedID: args.queriedID,
								query: args.query,
								widgetSettings: args.widgetSettings,
								listingType: args.listingType,
								preventCSS: true,
								extraProps: args.extraProps,
							}, function( response ) {

								$wrapper.removeClass( 'jet-listing-grid-loading' );

								var $widget = args.container.closest( '.elementor-widget' );

								if ( ! $widget.length ) {
									$widget = args.container.closest( '.jet-listing-grid--blocks' );
								}

								if ( ! $widget.length ) {
									$widget = args.container;
								}

								if ( $widget.length ) {
									$widget.find( '.jet-listing-grid' ).first().removeClass( 'jet-listing-grid--lazy-load' );
									$widget.find( '.jet-listing-grid' ).first().addClass( 'jet-listing-grid--lazy-load-completed' );
								}

								JetEngine.widgetListingGrid( $widget );
								JetEngine.lazyLoading = false;

								let needReInitFilters = false;
								let isFrontend = JetEngine.isFrontend();

								if ( isFrontend && window.JetSmartFilterSettings ) {

									if ( response.data.filters_data ) {
										$.each( response.data.filters_data, function( param, data ) {
											if ( 'extra_props' === param ) {
												window.JetSmartFilterSettings[ param ] = $.extend(
													{},
													window.JetSmartFilterSettings[ param ],
													data
												);
											} else {
												if ( window.JetSmartFilterSettings[ param ]['jet-engine'] ) {
													window.JetSmartFilterSettings[ param ]['jet-engine'] = $.extend(
														{},
														window.JetSmartFilterSettings[ param ]['jet-engine'],
														data
													);
												} else {
													window.JetSmartFilterSettings[ param ]['jet-engine'] = data;
												}
											}
										});

										needReInitFilters = true;
									}

									if ( response.data.indexer_data ) {
										const {
											provider = false,
											query = {}
										} = response.data.indexer_data;

										window.JetSmartFilters.setIndexedData( provider, query );
									}
								}

								// ReInit filters
								if ( needReInitFilters && window.JetSmartFilters ) {
									window.JetSmartFilters.reinitFilters();
								}

								$( document ).trigger( 'jet-engine/listing-grid/after-lazy-load', [ args, response, $widget ] );

							}, function() {
								JetEngine.lazyLoading = false;

								if ( ! $wrapper.length ) {
									$wrapper = args.container;
								}

								$wrapper.removeClass( 'jet-listing-grid-loading' );
							} );

							// Detach observer after the first load the listing
							observer.unobserve( entries[0].target );
						}
					},
					{
						rootMargin: '0% 0% ' + args.offset + ' 0%'
					}
				);

			observer.observe( args.container[0] );
		},

		ensureJSON: function( maybeJSON ) {

			if ( ! maybeJSON ) {
				return maybeJSON;
			}

			if ( 'string' === typeof maybeJSON ) {
				console.log( maybeJSON );
				//maybeJSON = JSON.parse( maybeJSON );
			}

			return maybeJSON;

		},

		rerunElementorAnimation: function( $scope, forceRerun = false ) {
			let selector = '.elementor-element[data-settings*="_animation"]';

			if ( ! forceRerun ) {
				selector += ':is( .elementor-invisible, :not(.jet-engine-animation-rerun) )';
			}

			$scope.find( selector ).each(
				( i, el ) => {
					const settings = JSON.parse( el.dataset.settings || '{}' );
					const $el = $( el );

					if ( settings._animation ) {
						const classes = `animated ${settings._animation}`;
						$el.removeClass( classes );

						setTimeout(
							() => {
								$el.removeClass( 'elementor-invisible' ).addClass( classes );
								$el.addClass( 'jet-engine-animation-rerun' );
							},
							settings._animation_delay
						);

					}
				}
			);
		},

		widgetDynamicField: function( $scope ) {

			var $slider = $scope.find( '.jet-engine-gallery-slider' );

			if ( $slider.length ) {
				if ( $.isFunction( $.fn.imagesLoaded ) ) {
					$slider.imagesLoaded().always( function( instance ) {

						var $eWidget = $slider.closest( '.elementor-widget' );

						if ( $slider.hasClass( 'slick-initialized' ) ) {
							$slider.slick( 'refresh', true );
						} else {
							var atts = $slider.data( 'atts' );

							atts = JetEngine.ensureJSON( atts );
							atts.slidesToShowDesktop = +atts.slidesToShow || 1;

							if ( $eWidget.length ) {
								var settings     = JetEngine.getElementorElementSettings( $scope ),
									eBreakpoints = window.elementorFrontend.config.responsive.activeBreakpoints,
									responsive   = [];

								if ( settings.img_slider_cols || settings.img_slider_cols_widescreen ) {
									atts.slidesToShow = settings.img_slider_cols_widescreen ? +settings.img_slider_cols_widescreen : +settings.img_slider_cols;
									atts.slidesToShowDesktop = +settings.img_slider_cols;
								}

								Object.keys( eBreakpoints ).reverse().forEach( function( breakpointName ) {

									if ( settings['img_slider_cols_' + breakpointName] ) {

										if ( 'widescreen' === breakpointName ) {

											responsive.push( {
												breakpoint: eBreakpoints[breakpointName].value,
												settings:   {
													slidesToShow: +settings['img_slider_cols'],
													swiperSlidesToShow: +settings['img_slider_cols_widescreen'],
												}
											} );

										} else {
											var breakpointSettings = {
												breakpoint: eBreakpoints[breakpointName].value + 1,
												settings:   {
													slidesToShow: +settings['img_slider_cols_' + breakpointName],
												}
											};

											responsive.push( breakpointSettings );
										}
									}

								} );

								atts.responsive = responsive;
							}

							if ( $slider.hasClass( 'slick-lib' ) ) {
								atts = window.JetPlugins.hooks.applyFilters(
									'jet-engine.dynamic-field.slider-options.slick',
									atts,
									$slider
								);

								$slider.slick( atts );
							} else {
								atts.loop = true;

								$slider.addClass( 'swiper' );

								const slider = $slider[0];
								const $slides = $slider.find( '.jet-engine-gallery-slider__item' );

								let nextArrowClass = 'swiper-button-next-svg';
								let prevArrowClass = 'swiper-button-prev-svg';
								let nextArrowElement;
								let prevArrowElement;

								let existingNextArrow = slider.querySelector( ':scope > .' + nextArrowClass );
								let existingPrevArrow = slider.querySelector( ':scope > .' + prevArrowClass );

								let arrowTemplate = document.createElement( 'template' );

								if ( existingNextArrow ) {
									nextArrowElement = existingNextArrow;
								} else {
									arrowTemplate.innerHTML = atts.nextArrow;
									nextArrowElement = arrowTemplate.content.firstChild;
									nextArrowElement.classList.add( nextArrowClass, 'swiper-arrow', nextArrowClass );

									slider.appendChild( nextArrowElement );
								}

								if ( existingPrevArrow ) {
									prevArrowElement = existingPrevArrow;
								} else {
									arrowTemplate.innerHTML = atts.prevArrow;
									prevArrowElement = arrowTemplate.content.firstChild;
									prevArrowElement.classList.add( prevArrowClass, 'swiper-arrow', prevArrowClass );

									slider.appendChild( prevArrowElement );
								}
								
								atts.navigation = {
									addIcons: false,
									nextEl: nextArrowElement,
									prevEl: prevArrowElement,
								};

								atts.slidesPerView = atts.slidesToShowDesktop;

								if ( atts?.responsive?.length > 1 ) {
									let breakpoints = {};

									for ( const item of atts.responsive ) {
										const width  = item.breakpoint;
										const number = item.settings.swiperSlidesToShow ? item.settings.swiperSlidesToShow : item.settings.slidesToShow;
										
										let breakpoint = {
											slidesPerView: number,
										};

										if ( atts.slidesPerGroup > number ) {
											breakpoint.slidesPerGroup = number;
										}

										breakpoints[ width ] = breakpoint;
									}

									breakpoints[1920] = {
										slidesPerView: atts.slidesToShowDesktop,
									};

									let widthList = Object.keys( breakpoints );
									widthList = widthList.map( a=> +a ).sort( (a, b) => Math.sign( a - b ) );
									
									let swiperBreakpoints = {};

									for ( let i = 1, c = widthList.length; i < c; i++ ) {
										swiperBreakpoints[ widthList[ i - 1 ] ] = breakpoints[ widthList[ i ] ];
									}

									atts.slidesPerView = breakpoints[ widthList[0] ].slidesPerView;
									atts.breakpoints = swiperBreakpoints;
									delete atts.responsive;
								}

								$slides.addClass( 'swiper-slide' );

								atts = window.JetPlugins.hooks.applyFilters(
									'jet-engine.dynamic-field.slider-options.swiper',
									atts,
									$slider
								);

								if ( $slides.length < 2 ) {
									atts.loop = false;
									atts.centeredSlides = true;
									atts.navigation = false;
									nextArrowElement.remove();
									prevArrowElement.remove();
								}

								if ( atts.loop ) {
									atts.loopFillGroupWithBlank = true;
            						atts.watchOverflow = true;
								}

								if ( slider.swiper ) {
									slider.swiper.destroy();
								}

								atts.on = {
									afterInit: function( swiper ) {
										if ( ! swiper.$el.hasClass('jet-engine-gallery-lightbox') || ! window?.PhotoSwipeLightbox) {
											return;
										}

										let lightbox = new PhotoSwipeLightbox({
											mainClass: 'brx',
											gallery: slider,
											children: 'a',
											showHideAnimationType: 'none',
											zoomAnimationDuration: false,
											pswpModule: PhotoSwipe5,
										});

										let slideCount = swiper.slides.length;

										lightbox.addFilter( 'numItems', numItems => slideCount );

										lightbox.addFilter('clickedIndex', function (clickedIndex, e) {
											const slide = e.target.closest('.swiper-slide');

											if (!slide) {
												return clickedIndex;
											}

											if (clickedIndex >= slideCount) {
												return clickedIndex % slideCount;
											}

											return clickedIndex;
										});

										lightbox.addFilter('thumbEl', (thumbnail, itemData, index) => {
											return thumbnail;
										});

										lightbox.addFilter('thumbBounds', (thumbBounds, itemData, index) => {
											return thumbBounds;
										});

										lightbox.init();
									}
								};

								new Swiper( slider, atts );
							}
						}
					} );
				}
			}

			$slider.on('init', function (event, slick) {

				const slider = event.target;

				if (!slider.classList.contains('jet-engine-gallery-lightbox') || ! window?.PhotoSwipeLightbox) {
					return;
				}

				let lightbox = new PhotoSwipeLightbox({
					mainClass: 'brx',
					gallery: slider,
					children: 'a',
					showHideAnimationType: 'none',
					zoomAnimationDuration: false,
					pswpModule: PhotoSwipe5,
				});

				lightbox.addFilter('numItems', numItems => slick.slideCount);

				lightbox.addFilter('clickedIndex', function (clickedIndex, e) {
					const slide = e.target.closest('.slick-slide');

					if (!slide) {
						return clickedIndex;
					}

					if (clickedIndex >= slick.slideCount) {
						return clickedIndex % slick.slideCount;
					}

					return clickedIndex;
				});

				lightbox.addFilter('thumbEl', (thumbnail, itemData, index) => {
					return thumbnail;
				});

				lightbox.addFilter('thumbBounds', (thumbBounds, itemData, index) => {
					return thumbBounds;
				});

				lightbox.init();
			});

			// Masonry init
			var $masonry = $scope.find( '.jet-engine-gallery-grid--masonry' );

			if ( $masonry.length ) {
				JetEngine.initMasonry( $masonry, {
					columnsKey: 'img_columns',
					itemSelector: '> .jet-engine-gallery-grid__item',
				} );
			}

		},

		initElementsHandlers: function( $selector ) {

			// Actual init
			window.JetPlugins.init( $selector );

			// Legacy Elementor-only init
			$selector.find( '[data-element_type]' ).each( function() {

				var $this       = $( this ),
					elementType = $this.data( 'element_type' );

				if ( !elementType ) {
					return;
				}

				if ( ! window?.elementorFrontend?.hooks?.doAction ) {
					return;
				}

				if ( 'widget' === elementType ) {
					elementType = $this.data( 'widget_type' );
					window.elementorFrontend.hooks.doAction( 'frontend/element_ready/widget', $this, $ );
				}

				window.elementorFrontend.hooks.doAction( 'frontend/element_ready/global', $this, $ );
				window.elementorFrontend.hooks.doAction( 'frontend/element_ready/' + elementType, $this, $ );

			} );

			if ( window.elementorFrontend ) {
				const elementorLazyLoad = new Event( "elementor/lazyload/observe" );
				document.dispatchEvent( elementorLazyLoad );
			}

			if ( window.JetPopupFrontend && window.JetPopupFrontend.initAttachedPopups ) {
				$selector.find( '.jet-popup-attach-event-inited' ).removeClass( 'jet-popup-attach-event-inited' );
				window.JetPopupFrontend.initAttachedPopups( $selector );
			}

		},

		getElementorElementSettings: function( $scope ) {

			if ( window.elementorFrontend && window.elementorFrontend.isEditMode() && $scope.hasClass( 'elementor-element-edit-mode' ) ) {
				return JetEngine.getEditorElementSettings( $scope );
			}

			return $scope.data( 'settings' ) || {};
		},

		getEditorElementSettings: function( $scope ) {
			var modelCID = $scope.data( 'model-cid' ),
				elementData;

			if ( ! modelCID ) {
				return {};
			}

			if ( ! window.elementorFrontend.hasOwnProperty( 'config' ) ) {
				return {};
			}

			if ( ! window.elementorFrontend.config.hasOwnProperty( 'elements' ) ) {
				return {};
			}

			if ( ! window.elementorFrontend.config.elements.hasOwnProperty( 'data' ) ) {
				return {};
			}

			elementData = window.elementorFrontend.config.elements.data[ modelCID ];

			if ( ! elementData ) {
				return {};
			}

			return elementData.toJSON();
		},

		debounce: function( threshold, callback ) {
			var timeout;

			return function debounced( $event ) {
				function delayed() {
					callback.call( this, $event );
					timeout = null;
				}

				if ( timeout ) {
					clearTimeout( timeout );
				}

				timeout = setTimeout( delayed, threshold );
			};
		},

		updateAddedStyles: function() {
			if ( window.JetEngineSettings && window.JetEngineSettings.addedPostCSS ) {
				$.each( window.JetEngineSettings.addedPostCSS, function( ind, cssID ) {
					JetEngine.addedStyles.push( 'elementor-post-' + cssID );
					JetEngine.addedPostCSS.push( cssID );
				} );
			}
		},

		enqueueAssetsFromResponse: function( response ) {
			if ( response.data.scripts ) {
				JetEngine.enqueueScripts( response.data.scripts );
			}

			if ( response.data.styles ) {
				JetEngine.enqueueStyles( response.data.styles );
			}
		},

		enqueueScripts: function( scripts ) {
			$.each( scripts, function( handle, scriptHtml ) {
				JetEngine.enqueueScript( handle, scriptHtml )
			} );
		},

		enqueueStyles: function( styles ) {
			$.each( styles, function( handle, styleHtml ) {
				JetEngine.enqueueStyle( handle, styleHtml )
			} );
		},

		enqueueScript: function( handle, scriptHtml ) {

			if ( -1 !== JetEngine.addedScripts.indexOf( handle ) ) {
				return;
			}

			if ( ! scriptHtml ) {
				return;
			}

			var selector = 'script[id="' + handle + '-js"]';

			if ( $( selector ).length ) {
				return;
			}

			//$( 'body' ).append( scriptHtml );

			var scriptsTags = scriptHtml.match( /<script[\s\S]*?<\/script>/gm );

			if ( scriptsTags.length ) {

				for ( var i = 0; i < scriptsTags.length; i++ ) {

					JetEngine.assetsPromises.push(
						new Promise( function( resolve, reject ) {

							var $tag = $( scriptsTags[i] );

							if ( $tag[0].src ) {

								var tag = document.createElement( 'script' );

								tag.type   = $tag[0].type;
								tag.src    = $tag[0].src;
								tag.id     = $tag[0].id;
								tag.async  = false;
								tag.onload = function() {
									resolve();
								};

								document.body.append( tag );
							} else {
								$( 'body' ).append( scriptsTags[i] );
								resolve();
							}
						} )
					);
				}
			}

			JetEngine.addedScripts.push( handle );
		},

		enqueueStyle: function( handle, styleHtml ) {

			if ( -1 !== handle.indexOf( 'google-fonts' ) ) {
				JetEngine.enqueueGoogleFonts( handle, styleHtml );
				return;
			}

			if ( -1 !== JetEngine.addedStyles.indexOf( handle ) ) {
				return;
			}

			var selector = 'link[id="' + handle + '-css"],style[id="' + handle + '"]';

			if ( $( selector ).length ) {
				return;
			}

			$( 'head' ).append( styleHtml );

			JetEngine.addedStyles.push( handle );

			if ( -1 !== handle.indexOf( 'elementor-post' ) ) {
				var postID = handle.replace( 'elementor-post-', '' );
				JetEngine.addedPostCSS.push( postID );
			}
		},

		enqueueGoogleFonts: function( handle, styleHtml ) {

			var selector = 'link[id="' + handle + '-css"]';

			if ( $( selector ).length ) {}

			$( 'head' ).append( styleHtml );
		},

		isFrontend: function () {
			// Check the Elementor
			if (typeof window.elementorFrontend !== 'undefined') {
				return !window.elementorFrontend.isEditMode();
			}

			// Check the Bricks
			if (typeof window.bricksIsFrontend !== 'undefined') {
				return window.bricksIsFrontend;
			}

			// If no builders are found, we assume it is frontend.
			return true;
		},

		reinitBricksScripts: function (elementId) {
			if (!window.bricksIsFrontend) {
				return;
			}

			document.dispatchEvent(
				new CustomEvent("bricks/ajax/query_result/displayed", {
					detail: {
						queryId: elementId || null
					}
				})
			);
		},

		filters: ( function() {

			var callbacks = {};

			return {

				addFilter: function( name, callback ) {

					if ( ! callbacks.hasOwnProperty( name ) ) {
						callbacks[name] = [];
					}

					callbacks[name].push(callback);

				},

				applyFilters: function( name, value, args ) {

					if ( ! callbacks.hasOwnProperty( name ) ) {
						return value;
					}

					if ( args === undefined ) {
						args = [];
					}

					var container = callbacks[ name ];
					var cbLen     = container.length;

					for (var i = 0; i < cbLen; i++) {
						if (typeof container[i] === 'function') {
							value = container[i](value, args);
						}
					}

					return value;
				}

			};

		})()

	};

	$( window ).on( 'elementor/frontend/init', JetEngine.init );

	window.JetEngine = JetEngine;

	window.JetPlugins.hooks.doAction(
		'jet-engine.modules-include',
		JetEngine,
		$
	);

	JetEngine.commonInit();

	window.addEventListener( 'DOMContentLoaded', function() {
		setTimeout( () => JetEngine.initBlocks() );
		JetEngine.initDone = true;
	} );

	window.jetEngineBricks = function() {
		JetEngine.initBricks();
	}

	$( window ).trigger( 'jet-engine/frontend/loaded' );

}( jQuery ) );
