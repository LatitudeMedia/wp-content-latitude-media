const $ = jQuery;

window.JetPlugins.hooks.addAction(
    'jet-engine.modules-include',
    'module-jet-popup',
    function( JetEngine ) {
        JetEngine.triggerPopup = triggerPopup;
        JetEngine.addPopupFilter = addPopupFilter;
        JetEngine.prepareJetPopup = prepareJetPopup;

        $( window ).on( 'elementor/frontend/init', initElementor );

        JetEngine.addPopupFilter();
    }
);

// window.JetPlugins.hooks.addAction(
//     'jet-engine.common-events',
//     'module-calendar',
//     function( $scope, JetEngine ) {
//         $scope;
//     }
// );

function initElementor() {
    window.elementorFrontend.hooks.addFilter(
        'jet-popup/widget-extensions/popup-data',
        JetEngine.prepareJetPopup
    );
}

function addPopupFilter() {
    window.JetPlugins.hooks.addFilter(
        'jet-popup.show-popup.data',
        'JetEngine.popupData',
        ( popupData, $popup, $triggeredBy ) => {

            if ( ! $triggeredBy ) {
                return popupData;
            }

            if ( $triggeredBy.data( 'popupIsJetEngine' ) ) {
                popupData = JetEngine.prepareJetPopup( popupData, { 'is-jet-engine': true }, $triggeredBy );
            }

            return popupData;
        }
    );
}

function prepareJetPopup( popupData, widgetData, $scope ) {

    var postId = null;

    if ( widgetData['is-jet-engine'] ) {
        popupData['isJetEngine'] = true;

        var $gridItems     = $scope.closest( '.jet-listing-grid__items' ),
            $gridItem      = $scope.closest( '.jet-listing-grid__item' ),
            $calendarItem  = $scope.closest( '.jet-calendar-week__day-event' ),
            $itemObject    = $scope.closest( '[data-item-object]' ),
            filterProvider = false,
            filterQueryId  = 'default';

        if ( $gridItems.length ) {
            popupData['listingSource'] = $gridItems.data( 'listing-source' );
            popupData['listingId']     = $gridItems.data( 'listing-id' );
            popupData['queryId']       = $gridItems.data( 'query-id' );
        } else {

            var $queryItems    = $scope.closest( '[data-query-id]' ),
                $listingSource = $scope.closest( '[data-listing-source]' );

            if ( $queryItems.length ) {
                popupData['queryId'] = $queryItems.data( 'query-id' );
            }

            if ( $listingSource.length ) {
                popupData['listingSource'] = $listingSource.data( 'listing-source' );
            }
        }

        if ( $itemObject?.length ) {
            popupData['postId'] = $itemObject.data( 'item-object' );

            filterProvider = $itemObject.data( 'render-type' );

            if ( ! filterProvider && $itemObject.hasClass( 'jet-dynamic-table__row' ) ) {
                filterProvider = 'jet-data-table';
            }
        } else if ( $gridItem.length ) {
            popupData['postId'] = $gridItem.data( 'post-id' );
            filterProvider = 'jet-engine';
        } else if ( $calendarItem.length ) {
            popupData['postId'] = $calendarItem.data( 'post-id' );
            filterProvider = 'jet-engine-calendar';
        } else if ( window.elementorFrontendConfig && window.elementorFrontendConfig.post ) {
            popupData['postId'] = window.elementorFrontendConfig.post.id;
        } else if ( JetEngineSettings?.post_id || JetEngineSettings?.queried_object_id ) {
            popupData['postId'] = JetEngineSettings.post_id || JetEngineSettings.queried_object_id;

            if ( JetEngineSettings.queried_object_class ) {
                popupData['listingSource'] = JetEngineSettings.queried_object_class;
            }
        }

        if ( window.JetEngineFormsEditor && window.JetEngineFormsEditor.hasEditor ) {
            popupData['hasEditor'] = true;
        }

        // Add the filtered query to the popup data
        if ( window.JetSmartFilters ) {

            switch ( filterProvider ) {
                case 'jet-engine':
                    var nav = $gridItems.data( 'nav' );

                    if ( nav.widget_settings?._element_id ) {
                        filterQueryId = nav.widget_settings._element_id;
                    }
                    break;

                case 'jet-engine-calendar':
                    var settings = $calendarItem.closest( '.jet-listing-calendar' ).data( 'settings' );

                    if ( settings._element_id ) {
                        filterQueryId = settings._element_id;
                    }
                    break;

                case 'jet-data-table':
                    const table = $scope.closest( '.jet-dynamic-table' );
                    const queryId = table[0].dataset.queryId;
                    const customIds = JetEngineSettings.query_builder.custom_ids;

                    if ( customIds.length !== 0 && customIds?.[ queryId ] ) {
                        filterQueryId = customIds[ queryId ];
                    }
                    break;
            }

            filterProvider = window.JetPlugins.hooks.applyFilters( 'jet-engine.prepareJetPopupData.filterProvider', filterProvider, $scope, widgetData );
            filterQueryId  = window.JetPlugins.hooks.applyFilters( 'jet-engine.prepareJetPopupData.filterQueryId', filterQueryId, $scope, widgetData );

            if ( popupData.queryId && filterProvider
                && window.JetSmartFilters?.filterGroups?.[ filterProvider + '/' + filterQueryId ]?.currentQuery
            ) {
                popupData['filtered_query'] = window.JetSmartFilters.filterGroups[ filterProvider + '/' + filterQueryId ].currentQuery;
            }
        }

    }

    return popupData;

}

function triggerPopup( popupID, isJetEngine, postID ) {

    if ( ! popupID ) {
        return;
    }

    var popupData = {
        popupId: 'jet-popup-' + popupID,
    };

    if ( isJetEngine ) {
        popupData.isJetEngine = true;
        popupData.postId      = postID;
    }

    $( window ).trigger( {
        type: 'jet-popup-open-trigger',
        popupData: popupData
    } );

}
