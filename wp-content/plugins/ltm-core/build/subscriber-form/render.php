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
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'subscriber-form form-block layout-' . $layout ] ); ?>>
	<div class="form-block-wrapper">
		<?php if ( $title ) : ?>
			<h2 class="form-title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $disclaimer ) : ?>
			<p style="color:#fff;text-align:center;"><?php echo esc_html( $disclaimer ); ?></p>
		<?php endif; ?>
	
			<?php echo $embed_code; ?>
	</div>
</div>
