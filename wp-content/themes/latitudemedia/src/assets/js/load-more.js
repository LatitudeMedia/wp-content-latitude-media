$ = jQuery;

$(document).ready(function($) {
    let loadMoreContainer = $('.load-more-container');
    let pageLinks = $('.pager a', loadMoreContainer);
    pageLinks.on( 'click', loadMore);

    function loadMore(e) {
        e.preventDefault();
        const url = $(e.target).attr('href');

        $.get({
            url: url,
            success: (responce) => {
                const newxPageContent = $($.parseHTML(responce)).find(".load-more-posts");
                if(newxPageContent) {
                    // loadMoreContainer.replaceWith(newxPageContent);
                    history.pushState({}, "", url);
                    $('.load-more-posts', loadMoreContainer).replaceWith(newxPageContent);
                    pageLinks = $('.pager a', loadMoreContainer);
                    pageLinks.on( 'click', loadMore);
                    $('html, body').animate({
                        scrollTop: $('.load-more-posts', loadMoreContainer).offset().top  - 250
                    }, 500);
                }
            },
            error: (err) => {
                console.log(err);
            },
        });
    }
});
