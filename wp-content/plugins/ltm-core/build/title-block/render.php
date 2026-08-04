<?php
/**
 * Server-side render for latitudemedia/title-block.
 *
 * $content is the already-rendered markup of the inner core/post-title block.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$post_id = get_the_ID();
$sponsor = get_post_sponsor( $post_id );

$kicker              = $attributes['kicker'] ?? '';
$background_image_id = $attributes['backgroundImageId'] ?? 0;
$logo_override_id    = $attributes['sponsorLogoOverrideId'] ?? 0;

$style = '';
if ( $background_image_id ) {
	$bg_url = wp_get_attachment_image_url( $background_image_id, 'full' );
	if ( $bg_url ) {
		$style = sprintf( ' style="--ltm-title-block-bg: url(%s);"', esc_url( $bg_url ) );
	}
}
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'ltm-title-block' ] ); ?><?php echo $style; ?>>
	<div class="ltm-title-block__inner">
		<div class="ltm-title-block__content">
			<?php if ( $kicker ) : ?>
				<p class="ltm-title-block__kicker"><?php echo esc_html( $kicker ); ?></p>
			<?php endif; ?>
			<?php echo $content; ?>
		</div>
		<?php if ( $sponsor ) : ?>
			<div class="ltm-title-block__sponsor">
				<span class="ltm-title-block__sponsor-label"><?php esc_html_e( 'Presented By', 'ltm' ); ?></span>
				<?php
				if ( $logo_override_id ) {
					echo wp_get_attachment_image( $logo_override_id, 'medium', false, [ 'class' => 'ltm-title-block__sponsor-logo' ] );
				} elseif ( has_post_thumbnail( $sponsor->ID ) ) {
					echo get_the_post_thumbnail( $sponsor->ID, 'medium', [ 'class' => 'ltm-title-block__sponsor-logo' ] );
				}
				?>
			</div>
		<?php endif; ?>
	</div>
</div>
