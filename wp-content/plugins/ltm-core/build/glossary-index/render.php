<?php
/**
 * Server-side render for latitudemedia/glossary-index.
 *
 * Lists every published glossary term, grouped A–Z, with a jump nav. The
 * search field is rendered `hidden` and revealed by view.js, which filters
 * items by their `data-term` (the lowercased raw title) as the visitor types.
 * Data comes from includes/Blocks/GlossaryIndex.php.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

use LTMCore\Blocks\GlossaryIndex;

$groups = GlossaryIndex::get_grouped_terms();

if ( ! $groups ) {
	return;
}

$search_id = wp_unique_id( 'ltm-glossary-search-' );
$letters   = array_merge( isset( $groups['#'] ) ? [ '#' ] : [], range( 'A', 'Z' ) );
$anchor    = static fn( $letter ) => 'glossary-' . ( '#' === $letter ? 'other' : strtolower( $letter ) );
?>
<?php
// Strings for view.js. Passed as data attributes rather than via wp-i18n: the
// theme's add_script_defer_attribute() defers every script, which breaks
// wp-i18n's inline setup script ("wp is not defined").
$wrapper_attributes = get_block_wrapper_attributes(
	[
		'class'             => 'ltm-glossary-index',
		/* translators: %s: the visitor's search text. */
		'data-text-empty'   => __( 'No terms match “%s”.', 'ltm' ),
		/* translators: %d: number of matching glossary terms (always 1). */
		'data-text-one'     => __( '%d term found', 'ltm' ),
		/* translators: %d: number of matching glossary terms. */
		'data-text-many'    => __( '%d terms found', 'ltm' ),
	]
);
?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="ltm-glossary-index__search" hidden>
		<label class="ltm-glossary-index__search-label" for="<?php echo esc_attr( $search_id ); ?>">
			<?php esc_html_e( 'Search the glossary', 'ltm' ); ?>
		</label>
		<input
			type="search"
			id="<?php echo esc_attr( $search_id ); ?>"
			class="ltm-glossary-index__search-input"
			placeholder="<?php esc_attr_e( 'Start typing a term…', 'ltm' ); ?>"
			autocomplete="off"
			spellcheck="false"
		/>
		<p class="ltm-glossary-index__status" aria-live="polite"></p>
	</div>

	<nav class="ltm-glossary-index__nav" aria-label="<?php esc_attr_e( 'Glossary letters', 'ltm' ); ?>">
		<ul>
			<?php foreach ( $letters as $letter ) : ?>
				<li>
					<?php if ( isset( $groups[ $letter ] ) ) : ?>
						<a href="#<?php echo esc_attr( $anchor( $letter ) ); ?>" data-letter="<?php echo esc_attr( $letter ); ?>"><?php echo esc_html( $letter ); ?></a>
					<?php else : ?>
						<span aria-disabled="true"><?php echo esc_html( $letter ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<p class="ltm-glossary-index__empty" hidden></p>

	<?php foreach ( $groups as $letter => $terms ) : ?>
		<section class="ltm-glossary-index__group" id="<?php echo esc_attr( $anchor( $letter ) ); ?>" data-letter="<?php echo esc_attr( $letter ); ?>">
			<h2 class="ltm-glossary-index__letter"><?php echo esc_html( $letter ); ?></h2>
			<ul class="ltm-glossary-index__list">
				<?php foreach ( $terms as $term ) : ?>
					<?php $summary = get_the_excerpt( $term ); ?>
					<li class="ltm-glossary-index__item" data-term="<?php echo esc_attr( mb_strtolower( wp_strip_all_tags( $term->post_title ) ) ); ?>">
						<a class="ltm-glossary-index__term" href="<?php echo esc_url( get_permalink( $term ) ); ?>"><?php echo esc_html( get_the_title( $term ) ); ?></a>
						<?php if ( $summary ) : ?>
							<p class="ltm-glossary-index__summary"><?php echo esc_html( $summary ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endforeach; ?>
</div>
