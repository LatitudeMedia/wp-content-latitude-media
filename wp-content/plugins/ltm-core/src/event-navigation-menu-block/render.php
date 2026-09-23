<?php
/**
 * Server-side render for acf/event-navigation-menu-block.
 *
 * Fields are attached to the block itself (see
 * includes/Blocks/EventNavigationMenu.php) and stored in the block's own
 * embedded data, so bare get_field() calls resolve via ACF's active
 * block-meta context rather than a post ID.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$navigation_links = get_field( 'navigation_links' ) ?: [];
$button_one       = get_field( 'button_one' );
$button_two       = get_field( 'button_two' );
$display          = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

if ( empty( $navigation_links ) ) {
	return;
}

// The `admin` class offsets the sticky top for the WP admin bar (see style.scss).
$blockAttrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => 'content-block navigation-menu-section' . ( is_user_logged_in() ? ' admin' : '' ),
			'id'    => $block['anchor'] ?? '',
		]
	)
);
?>

<div <?php echo $blockAttrs; ?>>
	<div class="container-narrow">
		<div class="navigation-menu-wrapper">
			<div class="navigation-menu-links">
				<?php foreach ( $navigation_links as $link ) : ?>
					<a href="#<?php echo esc_attr( $link['anchor'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="buttons-container">
				<?php foreach ( [ $button_one, $button_two ] as $button ) : ?>
					<?php if ( $button ) : ?>
						<a href="<?php echo esc_url( $button['url'] ); ?>" class="nav-button" target="<?php echo esc_attr( $button['target'] ); ?>"><?php echo esc_html( $button['title'] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
