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
        const listHiddenEvents = $('.three-events-section[data-list-id="' + e.target.dataset.listId + '"] ul .hidden');
        const nextItems = listHiddenEvents.slice(0,3)
        if(nextItems.length) {
            nextItems.removeClass('hidden');
        }
        if(listHiddenEvents.length <= 3) {
            e.target.classList.add('hidden');
        }
    }
});
