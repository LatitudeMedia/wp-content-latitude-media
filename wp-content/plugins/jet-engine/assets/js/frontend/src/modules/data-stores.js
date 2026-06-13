const $ = jQuery;

let JetEngineRegisteredStores = window.JetEngineRegisteredStores || {};
let JetEngineStores           = window.JetEngineStores || {};

window.JetPlugins.hooks.addAction(
    'jet-engine.modules-include',
    'module-data-stores',
    function( JetEngine ) {
        JetEngine.addToStore = addToStore;
        JetEngine.removeFromStore = removeFromStore;
        JetEngine.initStores = initStores;
        JetEngine.initFrontStores = initFrontStores;
        JetEngine.loadFrontStoresItems = loadFrontStoresItems;
        JetEngine.dataStoreSyncListings = dataStoreSyncListings;
        JetEngine.switchDataStoreStatus = switchDataStoreStatus;

        JetEngine.dataStores = {
            queues: {},
            getQueue: function( store ) {
                if ( ! ( this.queues?.[ store ] instanceof Promise ) ) {
                    this.queues[ store ] = Promise.resolve();
                }
        
                return this.queues[ store ];
            },
            addToQueue: function( store, callback ) {
                this.queues[ store ] = this.getQueue( store ).then( callback );
            },
        }

        $( window ).on( 'jet-popup/render-content/ajax/success', JetEngine.initStores );

        $( document )
            .on( 'jet-engine/listing/ajax-get-listing/done', function( e, $html ) {
                JetEngine.initFrontStores( $html );
            } )
            .on( 'jet-engine/listing-grid/after-lazy-load', function( e, args, response, $widget ) {
                JetEngine.loadFrontStoresItems( $widget );
            } );

        JetEngine.initStores();
    }
);

window.JetPlugins.hooks.addAction(
    'jet-engine.common-events',
    'module-data-stores',
    function( $scope, JetEngine ) {
        $scope
            .on( 'click.JetEngine', '.jet-add-to-store', JetEngine.addToStore )
            .on( 'click.JetEngine', '.jet-remove-from-store', JetEngine.removeFromStore );
    }
);

function dataStoreSyncListings( args ) {
    if ( ! args.synch_id || typeof args.synch_id !== 'string' ) {
        return;
    }

    const ids = args.synch_id.split( /[\s,]+/ ).map( ( id ) => id.replace( /\s/, '' ) ).filter( ( id ) => !! id );

    ids.forEach( function ( id ) {
        let $container = $( '#' + id ),
        $elemContainer = $container.find( '> .elementor-widget-container' );

        if ( ! $container.length ) {
            return;
        }

        let $items         = $container.find( '.jet-listing-grid__items' ),
            posts          = [],
            nav            = $items.data( 'nav' ) || {},
            query          = nav.query || {},
            postID         = window.elementorFrontendConfig?.post?.id || 0;

        nav = JetEngine.ensureJSON( nav );

        // Context Bricks
        if ( $container.hasClass( 'brxe-jet-engine-listing-grid' ) ) {
            postID = window.bricksData.postId;
        }

        // Context Gutenberg
        if ( $container.hasClass( 'jet-listing-grid--blocks' )) {
            postID = JetEngineSettings.post_id;
        }

        if ( args?.store?.is_front && Object.keys( query ).length ) {
            let store = JetEngineStores[ args.store.type ];

            posts = store.getStore( args.store.slug );

            if ( ! posts.length ) {
                posts = [ 'is-front', args.store.type, args.store.slug ];
            }

            query.front_store__in = posts;
            query.is_front_store = true;
        }

        let options = {
            handler: 'get_listing',
            container: $elemContainer.length ? $elemContainer : $container,
            masonry: false,
            slider: false,
            append: false,
            query: query,
            widgetSettings: nav.widget_settings,
            postID: postID,
            elementID: $container.data( 'id' ),
        };

        JetEngine.ajaxGetListing( options, function( response ) {
            JetEngine.widgetListingGrid( $container );
        } );
    } );
}

