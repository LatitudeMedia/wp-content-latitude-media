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
                const newxPageContent = $($.parseHTML(responce)).find(".posts-list-section");
                if(newxPageContent) {
                    // loadMoreContainer.replaceWith(newxPageContent);
                    history.pushState({}, "", url);
                    $('.posts-list-section', loadMoreContainer).replaceWith(newxPageContent);
                    pageLinks = $('.pager a', loadMoreContainer);
                    pageLinks.on( 'click', loadMore);
                }
            },
            error: (err) => {
                console.log(err);
            },
        });
    }
});
