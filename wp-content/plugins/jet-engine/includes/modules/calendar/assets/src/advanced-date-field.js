import JetEngineRenderAdvancedDateField from 'field-ui/advanced-date-field-render';

(function( $, JetMapFieldsSettings ) {

	'use strict';

	class JetEngineAdvancedDateFields {

		constructor( args ) {
			this.events();
			this.initCallback = args.initCallback || null;
		}

		events() {
			const self = this;

			self.initFields( $( '.cx-control' ) );

			$( document ).on( 'cx-control-init', function ( event, data ) {
				if ( data?.target ) {
					self.initFields( $( data.target ) );
				}
			} );
		}

		initFields( $scope ) {

			const self = this;

			$( '.jet-engine-advanced-date-field.cx-ui-container', $scope ).each( function() {

				const $this    = $( this );
				const observer = new IntersectionObserver(
					function( entries, observer ) {

						entries.forEach( function( entry ) {

							if ( entry.isIntersecting ) {

								new JetEngineRenderAdvancedDateField( $this, {
									labels: {},
									templateEngine: null,
									initCallback: self.initCallback
								} );

								// Detach observer after the first render the field
								observer.unobserve( entry.target );
							}
						} );
					}
				);

				observer.observe( $this[0] );

			} );
		}
	}

	// Run on document ready.
	$( function () {

		const fields = new JetEngineAdvancedDateFields( {
			initCallback( data ) {
				if ( data?.newRow ) {
					window.JetEngineMetaBoxes.initDateFields( $( data.newRow ) );
				} else {
					window.JetEngineMetaBoxes.initDateFields( data.$container );
				}
			}
		} );

	} );


})( jQuery );
