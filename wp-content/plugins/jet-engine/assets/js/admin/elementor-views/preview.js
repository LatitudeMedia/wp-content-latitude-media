(function( $ ) {

	"use strict";

	var JetEngineElementorPreview = {

		selectors: {
			document: '.elementor[data-elementor-type="jet-listing-items"]',
			newLising: '.jet-new-listing-item',
		},

		init: function() {

			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/jet-listing-grid.default', JetEngineElementorPreview.loadHandles );

			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function( $scope ) {
				const widgetType = $scope.data( 'widget_type' );

				if ( widgetType && widgetType.includes( 'jet-engine-component' ) ) {
					$scope.find( '.elementor-invisible' ).each( function() {
						$( this ).removeClass( 'elementor-invisible' );
					} );
				}
			} );


			$( document ).on( 'jet-engine/listing-grid/after-lazy-load', JetEngineElementorPreview.loadHandlesOnLazyLoad );

			window.elementorFrontend.on( 'components:init', function () {
				window.elementor.on( 'document:loaded', function () {
					JetEngineElementorPreview.loadBackHandles();
				} );
			});

			$( document )
				.on( 'click', '.jet-engine-document-handle',      JetEngineElementorPreview.documentHandleClick )
				.on( 'click', '.jet-engine-document-back-handle', JetEngineElementorPreview.documentBackHandleClick );


			// Re-Init the masonry js on change the columns setting
			window.elementor.on( 'document:loaded', () => {
				window.elementor.channels.editor.on(
					'change:jet-listing-grid',
					JetEngineElementorPreview.reInitMasonryOnChangeColumns
				);
			} );

			/**
			 * Re-init elements inside JetEngine components on Elementor global widget init
			 * @see https://github.com/Crocoblock/issues-tracker/issues/13891
			 */
			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $scope ) {
				if ( $scope.data( 'widget_type' ) && $scope.data( 'widget_type' ).includes( 'jet-engine-component' ) ) {

					// Prevent double initialization
					if ( $scope.data( 'is_component_initialized' ) ) {
						return;
					}

					$scope.data( 'is_component_initialized', true );

					window.JetEngine.initElementsHandlers(
						$scope.find( 'div[data-elementor-type="jet-engine-component"]' ).first()
					);
				}
			} );
		},

		reInitMasonryOnChangeColumns: function( childView, editedElement ) {

			const settingName = childView.model.get( 'name' );

			if ( 'columns' !== settingName && -1 === settingName.indexOf( 'columns_' ) ) {
				return;
			}

			if ( ! editedElement.model.getSetting( 'is_masonry' ) ) {
				return;
			}

			const $masonry = editedElement.$el.find( '.jet-listing-grid__masonry' );

			if ( ! $masonry.length ) {
				return;
			}

			if ( window.JetEngine ) {
				window.JetEngine.runMasonry( $masonry );
			} else {
				editedElement.renderHTML();
			}
		},

		loadHandlesOnLazyLoad: function( event, args ) {
			JetEngineElementorPreview.loadHandles( $( args.container ) );
		},

		loadHandles: function( $scope ) {
			var $listing   = $scope.find( '.jet-listing' ).first(),
				$documents = $scope.find( JetEngineElementorPreview.selectors.document ),
				handlesDocuments = [],
				$handleHtml;

			// Nested lists should not add handles.
			if ( $listing.closest( JetEngineElementorPreview.selectors.document ).length ) {
				return;
			}

			if ( !$documents.length ) {
				return;
			}

			if ( $documents.hasClass( 'elementor-edit-mode' ) ) {
				return;
			}

			$handleHtml = '<div class="jet-engine-document-handle" role="button" title="' + window.JetEngineElementorPreviewConfig.i18n.edit + '"><i class="eicon-edit"></i></div>';

			$documents.each( function() {

				var $document = $( this ),
					documentID = $document.data( 'elementorId' );

				if ( -1 !== handlesDocuments.indexOf( documentID ) ) {
					return;
				}

				$document.addClass( 'jet-engine-document-edit-item' );
				$document.prepend( $handleHtml );
				handlesDocuments.push( documentID );
			} );
		},

		loadBackHandles: function() {
			var $documents = $( JetEngineElementorPreview.selectors.document ).filter( '.jet-engine-document-edit-item.elementor-edit-mode' ),
				$handleHtml;

			if ( ! $documents.length ) {
				return;
			}

			$handleHtml = '<div class="jet-engine-document-back-handle" role="button" title="' + window.JetEngineElementorPreviewConfig.i18n.back + '"><i class="eicon-arrow-left"></i></div>';

			$documents.prepend( $handleHtml );

			let $widgets = $( '[data-element_type="widget"]:has(.jet-engine-document-edit-item.elementor-edit-mode)' );
			JetEngineElementorPreview.removeInlineListingStyles( $widgets );
		},

		documentHandleClick: async function() {
			var $handle = $( this ),
				$document = $handle.closest( JetEngineElementorPreview.selectors.document );

			if ( $document.hasClass( 'elementor-edit-area-active' ) ) {
				return;
			}

			let widget = this.closest( '[data-element_type="widget"]' );
			let widgetId = widget.dataset.id;
			let listingId = $document.data( 'elementorId' );

			await JetEngineElementorPreview.switchDocument(
				listingId,
				`.elementor-element-${widgetId} .elementor-${listingId}`
			);

			let $handles = $( '.jet-engine-document-handle' ).filter(
				( i, e ) => {
					return e !== $handle[0] && +e?.parentNode?.dataset?.elementorId === +listingId;

				}
			);

			$handles.addClass( 'jet-engine-hidden-handle' );

			JetEngineElementorPreview.removeInlineListingStyles( $( `[data-listing-id="${listingId}"]` ) );
		},

		removeInlineListingStyles: function( $scope ) {
			$scope.find( '.jet-listing-grid__item > style' ).remove();
		},

		documentBackHandleClick: async function() {
			await JetEngineElementorPreview.switchDocument( window.elementorFrontendConfig.post.id );
			$( '.jet-engine-document-handle' ).removeClass( 'jet-engine-hidden-handle' );
		},

		switchDocument: async function( documentID, selector = '' ) {
			if ( ! documentID ) {
				return;
			}

			let args = {
				id: documentID,
			}

			if ( selector ) {
				args.selector = selector;
			}

			window.elementorCommon.api.internal( 'panel/state-loading' );
			await window.elementorCommon.api.run( 'editor/documents/switch', args ).then( function() {
				return window.elementorCommon.api.internal( 'panel/state-ready' );
			} );
		}

	};

	$( window ).on( 'elementor/frontend/init', JetEngineElementorPreview.init );

}( jQuery ));
