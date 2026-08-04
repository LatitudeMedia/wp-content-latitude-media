<?php
/**
 * Server-side render for latitudemedia/category-post-listing.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$query = \LTMCore\Blocks\CategoryPostListing::get_query( $attributes );

if ( ! $query->have_posts() ) {
	return;
}

$layout = $attributes['layout'] ?? 'five-post-feature';
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'ltm-category-post-listing' ] ); ?>>
	<?php if ( $layout === 'five-post-feature' ) : ?>
		<?php
		$main_template = get_wrap_rows_from_template(
			'<div class="image-folder">[thumb]</div>
			<div class="content-folder">
				[tags-list]
				[title]
				[excerpt]
				<div class="info">
					[author]<span></span>[date]
				</div>
			</div>'
		);

		$secondary_template = get_wrap_rows_from_template(
			'<li>
				<div class="image-folder">[thumb]</div>
				<div class="content-folder">
					[title]
				</div>
			</li>'
		);

		$position = 0;
		?>
		<div class="feature">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();
				$position++;

				if ( 1 === $position ) {
					?>
					<div class="feature-main">
						<?php
						get_template_part(
							'template-parts/components/post',
							'item',
							[
								'post_id'  => get_the_ID(),
								'rows'     => $main_template['rows'],
								'wrap'     => $main_template['wrap'],
								'settings' => [
									'thumb'  => [
										'size' => 'news-with-hero',
										'link' => true,
									],
									'author' => [
										'link_class' => 'author',
									],
									'date'   => [
										'format' => 'M j, Y',
									],
								],
							]
						);
						?>
					</div>
					<ul class="feature-grid">
					<?php
				} else {
					get_template_part(
						'template-parts/components/post',
						'item',
						[
							'post_id'  => get_the_ID(),
							'rows'     => $secondary_template['rows'],
							'wrap'     => $secondary_template['wrap'],
							'settings' => [
								'thumb' => [
									'size' => 'news-with-hero',
									'link' => true,
								],
							],
						]
					);
				}
			}
			wp_reset_postdata();

			if ( $position > 1 ) {
				echo '</ul>';
			}
			?>
		</div>
	<?php else : ?>
		<?php
		$template = get_wrap_rows_from_template(
			'<li>
				<div class="image-folder">[thumb]</div>
				<div class="content-folder">
					[tags-list]
					[title]
					[excerpt]
					<div class="info">
						[author]<span></span>[date]
					</div>
				</div>
			</li>'
		);
		?>
		<ul class="posts">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();

				get_template_part(
					'template-parts/components/post',
					'item',
					[
						'post_id'  => get_the_ID(),
						'rows'     => $template['rows'],
						'wrap'     => $template['wrap'],
						'settings' => [
							'thumb'  => [
								'size' => 'posts-list-small',
								'link' => true,
							],
							'author' => [
								'link_class' => 'author',
							],
							'date'   => [
								'format' => 'M j, Y',
							],
						],
					]
				);
			}
			wp_reset_postdata();
			?>
		</ul>
		<?php do_action( 'paginator', $query, true, 'cpl-page' ); ?>
	<?php endif; ?>
</div>