function switchDataStoreStatus( $item, toInitial ) {

    var isDataStoreLink = $item.hasClass( 'jet-data-store-link' ),
        $label = $item.find( '.jet-listing-dynamic-link__label, .jet-data-store-link__label' ),
        $icon  = $item.find( '.jet-listing-dynamic-link__icon, .jet-data-store-link__icon' ),
        args   = $item.data( 'args' ),
        replaceLabel,
        replaceURL,
        replaceIcon;

    args = JetEngine.ensureJSON( args );

    toInitial = toInitial || false;

    if ( isDataStoreLink ) {

        switch ( args.action_after_added ) {
            case 'remove_from_store':

                if ( toInitial ) {
                    $item.addClass( 'jet-add-to-store' );
                    $item.removeClass( 'jet-remove-from-store' );

                    $item.removeClass( 'in-store' );
                } else {
                    $item.addClass( 'jet-remove-from-store' );
                    $item.removeClass( 'jet-add-to-store' );

                    $item.addClass( 'in-store' );

                }

                break;

            case 'hide':

                if ( toInitial ) {
                    $item.removeClass( 'is-hidden' );
                } else {
                    $item.addClass( 'is-hidden' );
                }

                return;
        }

    }

    if ( toInitial ) {
        replaceLabel = args.label;
        replaceIcon  = args.icon;
        replaceURL   = '#';
    } else {
        replaceLabel = args.added_label;
        replaceIcon  = args.added_icon;
        replaceURL   = args.added_url;
    }

    if ( $label.length ) {
        $label.replaceWith( replaceLabel );
    } else {
        $item.append( replaceLabel );
    }

    if ( $icon.length ) {
        $icon.replaceWith( replaceIcon );
    } else {
        $item.prepend( replaceIcon );
    }

    if ( isDataStoreLink && 'remove_from_store' === args.action_after_added ) {
        return;
    }

    $item.attr( 'href', replaceURL );

    if ( toInitial ) {
        $item.removeClass( 'in-store' );
    } else if ( ! $item.hasClass( 'in-store' ) ) {
        $item.addClass( 'in-store' );
    }


}

function initStores() {

    JetEngine.initFrontStores();

    $.each( JetEngineRegisteredStores, function( storeSlug, storeType ) {

        var store = JetEngineStores[ storeType ],
            storeData = null,
            count = 0;

        if ( ! store ) {
            return;
        }

        storeData = store.getStore( storeSlug );

        if ( storeData && storeData.length ) {
            count = storeData.length;
        }

        $( 'span.jet-engine-data-store-count[data-store="' + storeSlug + '"]' ).text( count );

    } );

    JetEngine.loadFrontStoresItems();

}

function initFrontStores( $scope ) {

    $scope = $scope || $( 'body' );

    $( '.jet-add-to-store.is-front-store', $scope ).each( function() {

        var $this = $( this ),
            args  = $this.data( 'args' ),
            store = JetEngineStores[ args.store.type ],
            count = 0;

        args = JetEngine.ensureJSON( args );
        
        if ( ! store ) {
            return;
        }

        if ( store.inStore( args.store.slug, '' + args.post_id ) ) {
            JetEngine.switchDataStoreStatus( $this );
        }

    } );

    $( '.jet-remove-from-store.is-front-store', $scope ).each( function() {

        var $this = $( this ),
            args  = $this.data( 'args' ),
            store = JetEngineStores[ args.store.type ],
            count = 0;

        args = JetEngine.ensureJSON( args );

        if ( ! store ) {
            return;
        }

        if ( ! store.inStore( args.store.slug, '' + args.post_id ) ) {
            $this.addClass( 'is-hidden' );
        } else {
            $this.removeClass( 'is-hidden' );
        }

    } );

}

