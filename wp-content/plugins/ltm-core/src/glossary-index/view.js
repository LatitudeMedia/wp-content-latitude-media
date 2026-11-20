/**
 * Front-end live search for the Glossary Index block.
 *
 * render.php outputs every term up front, each `li` carrying its lowercased
 * title (`data-term`), abbreviation (`data-abbr`) and synonyms
 * (`data-synonyms`, one per line). As the visitor types, items where none of
 * those contain the query are hidden ("a" keeps every term with an "a",
 * "ant" every term with "ant"). When an item matches only through a synonym,
 * its "Also known as" line is shown so the match makes sense. Letter
 * sections left empty are hidden and their jump-nav letters marked disabled.
 * No request is made.
 *
 * Input handling uses event delegation (one listener on `document`), like
 * events-list-block/view.js. Translated strings come from the block's
 * `data-text-*` attributes (see render.php for why not wp-i18n).
 */

/**
 * Shows or hides one term for the query.
 *
 * @param {HTMLElement} item  A `.ltm-glossary-index__item` element.
 * @param {string}      query Lowercased, trimmed search text.
 * @return {boolean} Whether the term matches.
 */
function filterItem( item, query ) {
	const { term, abbr, synonyms } = item.dataset;
	const byName = term.includes( query ) || abbr.includes( query );
	const bySynonym = '' !== query && synonyms.includes( query );
	const match = '' === query || byName || bySynonym;

	item.hidden = ! match;

	const aka = item.querySelector( '.ltm-glossary-index__aka' );
	if ( aka ) {
		aka.hidden = ! ( bySynonym && ! byName );
	}

	return match;
}

/**
 * Applies the query to one block instance.
 *
 * @param {HTMLElement} block The `.ltm-glossary-index` element.
 * @param {string}      raw   The search field's value.
 */
function filterGlossary( block, raw ) {
	const query = raw.trim().toLowerCase();
	let visible = 0;

	block
		.querySelectorAll( '.ltm-glossary-index__group' )
		.forEach( ( group ) => {
			let groupVisible = 0;

			group
				.querySelectorAll( '.ltm-glossary-index__item' )
				.forEach( ( item ) => {
					groupVisible += filterItem( item, query ) ? 1 : 0;
				} );

			group.hidden = 0 === groupVisible;
			visible += groupVisible;

			const navLink = block.querySelector(
				`.ltm-glossary-index__nav a[data-letter="${ group.dataset.letter }"]`
			);
			if ( navLink && groupVisible ) {
				navLink.removeAttribute( 'aria-disabled' );
			} else if ( navLink ) {
				navLink.setAttribute( 'aria-disabled', 'true' );
			}
		} );

	const empty = block.querySelector( '.ltm-glossary-index__empty' );
	empty.hidden = 0 !== visible;
	empty.textContent = block.dataset.textEmpty.replace( '%s', raw.trim() );

	const count =
		1 === visible ? block.dataset.textOne : block.dataset.textMany;
	block.querySelector( '.ltm-glossary-index__status' ).textContent =
		'' === query ? '' : count.replace( '%d', visible );
}

// view scripts load deferred, so the markup is already in the DOM.
document
	.querySelectorAll( '.ltm-glossary-index__search' )
	.forEach( ( search ) => ( search.hidden = false ) );

document.addEventListener( 'input', ( event ) => {
	if ( ! event.target.matches( '.ltm-glossary-index__search-input' ) ) {
		return;
	}

	filterGlossary(
		event.target.closest( '.ltm-glossary-index' ),
		event.target.value
	);
} );
