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

    // Open modal on click corresponding button
    // Requirements:
    // button class = js-modal-open
    // button link should have hash to corresponding popup id
    // popup should have corresponding id.
    if($(".js-modal-open").length > 0) {
        $('.js-modal-open').on('click', function(e) {
            e.preventDefault();
            const modalTarget = $(this).attr('href');
            const fadeSpeed = 300;
            const modalContent = $(`${modalTarget}.modal-content`);
            if( !modalTarget || modalContent.length < 1) {
                return;
            }
            modalContent.append('<div class="js-modal-overlay"></div>');
            $('.js-modal-overlay').fadeIn(fadeSpeed);
            $(modalTarget).fadeIn(fadeSpeed).on('click', function(e) {
                e.stopPropagation();
            });

            $('.js-modal-overlay, .js-modal-close').on('click', function(e) {
                e.preventDefault();
                $(modalTarget).fadeOut(fadeSpeed);
                $('.js-modal-overlay').fadeOut(fadeSpeed, function() {
                    $(this).remove();
                });
            });
        });
    }

    // Copy post link in share buttons
    const copyArticleLink = $(".social-share-link");
    if(copyArticleLink.length > 0) {
        copyArticleLink.on("click", function(e) {
            e.preventDefault();
            const textarea = document.createElement("textarea");
            textarea.value = e.currentTarget.getAttribute('href');
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
            copyArticleLink.hide(1).delay(1000).show(1);
            $('.copied-to-clipboard').show(1).delay(1000).hide(1);
        })
    }

    //Mobile menu click on parent item prevent
    const mobileMenuParentItems = $('.accordion-menu .menu-main-menu-container .menu-item-has-children');
    if(mobileMenuParentItems.length > 0) {
        mobileMenuParentItems.on('click', function(e) {
            e.preventDefault();
        })
    }

    // cta-button
    // strict-button
    const ctaButtonsWithAnchors = $('.cta-button:not(.js-modal-close,.js-modal-open), .strict-button:not(.js-modal-close,.js-modal-open), .learn-more');
    if(ctaButtonsWithAnchors.length > 0) {
        ctaButtonsWithAnchors.on("click", function(e) {
            const anchor = e.target.getAttribute('href');
            if( !anchor.startsWith('#') || anchor.length === 1 ) {
                return;
            }

            $("html, body").animate({
                scrollTop: $(anchor).offset().top
            }, 1000);
            return false;
        });
    }
});

(function () {
    const hbsptScript = document.getElementById('hbspt-script');
    if(hbsptScript) {
        hbsptScript.addEventListener('load', function () {
            if (window.hbspt && window.hbspt.forms && typeof window.hbspt.forms.create === 'function') {
                window.dispatchEvent(new CustomEvent('hbspt-ready'));
            }
            else {
                console.error('hbspt object is not available.');
            }
        });
    }
})()