function loadFrontStoresItems( $scope ) {

    $scope = $scope || $( 'body' );

    $( '.jet-listing-not-found.jet-listing-grid__items', $scope ).each( function() {

        var $this   = $( this ),
            nav     = $this.data( 'nav' ),
            isStore = $this.data( 'is-store-listing' ),
            query   = nav.query || {};

        nav = JetEngine.ensureJSON( nav );

        if ( query && query.post__in && query.post__in.length && 0 >= query.post__in.indexOf( 'is-front' ) ) {

            var storeType  = query.post__in[1],
                storeSlug  = query.post__in[2],
                store      = JetEngineStores[ storeType ],
                posts      = [],
                $container = $this.closest( '.jet-listing-grid' );

            if ( ! store ) {
                return;
            }

            //Context Gutenberg
            if ( ! $container.length ) {
                $container = $this.closest( '.jet-listing-grid--blocks' );
            }

            // Context Bricks
            if ( ! $container.length ) {
                $container = $this.closest( '.brxe-jet-engine-listing-grid' )
            }

            posts = store.getStore( storeSlug );

            if ( ! posts.length ) {
                return;
            }

            query.front_store__in = posts;
            query.is_front_store = true;

            JetEngine.ajaxGetListing( {
                handler: 'get_listing',
                container: $container,
                masonry: false,
                slider: false,
                append: false,
                query: query,
                widgetSettings: nav.widget_settings,
            }, function( response ) {
                JetEngine.widgetListingGrid( $container );
            } );

        } else if ( isStore ) {
            $( document ).trigger( 'jet-listing-grid-init-store', $this );
        }

    } );
}

function addToStore( event ) {
    event.preventDefault();
    event.stopPropagation();

    var $this = $( this ),
        args  = $this.data( 'args' );

    args = JetEngine.ensureJSON( args );

    if ( $this.hasClass( 'in-store' ) ) {
        if ( args.popup ) {
            JetEngine.triggerPopup( args.popup, args.isJetEngine, args.post_id );
        } else if ( '_blank' === $this.attr( 'target' ) ) {
            window.open( $this.attr( 'href' ) );
        } else {
            window.location = $this.attr( 'href' );
        }
        return;
    }

    if ( args.store.is_front ) {

        var store = JetEngineStores[ args.store.type ],
            count = 0;

        if ( ! store ) {
            return;
        }

        if ( store.inStore( args.store.slug, '' + args.post_id ) ) {
            var storePosts = store.getStore( args.store.slug );
            count = storePosts.length;
        } else {

            count = store.addToStore( args.store.slug, args.post_id, args.store.size );

            if ( false === count ) {
                return;
            }

        }

        if ( args.popup ) {
            JetEngine.triggerPopup( args.popup, args.isJetEngine, args.post_id );
        }

        JetEngine.switchDataStoreStatus( $this );
        $( 'span.jet-engine-data-store-count[data-store="' + args.store.slug + '"]' ).text( count );
        $( '.jet-remove-from-store[data-store="' + args.store.slug + '"][data-post="' + args.post_id + '"]' ).removeClass( 'is-hidden' );

        JetEngine.dataStoreSyncListings( args );

        $( document ).trigger( 'jet-engine-data-stores-on-add', args );

        return;
    }

    if ( $this.hasClass( 'jet-store-processing' ) ) {
        return;
    }

    $this.css( 'opacity', 0.3 );
    $this.addClass( 'jet-store-processing' );

    $( document ).trigger( 'jet-engine-on-add-to-store', [ $this, args ] );

    JetEngine.dataStores.addToQueue( args.store.slug, () => {
        return $.ajax({
            url: JetEngineSettings.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'jet_engine_add_to_store_' + args.store.slug,
                store: args.store.slug,
                post_id: args.post_id,
            },
        }).done( function( response ) {

            $this.css( 'opacity', 1 );
            $this.removeClass( 'jet-store-processing' );

            if ( response.success ) {

                JetEngine.switchDataStoreStatus( $this );
                $( '.jet-remove-from-store[data-store="' + args.store.slug + '"][data-post="' + args.post_id + '"]' ).removeClass( 'is-hidden' );

                if ( response.data.fragments ) {
                    $.each( response.data.fragments, function( selector, value ) {
                        $( selector ).html( value );
                    } );
                }

                JetEngine.dataStoreSyncListings( args );

                if ( args.popup ) {
                    JetEngine.triggerPopup( args.popup, args.isJetEngine, args.post_id );
                }

            } else {
                alert( response.data.message );
            }

            $( document ).trigger( 'jet-engine-data-stores-on-add', args );

            return response;

        } ).done( function( response ) {

            if ( response.success ) {
                $( 'span.jet-engine-data-store-count[data-store="' + args.store.slug + '"]' ).text( response.data.count );
            }

        } ).fail( function( jqXHR, textStatus, errorThrown ) {
            $this.css( 'opacity', 1 );
            $this.removeClass( 'jet-store-processing' );
            alert( errorThrown );
        } );
    } );

}

