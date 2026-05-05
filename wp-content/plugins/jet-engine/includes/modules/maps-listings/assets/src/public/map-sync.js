( function( $ ) {

	"use strict";

	const initMapSyncFilter = function() {

		window.JetSmartFilters.filtersList.JetEngineMapSync = 'jet-smart-filters-map-sync';
		window.JetSmartFilters.filters.JetEngineMapSync = class JetEngineMapSync extends window.JetSmartFilters.filters.BasicFilter {

			name = 'map-sync';
			mapSelector = '.jet-map-listing';
			mapDefaults = null;

			constructor( $container ) {
				
				const $filter = $container.find( '.jet-smart-filters-map-sync' );
				
				super( $container, $filter );
				
				this.$container = $container;

				const mapId = $container.data( 'query-id' );
				
				if ( mapId && mapId !== 'default' ) {
					//left .elementor-widget-container to ensure backward compatibility
					this.mapSelector = `#${mapId} > .jet-map-listing, #${mapId} > .elementor-widget-container > .jet-map-listing`;
				}

				document.addEventListener( 'jet-engine/maps/update-sync-bounds', this.updateBounds.bind( this ) );

				document.addEventListener( 'jet-engine/maps/init-sync-bounds', this.saveDefaults.bind( this ) );
			}

			updateBounds( e ) {
				if ( e.detail.div !== document.querySelector( this.mapSelector ) ) {
					return;
				}

				if ( ! this.mapDefaults ) {
					this.saveDefaults( e );
				}

				this.mapDefaults.map.jetPlugins.autoCenterBlock = true;

				let prevValue = this.dataValue || {};
				
				this.dataValue = e.detail.bounds;

				if ( JSON.stringify( prevValue ) === JSON.stringify( this.dataValue ) ) {
					this.mapDefaults.map.jetPlugins.autoCenterBlock = false;
				}

				this.wasChanged ? this.wasChanged() : this.wasСhanged();
			}

			saveDefaults( e ) {
				if ( e.detail.div !== document.querySelector( this.mapSelector ) ) {
					return;
				}

				this.mapDefaults = e.detail;

				const map = this.mapDefaults.map;

				this.mapDefaults.center = map.getCenter();
				this.mapDefaults.zoom   = map.getZoom();
			}

			reset() {
				super.reset();

				if ( this.mapDefaults ) {
					this.mapDefaults.map.jetPlugins.autoCenterBlock = false;
				}

				// if ( this.mapDefaults === null ) {
				// 	return;
				// }

				// this.mapDefaults.mapProvider.panTo(
				// 	{
				// 		map: this.mapDefaults.map,
				// 		position: this.mapDefaults.center,
				// 		zoom: this.mapDefaults.zoom
				// 	},
				// 	true
				// );
			}

			processData() {
				return;
			}

		};

	}

	document.addEventListener( 'DOMContentLoaded', ( e ) => {
		initMapSyncFilter();
	});

}( jQuery ) );
