<?php
/**
 * iCalendar for Ninja Forms: Invitation.
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
	 * Parse query variables and return iCalendar file if match is set.
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

				$form_id     = $form_uids[ $form_uid ];
				$data        = get_option( 'ical_form_' . $form_id );
				$title       = wp_strip_all_tags( $data['icalendar_title'] );
				$event_start = wp_date( sprintf( '%s %s', $data['icalendar_date'], $data['icalendar_time_start'] ) );
				$event_end   = wp_date( sprintf( '%s %s', $data['icalendar_date'], $data['icalendar_time_end'] ) );

				$message = '';
				if ( isset( $data['icalendar_add_message'], $data['icalendar_message'] ) && '1' === $data['icalendar_add_message'] ) {
					$message .= wp_strip_all_tags( $data['icalendar_message'] );
				}
				if ( isset( $data['icalendar_append_url'], $data['icalendar_post_id'] ) && '1' === $data['icalendar_append_url'] ) {
					$url      = get_permalink( $data['icalendar_post_id'] );
					$message .= sprintf( "\n%s %s", __( 'More information at:', 'icalendar-ninja-forms' ), $url );
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
				header( 'Content-Disposition: inline; filename=calendar.ics' );

				echo esc_html( $icalobj->export() );
				exit();
			}
		}
	}
}