function removeFromStore( event ) {

    event.preventDefault();
    event.stopPropagation();

    var $this = $( this ),
        args  = $this.data( 'args' ),
        isDataStoreBtn = $this.hasClass( 'jet-data-store-link' );

    args = JetEngine.ensureJSON( args );

    if ( args.store.is_front ) {

        var store = JetEngineStores[ args.store.type ],
            count = 0;

        if ( ! store ) {
            return;
        }

        if ( ! store.inStore( args.store.slug, '' + args.post_id ) ) {
            var storePosts = store.getStore( args.store.slug );
            count = storePosts.length;
        } else {
            count = store.remove( args.store.slug, args.post_id );
        }

        $( '.jet-add-to-store[data-store="' + args.store.slug + '"][data-post="' + args.post_id + '"]' ).each( function() {
            JetEngine.switchDataStoreStatus( $( this ), true );
        } );

        $( '.jet-data-store-link.jet-remove-from-store[data-store="' + args.store.slug + '"][data-post="' + args.post_id + '"]' ).each( function() {
            JetEngine.switchDataStoreStatus( $( this ), true );
        } );

        $( 'span.jet-engine-data-store-count[data-store="' + args.store.slug + '"]' ).text( count );

        if ( args.remove_from_listing ) {
            $this.closest( '.jet-listing-dynamic-post-' + args.post_id ).remove();
        }

        JetEngine.dataStoreSyncListings( args );

        $( document ).trigger( 'jet-engine-data-stores-on-remove', args );

        return;

    }

    if ( $this.hasClass( 'jet-store-processing' ) ) {
        return;
    }

    $this.css( 'opacity', 0.3 );
    $this.addClass( 'jet-store-processing' );

    JetEngine.dataStores.addToQueue( args.store.slug, () => {
        return $.ajax({
            url: JetEngineSettings.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'jet_engine_remove_from_store_' + args.store.slug,
                store: args.store.slug,
                post_id: args.post_id,
            },
        }).done( function( response ) {

            $this.css( 'opacity', 1 );
            $this.removeClass( 'jet-store-processing' );

            if ( response.success ) {

                if ( ! isDataStoreBtn ) {
                    $this.addClass( 'is-hidden' );
                }

                $( '.jet-add-to-store[data-store="' + args.store.slug + '"][data-post="' + args.post_id + '"]' ).each( function() {
                    JetEngine.switchDataStoreStatus( $( this ), true );
                } );

                $( '.jet-data-store-link.jet-remove-from-store[data-store="' + args.store.slug + '"][data-post="' + args.post_id + '"]' ).each( function() {
                    JetEngine.switchDataStoreStatus( $( this ), true );
                } );

                JetEngine.dataStoreSyncListings( args );

                if ( args.remove_from_listing ) {
                    $this.closest( '.jet-listing-grid__item[data-post="' + args.post_id + '"]' ).remove();
                }

                if ( response.data.fragments ) {
                    $.each( response.data.fragments, function( selector, value ) {
                        $( selector ).html( value );
                    } );
                }

                $( document ).trigger( 'jet-engine-data-stores-on-remove', args );

            } else {
                alert( response.data.message );
            }

            return response;

        } ).done( function( response ) {

            if ( args.remove_from_listing ) {
                $this.closest( '.jet-listing-grid__item' ).remove();
            }

            if ( response.success ) {
                $( 'span.jet-engine-data-store-count[data-store="' + args.store.slug + '"]' ).text( response.data.count );
            }

        } ).fail( function( jqXHR, textStatus, errorThrown ) {
            $this.css( 'opacity', 1 );
            $this.removeClass( 'jet-store-processing' );
            alert( errorThrown );
        } );
    } );

}


