const $ = jQuery;

window.JetPlugins.hooks.addAction(
    'jet-engine.modules-include',
    'module-calendar',
    function( JetEngine ) {
        JetEngine.activeCalendarDay = null;
        JetEngine.currentMonth = null;
        JetEngine.currentRequest = {};

        JetEngine.calendarCache = calendarCache;
        JetEngine.selectCalendarMonth = selectCalendarMonth;
        JetEngine.switchCalendarMonth = switchCalendarMonth;
        JetEngine.showCalendarEvent = showCalendarEvent;
        JetEngine.openCalendarEvent = openCalendarEvent;
        JetEngine.closeCalendarEvents = closeCalendarEvents;
        JetEngine.updateDateSelectLabels = updateDateSelectLabels;
    }
);

window.JetPlugins.hooks.addAction(
    'jet-engine.common-events',
    'module-calendar',
    function( $scope, JetEngine ) {
        $scope
            .on( 'jet-filter-content-rendered', JetEngine.calendarCache.clear )
            .on( 'change.JetEngine', '.jet-calendar-caption__date-select', JetEngine.selectCalendarMonth )
            .on( 'click.JetEngine', '.jet-calendar-nav__link', JetEngine.switchCalendarMonth )
            .on( 'click.JetEngine', '.jet-calendar-week__day-mobile-overlay', JetEngine.showCalendarEvent )
            .on( 'click.JetEngine', '.jet-md-calendar__event', JetEngine.openCalendarEvent )
            .on( 'click.JetEngine', '.jet-md-calendar__event-overlay, .jet-md-calendar__event-close', JetEngine.closeCalendarEvents )
    }
);

function updateDateSelectLabels( wrapper ) {
    let month = wrapper.querySelector( '.jet-calendar-caption__date-select.select-month' ),
        year = wrapper.querySelector( '.jet-calendar-caption__date-select.select-year' );

    if ( ! month || ! year ) {
        return false;
    }

    let monthLabel = wrapper.querySelector( '.jet-calendar-caption__date-select-label.select-month' ),
        yearLabel = wrapper.querySelector( '.jet-calendar-caption__date-select-label.select-year' );

    wrapper.setAttribute( 'data-month', month.value + ' ' + year.value );

    const monthOption = month.querySelector( `option[value="${month.value}"]` ),
          yearOption = year.querySelector( `option[value="${year.value}"]` );

    monthLabel.innerHTML = monthOption.innerHTML;
    yearLabel.innerHTML = yearOption.innerHTML;

    return true;
}

function openCalendarEvent( event ) {
    event.preventDefault();
    event.stopPropagation();

    var $event = $( event.currentTarget );
    var eventId = $event.data( 'object-id' );

    var $eventContent = $event.closest( '.jet-calendar' ).find( `.jet-md-calendar__event-content[data-object-id="${eventId}"]` );

    if ( ! $eventContent.length ) {
        return;
    }

    JetEngine.closeCalendarEvents();

    $eventContent.addClass( 'is-active' );
}

function closeCalendarEvents( event ) {

    if ( event ) {
        event.preventDefault();
        event.stopPropagation();
    }

    $( '.jet-md-calendar__event-content' ).removeClass( 'is-active' );
}

function showCalendarEvent( event ) {

    var $this       = $( this ),
        $day        = $this.closest( '.jet-calendar-week__day' ),
        $week       = $day.closest( '.jet-calendar-week' ),
        $events     = $day.find( '.jet-calendar-week__day-content' ),
        activeClass = 'calendar-event-active';

    if ( $day.hasClass( activeClass ) ) {
        $day.removeClass( activeClass );
        JetEngine.activeCalendarDay.remove();
        JetEngine.activeCalendarDay = null;
        return;
    }

    if ( JetEngine.activeCalendarDay ) {
        JetEngine.activeCalendarDay.remove();
        $( '.' + activeClass ).removeClass( activeClass );
        JetEngine.activeCalendarDay = null;
    }

    $day.addClass( 'calendar-event-active' );

    JetEngine.activeCalendarDay = $( '<tr class="jet-calendar-week"><td colspan="7" class="jet-calendar-week__day jet-calendar-week__day-mobile"><div class="jet-calendar-week__day-mobile-event">' + $events.html() + '</div></td></tr>' );

    // Need for re-init popup events
    JetEngine.activeCalendarDay.find( '.jet-popup-attach-event-inited' ).removeClass( 'jet-popup-attach-event-inited' );
    JetEngine.initElementsHandlers( JetEngine.activeCalendarDay );

    JetEngine.activeCalendarDay.insertAfter( $week );

}

