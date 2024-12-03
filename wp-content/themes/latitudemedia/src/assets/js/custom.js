$ = jQuery;

$(document).ready(function($) {

    let menuToggle = $("header .header-wrapper .bottom-head .toggle-container .menu-toggle");
    let hamburgerMenu = $("header .header-wrapper .bottom-head .hamburger-menu");
    let stickyHeader = $("header .header-wrapper .bottom-head");
    let stickyLogo = $("header .header-wrapper .middle-head");
    if((stickyHeader).length > 0) {
        var stickyTop = $(stickyHeader).offset().top - 43;
    }

    $(".hamburger-accordion .accordion_tab").on('click', function () {
        $('.accordion_tab').not($(this)).removeClass("active");
        $(this).toggleClass("active");
        $('.accordion_content').not($(this).next()).slideUp(250);
        $(this).next().slideToggle(250);
    });

    $(menuToggle).on('click', function (e) {
        e.preventDefault();
        $(menuToggle).toggleClass("active");
        $(hamburgerMenu).slideToggle('500','swing');
    });

    if($(stickyHeader).length > 0) {
        $(window).on('scroll', function () {
            var windowTop = $(window).scrollTop();

            if (stickyTop < windowTop) {
                $(stickyHeader).addClass('sticky');
                $(stickyLogo).addClass('sticky');
            } else {
                $(stickyHeader).removeClass('sticky');
                $(stickyLogo).removeClass('sticky');
            }
        });
    }

    if($(".js-modal-open, .podcasts-sponsorship-section .cta-button, .advertise-description-block .cta-button").length > 0) {
        $('.js-modal-open, .podcasts-sponsorship-section .cta-button, .advertise-description-block .cta-button').on('click', function(e) {
            e.preventDefault();
            const podcastCtaModal = $('.podcasts-sponsorship-section .modal-content');
            const advertiseCtaModal = $('.advertise-modal-content .modal-content');
            var fadeSpeed = 300,
                modalTarget = '#' + $(this).attr('data-target');
            $('.modal-content').append('<div class="js-modal-overlay"></div>');
            $('.js-modal-overlay').fadeIn(fadeSpeed);
            $(podcastCtaModal).fadeIn(fadeSpeed).on('click', function(e) {
                e.stopPropagation();
            });
            $(advertiseCtaModal).fadeIn(fadeSpeed).on('click', function(e) {
                e.stopPropagation();
            });
            $(modalTarget).fadeIn(fadeSpeed).on('click', function(e) {
                e.stopPropagation();
            });

            // modalResize();
            //
            // function modalResize() {
            //     var x = ( $(window).width() - $(modalTarget).outerWidth() ) / 2,
            //         y = ( $(window).height() - $(modalTarget).outerHeight() ) / 2;
            //         height = ( $(window).height() - $(modalTarget).outerHeight() );
            //     $(modalTarget).css({
            //         'top': y + 'px',
            //         'left': x + 'px',
            //         'height': height + 'px'
            //     });
            // }
            //
            // var resizeTimer;
            // $(window).on('resize', function() {
            //     clearTimeout(resizeTimer);
            //     resizeTimer = setTimeout(function() {
            //         modalResize();
            //     }, 10);
            // });

            $('.js-modal-overlay, .js-modal-close').on('click', function(e) {
                e.preventDefault();
                $(modalTarget).fadeOut(fadeSpeed);
                $(podcastCtaModal).fadeOut(fadeSpeed);
                $('.js-modal-overlay').fadeOut(fadeSpeed, function() {
                    $(this).remove();
                });
            });
        });
    }
});

