<?php
/**
 * Server-side render for latitudemedia/featured-post-block.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$post_id = absint( $attributes['postId'] ?? 0 );

if ( ! $post_id || get_post_status( $post_id ) !== 'publish' ) {
	return;
}

\LatitudeMedia\Page_Data()->addItems( [ $post_id ] );

$template = get_wrap_rows_from_template(
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
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'ltm-featured-post-block' ] ); ?>>
	<?php
	get_template_part(
		'template-parts/components/post',
		'item',
		[
			'post_id'  => $post_id,
			'rows'     => $template['rows'],
			'wrap'     => $template['wrap'],
			'settings' => [
				'thumb'  => [
					'size' => 'list-three-events',
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