function switchCalendarMonth( $event ) {

    var $this   = $( this ),
        $calendar = $this.closest( '.jet-calendar' ),
        $widget   = $this.closest( '.elementor-widget' ),
        widgetID  = $widget.closest( '.elementor-widget' ).data( 'id' ),
        settings  = $calendar.data( 'settings' ),
        post      = $calendar.data( 'post' ),
        month     = $this.data( 'month' );

    settings = JetEngine.ensureJSON( settings );

    settings['renderer'] = $calendar.data( 'renderer' ) || '';

    const isMultiday = $calendar.hasClass( 'jet-md-calendar' );
    const blockSelector = isMultiday ? '.jet-multiday-listing-calendar-block' : '.jet-listing-calendar-block';
    const bricksSelector = isMultiday ? '.brxe-jet-listing-multiday-calendar' : '.brxe-jet-listing-calendar';

    if ( this.classList.contains( 'nav-link-prev' ) ) {
        settings['__switch_direction'] = -1;
    } else if ( this.classList.contains( 'nav-link-next' ) ) {
        settings['__switch_direction'] = 1;
    } else {
        settings['__switch_direction'] = 0;
    }

    let widgetType = 'elementor';

    // Context Gutenberg
    if ( ! $widget.length ) {
        $widget = $calendar.closest( blockSelector );
        widgetType = 'block';
    }

    // Context Bricks
    if ( ! $widget.length ) {
        $widget = $calendar.closest( bricksSelector );
        widgetID = $widget.data( 'element-id' );
        widgetType = 'bricks';
    }

    JetEngine.calendarCache.modifyJetSmartFiltersSetiings( $widget, widgetType, month );

    const cacheId = $calendar.data( 'cache-id' ) || false,
          cacheTimeout = ( settings['cache_timeout'] ?? 0 ) * 1000;

    if ( cacheId && cacheTimeout ) {

        JetEngine.calendarCache.deleteExpiredEntries( cacheId, cacheTimeout );

        // Remove the 'listening' and 'brx-open' classes from all matched elements to prevent
        // reinitialization issues in the accordion.
        if ( window.bricksIsFrontend ) {
            $calendar.find('.accordion-item.listening, .brxe-accordion-nested > .listening')
                .removeClass('listening brx-open');
        }

        JetEngine.calendarCache.update( cacheId, settings['prev_month'], $calendar.prop('outerHTML'), settings );

        const cached = JetEngine.calendarCache.get( cacheId, month );

        if ( cached?.length && cached[0] && ! JetEngine.calendarCache.isExpired( cacheId, month, cacheTimeout ) ) {
            let replacement = $( cached[0] );
            replacement.removeClass( 'jet-calendar-loading' );
            $calendar.replaceWith( replacement[0] );
            JetEngine.initElementsHandlers( $widget );
            JetEngine.updateDateSelectLabels( $widget[0] );
            // Re-init Bricks scripts
            JetEngine.reinitBricksScripts( widgetID );

            $( document ).trigger( 'jet-engine-request-calendar-cached', [ $widget ] );

            return;
        }
    }

    $calendar.addClass( 'jet-calendar-loading' );

    JetEngine.currentRequest = {
        jet_engine_action: 'jet_engine_calendar_get_month',
        month: month,
        settings: settings,
        post: post,
    };

    $( document ).trigger( 'jet-engine-request-calendar' );

    $.ajax({
        url: JetEngineSettings.ajaxlisting,
        type: 'POST',
        dataType: 'json',
        data: JetEngine.currentRequest,
    }).done( function( response ) {
        if ( response.success ) {
            $calendar.replaceWith( response.data.content );

            if ( cacheId && cacheTimeout ) {
                JetEngine.calendarCache.set( cacheId, month, response.data.content, settings );
            }

            JetEngine.initElementsHandlers( $widget );
            // Re-init Bricks scripts
            JetEngine.reinitBricksScripts( widgetID );

            $( document ).trigger( 'jet-engine-request-calendar-done', [ $widget ] );
        }
        $calendar.removeClass( 'jet-calendar-loading' );
    } );
}

function selectCalendarMonth( $event ) {
    let wrapper = this.closest( '.jet-calendar-caption__dates' );

    if ( ! JetEngine.updateDateSelectLabels( wrapper ) ) {
        return;
    }

    JetEngine.switchCalendarMonth.bind( wrapper )()
}

