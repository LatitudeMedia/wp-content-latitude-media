const $ = jQuery;

window.JetPlugins.hooks.addAction(
    'jet-engine.modules-include',
    'module-calendar',
    function( JetEngine ) {
        JetEngine.filtersCompatibility = filtersCompatibility;

        document.addEventListener( 'jet-smart-filters/inited', function() {
            if ( ! window.JetEngine.initFrontStores ) {
                return;
            }
    
            window.JetSmartFilters.events.subscribe( 'ajaxFilters/updated', function( provider, queryId ) {
                const $provider = window.JetSmartFilters?.filterGroups?.[ provider + '/' + queryId ]?.$provider;

                if ( ! $provider?.length ) {
                    return;
                }

                window.JetEngine.initFrontStores( $provider );
            } );
        } );

        const initFilterConflictHandler = function() {
            const conflictHandler = class FilterConflictHandler {
        
                isResolving = false;
        
                constructor() {
                    this.init();
                }
        
                init( e ) {
                    JetSmartFilters.events.subscribe( 'fiter/change', ( filter ) => {
                        if ( this.isResolving || filter?.filterConflictHandlerBlocked ) {
                            return;
                        }
        
                        this.isResolving = true;
        
                        if ( ! [ 'map-sync', 'user-geolocation', 'location-distance' ].includes( filter?.name ) ) {
                            return;
                        }
        
                        let conflictingTypes = [];
        
                        switch ( filter.name ) {
                            case 'map-sync':
                                conflictingTypes = [ 'user-geolocation', 'location-distance' ];
                                break;
                            case 'location-distance':
                                conflictingTypes = [ 'map-sync', 'user-geolocation' ];
                                break;
                            default:
                                conflictingTypes = [ 'map-sync' ];
                        }
        
                        this.resetConflictingFilters( filter, conflictingTypes );
                    } );
                }
        
                resetConflictingFilters( filter, conflictingTypes ) {
                    for ( const conflictingFilter of this.getFilters( filter, conflictingTypes ) ) {
                        conflictingFilter.reset();
                        conflictingFilter.dataValue = false;
                        conflictingFilter.wasChanged ? conflictingFilter.wasChanged() : conflictingFilter.wasСhanged();
                    }
        
                    this.isResolving = false;
                }
        
                getFilters( filter, types ) {
                    if ( ! types.length ) {
                        return [];
                    }
        
                    let filters = [];
        
                    filter.filterGroup.filters.forEach(
                        ( f ) => {
                            if ( ! types.includes( f.name ) ) {
                                return;
                            }
        
                            filters.push( f );
                        }
                    );
        
                    return filters;
                }
        
            };
        
            new conflictHandler();
        
        }
        
        document.addEventListener( 'jet-smart-filters/inited', initFilterConflictHandler );
    }
);

window.JetPlugins.hooks.addAction(
    'jet-engine.common-events',
    'module-calendar',
    function( $scope, JetEngine ) {
        $scope
            .on( 'jet-filter-content-rendered', JetEngine.filtersCompatibility )
            .on( 'jet-filter-content-rendered', JetEngine.maybeReinitSlider );
    }
);

function filtersCompatibility( event, $provider, filtersInstance, providerType ) {

    let providers = {
        'jet-engine': true,
        'jet-engine-calendar': true,
        'jet-data-table': true,
    };

    if ( ! providers[ providerType ] ) {
        return;
    }

    /*
    No need anymore to manually re-init the block.
    JetSmartFilters automatically re-init blocks.

    var $blocksListing = $provider.closest( '.jet-listing-grid--blocks' );

    if ( $blocksListing.length ) {
        JetEngine.widgetListingGrid( $blocksListing );
    }
    */

    if ( window.JetPopupFrontend && window.JetPopupFrontend.initAttachedPopups ) {
        window.JetPopupFrontend.initAttachedPopups( $provider );
    }
}
