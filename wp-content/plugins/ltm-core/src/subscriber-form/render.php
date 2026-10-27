<?php
/**
 * Server-side render for latitudemedia/subscriber-form.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$title      = $attributes['title'] ?? '';
$disclaimer = $attributes['disclaimer'] ?? '';
$embed_code = $attributes['embedCode'] ?? '';
$layout     = $attributes['layout'] ?? 'square';

// ServerSideRender (used for the block editor preview) hits the block-renderer
// REST endpoint; real front-end output never goes through REST_REQUEST.
$is_editor_preview = defined( 'REST_REQUEST' ) && REST_REQUEST;

// Editor-only fallbacks shown while a field is empty; never saved or rendered
// on the front end.
$placeholder               =$block->block_type->example['attributes'] ?? [];
$is_title_placeholder      = $is_editor_preview && ! $title;
$is_disclaimer_placeholder = $is_editor_preview && ! $disclaimer;

if ( $is_title_placeholder ) {
	$title = '(' . $placeholder['title'] . '...)';
}
if ( $is_disclaimer_placeholder ) {
	$disclaimer = '(' . $placeholder['disclaimer'] . '...)';
}
if ( $is_editor_preview && ! $embed_code ) {
	$embed_code = $placeholder['embedCode'];
}
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'subscriber-form form-block layout-' . $layout ] ); ?>>
	<div class="form-block-wrapper">
		<?php if ( $title ) : ?>
			<h2 class="form-title<?php echo $is_title_placeholder ? ' is-placeholder' : ''; ?>"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $disclaimer ) : ?>
			<p<?php echo $is_disclaimer_placeholder ? ' class="is-placeholder"' : ''; ?> style="color:#fff;text-align:center;"><?php echo esc_html( $disclaimer ); ?></p>
		<?php endif; ?>

		<?php if ( $embed_code && $is_editor_preview ) : ?>
			<p class="form-embed-placeholder">
				<?php echo esc_html__( '[your form will render here on the frontend]', 'ltm' ); ?>
			</p>
		<?php else : ?>
			<?php echo $embed_code; ?>
		<?php endif; ?>
	</div>
</div>
