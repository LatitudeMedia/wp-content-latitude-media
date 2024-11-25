$ = jQuery;

$(document).ready(function($) {
    const loadMoreContainer = $('.three-events-section');
    const pageLinks = $('.load-more-events', loadMoreContainer);
    const container = $('ul', loadMoreContainer);

    if(!loadMoreContainer.length
        || !pageLinks.length
        || !container.length) {
        return;
    }

    pageLinks.on( 'click', loadMoreEvents);
    function loadMoreEvents(e) {
        e.preventDefault();
        if(!appRest) {
            return;
        }
        let postBody = e.target.dataset;

        $.get({
            url: appRest.rest_endpoints.load_more_events,
            data: postBody,
            success: (wpRes) => {
                if ('content' in wpRes) {
                    let listContainer = $('.three-events-section[data-list-id="' + e.target.dataset.listId + '"] ul');
                    if(listContainer.length) {
                        listContainer.append(wpRes.content);
                    }
                }
                if ('next_page' in wpRes && wpRes.next_page) {
                    e.target.dataset.page = wpRes.next_page;
                } else {
                    e.target.style.display = 'none';
                }
            },
            error: (err) => {
                console.log(err);
            },
        });
    }
});
