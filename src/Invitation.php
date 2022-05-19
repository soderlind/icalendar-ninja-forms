<?php
/**
 * iCalendar for Ninja Forms: Invitation. @codingStandardsIgnoreLine.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\iCalendar;

/**
 * Create invitation card (VCALENDAR).
 */
class Invitation {

	/**
	 * Parse query variables and return iCalendar file if match is met.
	 *
	 * @param \WP $wp Current WordPress environment instance (passed by reference).
	 *
	 * @return void
	 */
	public function card( \WP $wp ) {
		if ( isset( $wp->query_vars[ Permalink::$query_var ] ) ) {
			require_once \plugin_dir_path( ICALENDAR_FILE ) . 'include/icalendar/zapcallib.php';

			$form_uid  = $wp->query_vars[ Permalink::$query_var ];
			$form_uids = array_flip( get_option( 'ical_link_form_id', [] ) );

			if ( ! isset( $form_uids[ $form_uid ] ) ) {
				// Cheating, are we?
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				nocache_headers();
				get_template_part( 404 );

				die();
			} else {

				$form_id = $form_uids[ $form_uid ];
				$data    = get_option( 'ical_form_' . $form_id );
				$title   = ( ! empty( $data['icalendar_title'] ) ) ? wp_strip_all_tags( $data['icalendar_title'] ) : __( 'Event invitation', 'icalendar-ninja-forms' );

				// if ( isset( $data['icalendar_time_start'] ) ) {
				// 	_doing_it_wrong(
				// 		'icalendar time fields',
				// 		esc_html__( 'The ninja forms icalendar setting time fields are deprecated. Please use upgrade the plugin to version 2.0.0 or later', 'icalendar-ninja-forms' ),
				// 		'2.0.0'
				// 	);
				// 	$event_start = wp_date( sprintf( ' % s % s', $data['icalendar_date'], $data['icalendar_time_start'] ) );
				// 	$event_end   = wp_date( sprintf( ' % s % s', $data['icalendar_date'], $data['icalendar_time_end'] ) );
				// } else {
					$event_start = $data['icalendar_date'];
					$event_end   = $data['icalendar_end_date'];
				// };
				$message = '';
				if ( isset( $data['icalendar_add_message'], $data['icalendar_message'] ) && '1' === $data['icalendar_add_message'] ) {
					$message .= wp_strip_all_tags( $data['icalendar_message'] );
				}
				if ( isset( $data['icalendar_append_url'], $data['icalendar_post_id'] ) && '1' === $data['icalendar_append_url'] ) {
					$url       = get_permalink( $data['icalendar_post_id'] );
					$link_text = ( ! empty( $data['icalendar_append_url_link_text'] ) ) ? wp_strip_all_tags( $data['icalendar_append_url_link_text'] ) : __( 'More information', 'icalendar-ninja-forms' );
					$message  .= sprintf( "\n%s %s", $link_text, $url );
				}

				$icalobj  = new \ZCiCal();
				$eventobj = new \ZCiCalNode( 'VEVENT', $icalobj->curnode );
				$eventobj->addNode( new \ZCiCalDataNode( 'SUMMARY:' . $title ) );

				$eventobj->addNode( new \ZCiCalDataNode( 'DTSTART:' . \ZCiCal::fromSqlDateTime( $event_start ) ) );
				$eventobj->addNode( new \ZCiCalDataNode( 'DTEND:' . \ZCiCal::fromSqlDateTime( $event_end ) ) );
				$eventobj->addNode( new \ZCiCalDataNode( 'ORGANIZER:' . $data['icalendar_organizer'] ) );

				$uid = 'icalendar-ninja-forms-' . $data['icalendar_uid'];
				$eventobj->addNode( new \ZCiCalDataNode( 'UID:' . $uid ) );

				// DTSTAMP is a required item in VEVENT.
				$utc_now = wp_date( 'Y-m-d H:i:s', time(), new \DateTimeZone( 'UTC' ) );
				$eventobj->addNode( new \ZCiCalDataNode( 'DTSTAMP:' . \ZCiCal::fromSqlDateTime( $utc_now ) ) );

				if ( '' !== $message ) {
					$eventobj->addNode( new \ZCiCalDataNode( 'DESCRIPTION:' . \ZCiCal::formatContent( $message ) ) );
				}

				header( 'Content-type: text/calendar; charset=utf-8' );
				header( sprintf( 'Content-Disposition:inline; filename=event-%s.ics', $data['icalendar_uid'] ) );

				echo esc_html( $icalobj->export() );
				exit();
			}
		}
	}

	/**
	 * Check if date is valid.
	 *
	 * @param [type] $date Date to be validated.
	 * @param string $format Date format.
	 * @link https://www.php.net/manual/en/function.checkdate.php#126477
	 *
	 * @return bool
	 */
	private function is_valid_date( $date, $format = 'Y-m-d H:i:s' ) {
		// replace a 'Z' at the end by ' + 00:00'.
		$date = preg_replace( '/(.*)Z$/', '${1}+00:00', $date );

		$d = \DateTime::createFromFormat( $format, $date );

		return $d && $d->format( $format ) === $date;
	}

}
