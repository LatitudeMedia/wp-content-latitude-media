const $ = jQuery;

window.JetPlugins.hooks.addAction(
    'jet-engine.modules-include',
    'slider',
    function( JetEngine ) {
        JetEngine.initSlider = initSlider;
        JetEngine.maybeReinitSlider = maybeReinitSlider;
    }
);

function initSlider( $slider, customOptions ) {
    var $eWidget    = $slider.closest( '.elementor-widget' ),
        options     = $slider.data( 'slider_options' ),
        windowWidth = $( window ).width(),
        tabletBP    = 1025,
        mobileBP    = 768,
        tabletSlides, mobileSlides, defaultOptions;

    let widgetID = null;

    options = JetEngine.ensureJSON( options );

    customOptions = customOptions || {};

    options = $.extend( {}, options, customOptions );

    options.swiperDefaultSpaceBetween = 20;

    if ( $eWidget.length ) {

        var settings     = JetEngine.getElementorElementSettings( $eWidget ),
            responsive   = [],
            deviceMode   = elementorFrontend.getCurrentDeviceMode(),
            eBreakpoints = window.elementorFrontend.config.responsive.activeBreakpoints;

        options.slidesToShow = settings.columns_widescreen ? +settings.columns_widescreen : +settings.columns;
        options.slidesToShowDesktop = +settings.columns;
        options.swiperDefaultSpaceBetween = settings?.horizontal_gap?.size;

        Object.keys( eBreakpoints ).reverse().forEach( function( breakpointName ) {

            if ( settings['columns_' + breakpointName] ) {

                if ( 'widescreen' === breakpointName ) {

                    let breakpointSettings = {
                        breakpoint: eBreakpoints[breakpointName].value,
                        settings: {
                            slidesToShow: +settings['columns'],
                            swiperSlidesToShow: +settings.columns_widescreen,
                        }
                    };

                    if ( settings?.horizontal_gap_widescreen?.size ) {
                        breakpointSettings.settings.swiperSpaceBetween = settings.horizontal_gap_widescreen.size;
                    }

                    responsive.push( breakpointSettings );

                } else {
                    var breakpointSettings = {
                            breakpoint: eBreakpoints[breakpointName].value + 1,
                            settings:   {
                                slidesToShow: +settings['columns_' + breakpointName],
                            }
                        };

                    if ( options.slidesToScroll > breakpointSettings.settings.slidesToShow ) {
                        breakpointSettings.settings.slidesToScroll = breakpointSettings.settings.slidesToShow;
                    }

                    if ( settings?.['horizontal_gap_' + breakpointName]?.size ) {
                        breakpointSettings.settings.swiperSpaceBetween = settings['horizontal_gap_' + breakpointName].size;
                    }

                    responsive.push( breakpointSettings );
                }
            }

        } );

        options.responsive = responsive;

    } else {

        // Ensure we have at least some options to avoid errors
        if ( ! options.slidesToShow ) {
            options.slidesToShow = {
                desktop: 3,
                tablet: 1,
                mobile: 1,
            }
        }

        if ( options.itemsCount <= options.slidesToShow.desktop && windowWidth >= tabletBP ) { // 1025 - ...
            $slider.removeClass( 'jet-listing-grid__slider' );
            return;
        } else if ( options.itemsCount <= options.slidesToShow.tablet && tabletBP > windowWidth && windowWidth >= mobileBP ) { // 768 - 1024
            $slider.removeClass( 'jet-listing-grid__slider' );
            return;
        } else if ( options.itemsCount <= options.slidesToShow.mobile && windowWidth < mobileBP ) { // 0 - 767
            $slider.removeClass( 'jet-listing-grid__slider' );
            return;
        }

        if ( options.slidesToShow.tablet ) {
            tabletSlides = options.slidesToShow.tablet;
        } else {
            tabletSlides = 1 === options.slidesToShow.desktop ? 1 : 2;
        }

        if ( options.slidesToShow.mobile ) {
            mobileSlides = options.slidesToShow.mobile;
        } else {
            mobileSlides = 1;
        }

        options.slidesToShow = options.slidesToShow.desktop;
        options.slidesToShowDesktop = options.slidesToShow;

        options.responsive = [
            {
                breakpoint: 1025,
                settings: {
                    slidesToShow: tabletSlides,
                    slidesToScroll: options.slidesToScroll > tabletSlides ? tabletSlides : options.slidesToScroll
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: mobileSlides,
                    slidesToScroll: 1
                }
            }
        ];
    }

    // Fallback context for Bricks builder (when Elementor widget wrapper is not present)
    if ( ! $eWidget.length ) {
        $eWidget = $slider.closest( '.brxe-jet-engine-listing-grid' );
        widgetID = $eWidget.data( 'element-id' );
    }

    if ( JetEngineSettings?.sliderLibrary === 'slick' ) {
        defaultOptions = {
            customPaging: function( slider, i ) {
                return $( '<span />' ).text( i + 1 ).attr( 'role', 'tab' );
            },
            slide: '.jet-listing-grid__item',
            dotsClass: 'jet-slick-dots',
        };
        
        let slickOptions = $.extend( {}, defaultOptions, options );

        var $sliderItems = $slider.find( '> .jet-listing-grid__items' );

        if ( slickOptions.infinite ) {
            $sliderItems.on( 'init', function() {
                var $items        = $( this ),
                    $clonedSlides = $( '> .slick-list > .slick-track > .slick-cloned.jet-listing-grid__item', $items );

                if ( !$clonedSlides.length ) {
                    return;
                }

                JetEngine.initElementsHandlers( $clonedSlides );

                //Re-init Bricks scripts
                if ( widgetID ) {
                    JetEngine.reinitBricksScripts( widgetID );
                }

                if ( $slider.find('.bricks-lazy-hidden').length ) {
                    bricksLazyLoad();
                }
            } );
        }

        // Temporary solution issue with Lazy Load images + RTL on Chrome.
        // Remove after fix in Chrome.
        // See: https://github.com/Crocoblock/issues-tracker/issues/7552
        if ( slickOptions.rtl ) {
            $sliderItems.on( 'init', function() {
                var $items      = $( this ),
                    $lazyImages = $( 'img[loading=lazy]', $items ),
                    lazyImageObserver = new IntersectionObserver(
                        function( entries, observer ) {
                            entries.forEach( function( entry ) {
                                if ( entry.isIntersecting ) {
                                    // If an image does not load, need to remove the `loading` attribute.
                                    if ( ! entry.target.complete ) {
                                        entry.target.removeAttribute( 'loading' );
                                    }

                                    // Detach observer
                                    observer.unobserve( entry.target );
                                }
                            } );
                        }
                    );

                $lazyImages.each( function() {
                    const $img = $( this );
                    lazyImageObserver.observe( $img[0] );
                } );
            } );
        }

        if ( $sliderItems.hasClass( 'slick-initialized' ) ) {
            $sliderItems.slick( 'refresh', true );
            return;
        }

        if ( slickOptions.variableWidth ) {
            slickOptions.slidesToShow = 1;
            slickOptions.slidesToScroll = 1;
            slickOptions.responsive = null;
        }

        $sliderItems.on(
            'init.JetEngine',
            () => {
                $sliderItems.find( '.slick-active' ).each(
                    ( i, el ) => {
                        JetEngine.rerunElementorAnimation( $( el ) );
                    }
                );
            }
        );

        $sliderItems.on(
            'afterChange.JetEngine',
            () => {
                $sliderItems.find( '.slick-active' ).each(
                    ( i, el ) => {
                        JetEngine.rerunElementorAnimation( $( el ) );
                    }
                );
            }
        );

        slickOptions = window.JetPlugins.hooks.applyFilters(
            'jet-engine.listing-grid.slider.slick.options',
            slickOptions,
            $slider
        );

        $sliderItems.slick( slickOptions );

        if ( $sliderItems.closest( '.jet-listing-grid--lazy-load-completed' ).length ) {
            $sliderItems.slick( 'refresh', true );
        }

        $sliderItems.off( 'init.JetEngine' );
    } else {
        //swiper init
        defaultOptions = {};

        let swiperOptions  = $.extend( {}, defaultOptions, options );

        let $sliderItems = $slider.find( '> .jet-listing-grid__items' );
        let $slides = $sliderItems.find( '> .jet-listing-grid__item' );

        $slides.addClass( 'swiper-slide' );

        let swiperElement = $slider[ 0 ];
        let listingWrapper = swiperElement.closest( '.jet-listing-grid.jet-listing' );

        if ( swiperOptions.infinite ) {
            swiperOptions.loop = true;
        }

        swiperOptions.slidesPerView = swiperOptions.slidesToShow;
        swiperOptions.slidesPerGroup = swiperOptions.slidesToScroll;
        delete swiperOptions.slidesToShow;
        delete swiperOptions.slidesToShow;

        if ( swiperOptions?.responsive?.length > 0 ) {
            let breakpoints = {};

            for ( const item of swiperOptions.responsive ) {
                const width  = item.breakpoint;
                const number = item.settings.swiperSlidesToShow ? item.settings.swiperSlidesToShow : item.settings.slidesToShow;
                
                let breakpoint = {
                    slidesPerView: number,
                    //spaceBetween: item.settings.swiperSpaceBetween || -1,
                };

                // if ( +width >= 1920 && breakpoint.spaceBetween < 0 ) {
                //     breakpoint.spaceBetween = swiperOptions.swiperDefaultSpaceBetween;
                // }

                if ( swiperOptions.slidesPerGroup > number ) {
                    breakpoint.slidesPerGroup = number;
                }

                breakpoints[ width ] = breakpoint;
            }

            breakpoints[1920] = {
                slidesPerView: swiperOptions.slidesToShowDesktop,
                //spaceBetween: swiperOptions.swiperDefaultSpaceBetween,
            };

            let widthList = Object.keys( breakpoints );
            widthList = widthList.map( a=> +a ).sort( (a, b) => Math.sign( a - b ) );
            
            let swiperBreakpoints = {};

            for ( let i = 1, c = widthList.length; i < c; i++ ) {
                swiperBreakpoints[ widthList[ i - 1 ] ] = breakpoints[ widthList[ i ] ];

                // if ( swiperBreakpoints[ widthList[ i - 1 ] ]?.spaceBetween < 0 ) {
                //     let foundSpaceBetween = swiperOptions.swiperDefaultSpaceBetween;

                //     for ( let j = i + 1; j < c; j++ ) {
                //         if ( breakpoints[ widthList[ j ] ]?.spaceBetween >= 0 ) {
                //             foundSpaceBetween = breakpoints[ widthList[ j ] ]?.spaceBetween;
                //             break;
                //         }
                //     }

                //     swiperBreakpoints[ widthList[ i - 1 ] ].spaceBetween = foundSpaceBetween;
                // }
            }

            swiperOptions.slidesPerView = breakpoints[ widthList[0] ].slidesPerView;
            //swiperOptions.spaceBetween = breakpoints[ widthList[0] ].spaceBetween;

            swiperOptions.breakpoints = swiperBreakpoints;

            delete swiperOptions.responsive;
        }

        if ( swiperOptions.arrows && $slides.length > 1 ) {
            let nextArrowClass = 'swiper-button-next-svg';
            let prevArrowClass = 'swiper-button-prev-svg';
            let nextArrowElement;
            let prevArrowElement;

            let existingNextArrow = listingWrapper.querySelector( ':scope > .' + nextArrowClass );
            let existingPrevArrow = listingWrapper.querySelector( ':scope > .' + prevArrowClass );

            let arrowTemplate = document.createElement( 'template' );

            if ( existingNextArrow ) {
                nextArrowElement = existingNextArrow;
            } else {
                arrowTemplate.innerHTML = swiperOptions.nextArrow;
                nextArrowElement = arrowTemplate.content.firstChild;
                nextArrowElement.classList.add( nextArrowClass, 'swiper-arrow', nextArrowClass );

                listingWrapper.appendChild( nextArrowElement );
            }

            if ( existingPrevArrow ) {
                prevArrowElement = existingPrevArrow;
            } else {
                arrowTemplate.innerHTML = swiperOptions.prevArrow;
                prevArrowElement = arrowTemplate.content.firstChild;
                prevArrowElement.classList.add( prevArrowClass, 'swiper-arrow', prevArrowClass );

                listingWrapper.appendChild( prevArrowElement );
            }
            
            swiperOptions.navigation = {
                addIcons: false,
                nextEl: nextArrowElement,
                prevEl: prevArrowElement,
            };
        }

        if ( swiperOptions.dots ) {
            let paginationElement;
            let paginationElementClass = 'jet-engine-swiper-pagination-wrapper';
            let existingPaginationElement = listingWrapper.querySelector( ':scope > .' + paginationElementClass );

            if ( existingPaginationElement ) {
                paginationElement = existingPaginationElement;
            } else {
                let paginationTemplate = document.createElement( 'template' );
                paginationTemplate.innerHTML = `<div class="${paginationElementClass}"><div class="swiper-pagination"></div></div>`;
                paginationElement = paginationTemplate.content.firstChild;
                listingWrapper.appendChild( paginationElement );
            }

            if ( ! swiperOptions.swiperPaginationType ) {
                swiperOptions.swiperPaginationType = 'bullets';
            }

            swiperOptions.pagination = {
                el: paginationElement,
                type: swiperOptions.swiperPaginationType,
            };

            switch ( swiperOptions.swiperPaginationType ) {
                case 'bullets':
                    swiperOptions.pagination.clickable = true;
                    break;
            }
        }

        if ( swiperOptions.autoplay ) {
            swiperOptions.autoplay = {
                delay: swiperOptions.autoplaySpeed,
            }
        }

        if ( swiperOptions.fade ) {
            swiperOptions.effect = 'fade';
            swiperOptions.fadeEffect = {
                crossFade: true,
            };
        }

        if ( swiperOptions.centerMode ) {
            swiperOptions.centeredSlides = true;
        }

        if ( swiperOptions.loop ) {
            swiperOptions.loopAddBlankSlides = true;
            swiperOptions.loopFillGroupWithBlank = true;
            swiperOptions.watchOverflow = true;
        }

        swiperOptions = window.JetPlugins.hooks.applyFilters(
            'jet-engine.listing-grid.slider.swiper.options',
            swiperOptions,
            $slider
        );

        if ( $slides.length < 2 ) {
            swiperOptions.loop = false;
            swiperOptions.centeredSlides = true;
            swiperOptions.navigation = false;
        }

        const autoColumns = swiperOptions.variableWidth || false;

        if ( autoColumns ) {
            delete swiperOptions.breakpoints;
            swiperOptions.slidesPerView = 'auto';
            $slides.addClass( 'auto-columns' );
        }

        if ( swiperOptions.loop ) {
            swiperOptions.loopFillGroupWithBlank = true;
            swiperOptions.watchOverflow = true;
        }

        swiperOptions.spaceBetween = 0;

        swiperOptions.on = {
            init: function( swiper ) {
                JetEngine.initElementsHandlers( $( swiper.slides ).filter( '.swiper-slide-duplicate' ) );

                const wrapper = swiper.$wrapperEl[0];

                if ( !!swiperOptions.autoplay && swiperOptions.pauseOnHover ) {
                    wrapper.addEventListener(
                        'mouseenter', () => {
                            swiper.autoplay.stop();
                        }
                    );

                    wrapper.addEventListener(
                        'mouseleave', () => {
                            swiper.autoplay.start();
                        }
                    );
                }

                if ( widgetID ) {
                    JetEngine.reinitBricksScripts( widgetID );
                }

                if ( swiper.$wrapperEl.find('.bricks-lazy-hidden').length ) {
                    bricksLazyLoad();
                }
            },
            // activeIndexChange: function( swiper ) {
            //     //console.log(swiper.activeIndex, swiper.activeIndex, swiper.previousIndex);
            // },
            // loopFix: function( swiper ) {
            //     console.log(swiper.activeIndex, swiper.activeIndex, swiper.previousIndex);
            // },
        };

        if ( swiperElement.swiper ) {
            swiperElement.swiper.destroy();
        }

        const swiperInstance = new Swiper(
            swiperElement,
            swiperOptions
        );

        window.JetPlugins.hooks.doAction(
            'jet-engine.listing-grid.slider.swiper.after-init',
            swiperInstance
        );
    }
}

function maybeReinitSlider( event, $scope ) {
    var $slider = $scope.find( '.jet-listing-grid__slider' );

    if ( $slider.length ) {
        $slider.each( function() {
            JetEngine.initSlider( $( this ) );
        } );

    }
}
