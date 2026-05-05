<?php
/**
 * Calendar header template
 * @var Jet_Listing_Render_Calendar $this
 */

global $wp_locale;

$days_format = ! empty( $settings['week_days_format'] ) ? $settings['week_days_format'] : 'short';
$week_begins = (int) get_option( 'start_of_week', 0 );
?>
<caption class="jet-calendar-caption">
	<?php $this->render_calendar_navigation( $settings, $current_month ); ?>
</caption>
<thead class="jet-calendar-header">
	<tr class="jet-calendar-header__week"><?php

		for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
			$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
		}

		foreach ( $myweek as $wd ) {

			switch ( $days_format ) {
				case 'short':
					$day_name = $wp_locale->get_weekday_abbrev( $wd );
					break;

				case 'initial':
					$day_name = $wp_locale->get_weekday_initial( $wd );
					break;

				default:
					$day_name = $wd;
					break;
			}

			printf( '<th class="jet-calendar-header__week-day">%s</th>', $day_name );
		}

	?></tr>
</thead>