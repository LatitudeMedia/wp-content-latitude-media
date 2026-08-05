<?php
/**
 * Server-side render for latitudemedia/news-type-preview.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$query = \LTMCore\Blocks\NewsTypePreview::get_query( $attributes );

if ( ! $query->have_posts() ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'ltm-news-type-preview' ] ); ?>>
	<ul class="ltm-news-type-preview__list">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			$post_id    = get_the_ID();
			$is_podcast = is_news_type( 'podcast', $post_id );

			if ( $is_podcast ) {
				$podcast = get_post_assigned_podcast( $post_id );
				$label   = $podcast ? get_the_title( $podcast ) : '';
			} else {
				$label = ltm_get_news_type( $post_id );
			}
			?>
			<li class="ltm-news-type-preview__item">
				<a class="ltm-news-type-preview__card" href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>">
					<span class="ltm-news-type-preview__thumb">
						<?php echo get_the_post_thumbnail( $post_id, 'event-sponsors-list' ); ?>
						<?php if ( $is_podcast ) : ?>
							<span class="ltm-news-type-preview__play" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="24" height="24"><polygon points="6,4 20,12 6,20" fill="currentColor" /></svg>
							</span>
						<?php endif; ?>
					</span>
					<span class="ltm-news-type-preview__content">
						<?php if ( $label ) : ?>
							<span class="ltm-news-type-preview__label"><?php echo esc_html( $label ); ?></span>
						<?php endif; ?>
						<span class="ltm-news-type-preview__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></span>
					</span>
				</a>
			</li>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
	</ul>
</div>