const calendarCache = {

    entries: {},

    //introduced because Firefox does not have forEach method for iterators
    iterate: function( iterator, callback ) {
        if ( typeof iterator?.forEach === 'function' ) {
            iterator.forEach( callback );
        } else if ( typeof iterator?.next === 'function' ) {
            let next;
            while ( next = iterator.next(), ! next.done ) {
                callback.call( this, next.value );
            }
        }
    },

    get: function ( cacheId, month ) {
        return JetEngine.calendarCache.entries[ cacheId ]?.get( month ) || false;
    },

    set: function ( cacheId, month, content, settings = {}, timestamp = false ) {
        if ( ! JetEngine.calendarCache.entries[ cacheId ] ) {
            JetEngine.calendarCache.entries[ cacheId ] = new Map();
        }

        if ( ! JetEngine.calendarCache.entries[ cacheId ].has( month )
            && JetEngine.calendarCache.entries[ cacheId ].size > ( settings['max_cache'] ?? 12 ) - 1
        ) {
            let deletedKey;

            const mapKeys = JetEngine.calendarCache.entries[ cacheId ].keys();

            if ( settings['__switch_direction'] < 0 ) {
                let maxDate = false;

                JetEngine.calendarCache.iterate(
                    mapKeys,
                    function ( key ) {
                        const parsedDate = Date.parse( key );

                        if ( ! maxDate || parsedDate > maxDate ) {
                            maxDate = parsedDate;
                            deletedKey = key;
                        }
                    }
                );
            } else {
                let minDate = false;

                JetEngine.calendarCache.iterate(
                    mapKeys,
                    function ( key ) {
                        const parsedDate = Date.parse( key );

                        if ( ! minDate || parsedDate < minDate ) {
                            minDate = parsedDate;
                            deletedKey = key;
                        }
                    }
                );
            }

            JetEngine.calendarCache.entries[ cacheId ].delete( deletedKey );
        }

        if ( ! timestamp ) {
            timestamp = Date.now();
        }

        JetEngine.calendarCache.entries[ cacheId ].set( month, [ content, timestamp ] );
    },

    update: function ( cacheId, month, content, settings = {} ) {
        let cached = JetEngine.calendarCache.get( cacheId, month );
        JetEngine.calendarCache.set( cacheId, month, content, settings, cached[1] ?? false );
    },

    deleteExpiredEntries: function ( cacheId, cacheTimeout ) {
        //delete possible orphaned caches
        for ( const cacheId in JetEngine.calendarCache.entries ) {
            if ( ! document.querySelector( `.jet-calendar[data-cache-id="${cacheId}"]` ) ) {
                delete JetEngine.calendarCache.entries[ cacheId ];
            }
        }

        if ( ! JetEngine.calendarCache.entries[ cacheId ] ) {
            return;
        }

        JetEngine.calendarCache.iterate(
            JetEngine.calendarCache.entries[ cacheId ].keys(),
            function ( month ) {
                if ( JetEngine.calendarCache.isExpired( cacheId, month, cacheTimeout ) ) {
                    JetEngine.calendarCache.entries[ cacheId ].delete( month );
                }
            }
        );
    },

    isExpired: function ( cacheId, month, cacheTimeout ) {
        if ( cacheTimeout < 0 ) {
            return false;
        }

        const cached = JetEngine.calendarCache.get( cacheId, month );

        if ( ! cached || ! Array.isArray( cached ) ) {
            return true;
        }

        return ! cached[1] || cached[1] < Date.now() - cacheTimeout;
    },

    clear: function( e, $calendar ) {
        const cacheId = $calendar.data( 'cache-id' ) || false;

        if ( ! cacheId ) {
            return;
        }

        JetEngine.calendarCache.entries[ cacheId ] = new Map();
    },

    modifyJetSmartFiltersSetiings: function( $widget, widgetType, monthData ) {
        if ( ! window.JetSmartFilterSettings || ! window.JetSmartFilterSettings.settings ) {
            return;
        }

        if ( ! window.JetSmartFilterSettings.settings['jet-engine-calendar'] ) {
            return;
        }

        monthData = monthData.split( ' ' );

        const month = monthData[0],
              year = monthData[1];

        let widgetId;

        switch ( widgetType ) {
            case 'block':
                widgetId = $widget.closest( '.jet-listing-calendar-block' )[0].id;

                if ( ! widgetId ) {
                    widgetId = 'default';
                }

                if ( window.JetSmartFilterSettings.settings['jet-engine-calendar'][ widgetId ] ) {
                    window.JetSmartFilterSettings.settings['jet-engine-calendar'][ widgetId ]['start_from_month'] = month;
                    window.JetSmartFilterSettings.settings['jet-engine-calendar'][ widgetId ]['start_from_year'] = year;
                }

                break;
            case 'bricks':
                widgetId = $widget.data( 'element-id' );

                if ( ! widgetId ) {
                    break;
                }

                for ( const id in window.JetSmartFilterSettings.settings['jet-engine-calendar'] ) {
                    if ( window.JetSmartFilterSettings.settings['jet-engine-calendar'][ id ]?._id === widgetId ) {
                        window.JetSmartFilterSettings.settings['jet-engine-calendar'][ id ]['start_from_month'] = month;
                        window.JetSmartFilterSettings.settings['jet-engine-calendar'][ id ]['start_from_year'] = year;
                        break;
                    }
                }

                break;
            case 'elementor':
                widgetId = $widget.closest( '.elementor-widget-jet-listing-calendar' )[0].id;

                if ( ! widgetId ) {
                    widgetId = 'default';
                }

                if ( window.JetSmartFilterSettings.settings['jet-engine-calendar']?.[ widgetId ] ) {
                    window.JetSmartFilterSettings.settings['jet-engine-calendar'][ widgetId ]['start_from_month'] = month;
                    window.JetSmartFilterSettings.settings['jet-engine-calendar'][ widgetId ]['start_from_year'] = year;
                }

                break;
        }
    },
}
