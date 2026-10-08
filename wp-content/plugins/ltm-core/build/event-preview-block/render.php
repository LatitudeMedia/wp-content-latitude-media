<?php
/**
 * Server-side render for acf/event-preview-block.
 *
 * Fields are attached to the block itself (see
 * includes/Blocks/EventPreview.php) and stored in the block's own embedded
 * data, so bare get_field() calls resolve via ACF's active block-meta
 * context rather than a post ID. $post_id is provided by ACF and refers to
 * the underlying Event post, used below to read the separate "General event
 * options" field group (still theme-registered, out of scope for this
 * migration) and the already-migrated event date helpers.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$rows                        = get_field( 'rows' ) ?: [];
$main_logo                   = get_field( 'main_logo' );
$show_event_collaborator     = get_field( 'show_event_collaborator' );
$event_collaborator_text     = get_field( 'event_collaborator_text' );
$event_collaborator_logo     = get_field( 'event_collaborator_logo' );
$event_collaborator_logo_url = get_field( 'event_collaborator_logo_url' );
$label_below                 = get_field( 'label_below' );
$subtitle                    = get_field( 'subtitle' );
$logos_title                 = get_field( 'logos_title' );
$co_hosted_logo              = get_field( 'co_hosted_logo' );
$display                     = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

$blockAttrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => 'content-block single-event-hero-section',
			'id'    => $block['anchor'] ?? '',
		]
	)
);

$date       = get_event_start_date( $post_id, 'F j, Y - h:i A' );
$endDate    = get_event__end_date( $post_id, 'F j, Y - h:i A' );
$eventData  = get_fields( $post_id );
$registerButtonTitle = 'Register';
if ( ! empty( $eventData['event_type'] ) && ( $eventData['event_type'] === 'virtual' || $eventData['event_type'] === 'webinar' ) ) {
	$registerButtonTitle = 'Watch Recording';
}
?>

<div <?php echo $blockAttrs; ?>>
	<?php
	if ( ! empty( $eventData['background_image'] ) ) {
		printf( '<img alt="img" class="bg-img" src="%s">', $eventData['background_image']['url'] );
	}
	?>
	<div class="container">
		<div class="single-event-hero-section-wrapper">
			<?php if ( ! $label_below ) : ?>
				<div class="data-row">
					<?php
					if ( in_array( 'date', $rows ) ) {
						echo '<div class="date">';
						do_action( 'print_event_date_range', $post_id );
						echo '</div>';
					}

					if ( in_array( 'type', $rows ) ) {
						$eventTypeDisplay = $eventData['event_type'] ?? '';
						if ( $eventTypeDisplay === 'frontier-forum' ) {
							$eventTypeDisplay = 'Frontier Forum';
						} elseif ( $eventTypeDisplay === 'live-podcast' ) {
							$eventTypeDisplay = 'Live Podcast';
						} else {
							$eventTypeDisplay = ucfirst( $eventTypeDisplay );
						}
						printf( '<div class="location">%s</div>', $eventTypeDisplay );
					}
					?>
				</div>
			<?php endif; ?>
			<?php

			if ( ! empty( $main_logo ) && is_array( $main_logo ) ) {
				$imageHtml = $main_logo['url'];
				printf( '<div class="main-logo-wrapper"><img alt="img" class="main-logo" src="%s"></div>', $imageHtml );
			} else {
				if ( in_array( 'title', $rows ) ) {
					printf( '<h1>%s</h1>', get_the_title( $post_id ) );
				}
			}

			if ( ! $label_below ) {
				if ( in_array( 'location', $rows ) ) {
					do_action( 'print_event_location', $post_id );
				}
			}

			if ( in_array( 'button', $rows ) && ! empty( $eventData['link'] ) ) {
				do_action( 'button_unit', [ 'title' => $registerButtonTitle, 'url' => $eventData['link'] ], null, 'strict-button green' );
			}

			if ( ! empty( $subtitle ) ) {
				printf( '<div class="subtitle">%s</div>', esc_html( $subtitle ) );
			}
			if ( ! empty( $co_hosted_logo ) && is_array( $co_hosted_logo ) ) {
				$imageHtml = '';
				foreach ( $co_hosted_logo as $logo ) {
					$imageHtml .= thumbnail_formatting( null, [ 'image_id' => $logo, 'size' => 'event-preview-hosted', 'link' => false, 'img_attr' => [ 'class' => 'logo' ] ], false );
				}
				printf( '<div class="logo-wrapper"><div class="label">%s</div>%s</div>', $logos_title ?? 'Co-hosted with:', $imageHtml );
			}

			if ( $show_event_collaborator || $label_below ) {
				echo '<div class="bottom-row ' . ( $show_event_collaborator ? 'has-collaborator' : '' ) . '">';

				if ( $show_event_collaborator ) {
					echo '<div class="collaborator-wrapper">';
					if ( ! empty( $event_collaborator_text ) ) {
						printf( '<span class="collaborator-text">%s</span>', esc_html( $event_collaborator_text ) );
					}
					if ( ! empty( $event_collaborator_logo ) && is_array( $event_collaborator_logo ) ) {
						$logo_img = sprintf( '<img alt="%s" class="collaborator-logo" src="%s">', esc_attr( $event_collaborator_logo['alt'] ?? '' ), esc_url( $event_collaborator_logo['url'] ) );
						if ( ! empty( $event_collaborator_logo_url ) ) {
							printf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url( $event_collaborator_logo_url ), $logo_img );
						} else {
							echo $logo_img;
						}
					}
					echo '</div>';
				}

				if ( $label_below ) {
					ob_start();
					do_action( 'print_event_date_range', $post_id );
					$date_output     = ob_get_clean();
					$location_label  = isset( $eventData['location'] ) ? $eventData['location'] : '';
					printf(
						'<div class="labels-wrapper"><div class="location label-text">%s</div><div class="date label-text">%s</div></div>',
						esc_html( $location_label ),
						$date_output
					);
				}

				echo '</div>';
			}
			?>
		</div>
	</div>
</div>
