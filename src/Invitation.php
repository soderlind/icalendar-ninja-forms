<?php
/**
 * iCalendar for Ninja Forms: Invitation. @codingStandardsIgnoreLine.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare(strict_types=1);

namespace Soderlind\NinjaForms\iCalendar;

use DateTimeImmutable;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\EmailAddress;
use Eluceo\iCal\Domain\ValueObject\GeographicPosition;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\Organizer;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

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
		if ( isset ( $wp->query_vars[ Permalink::$query_var ] ) ) {
			// require_once \plugin_dir_path( ICALENDAR_FILE ) . 'include/icalendar/zapcallib.php';

			$form_uid  = $wp->query_vars[ Permalink::$query_var ];
			$form_uids = array_flip( get_option( 'ical_link_form_id', [] ) );
			if ( ! isset ( $form_uids[ $form_uid ] ) ) {
				// Cheating, are we?
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				nocache_headers();
				get_template_part( '404' );

				die();
			} else {

				$form_id = $form_uids[ $form_uid ];
				$data    = get_option( 'ical_form_' . $form_id );

				// sanize data
				// $data = array_map( 'sanitize_text_field', $data );

				$title       = ( ! empty ( $data['icalendar_title'] ) ) ? wp_strip_all_tags( $data['icalendar_title'] ) : __( 'Event invitation', 'icalendar-ninja-forms' );
				$organizer   = ( ! empty ( $data['icalendar_organizer'] ) ) ? wp_strip_all_tags( $data['icalendar_organizer'] ) : __( 'Organizer', 'icalendar-ninja-forms' );
				$event_start = $data['icalendar_date'];
				$event_end   = $data['icalendar_end_date'];

				$message = '';
				if ( isset ( $data['icalendar_add_message'], $data['icalendar_message'] ) && '1' === $data['icalendar_add_message'] ) {
					$message .= wp_strip_all_tags( $data['icalendar_message'] );
				}


				// 1. Create Event domain entity.
				$event = new Event();
				$event
					->setSummary( $title )
					->setOrganizer( new Organizer(
						new EmailAddress( $organizer )
					) )
					->setOccurrence(
						new TimeSpan(

							new DateTime( new DateTimeImmutable( $event_start ), true ),
							new DateTime( new DateTimeImmutable( $event_end ), true )

						)
					);
				if ( '' !== $message ) {
					$event->setDescription( $message );
				}
				if ( isset ( $data['icalendar_append_url'], $data['icalendar_post_id'] ) && '1' === $data['icalendar_append_url'] ) {
					$event->setUrl( new Uri( get_permalink( $data['icalendar_post_id'] ) ) );
				}

				if ( isset ( $data['icalendar_venue_add'], $data['icalendar_venue'] ) && '1' === $data['icalendar_venue_add'] ) {
					$venue    = str_replace( array( "\r", "\n" ), ", ", $data['icalendar_venue'] );
					$location = (array) Helper\Location::get_lat_lng( $venue );
					if ( isset ( $location['lat'], $location['long'] ) ) {
						$event->setLocation(
							( new Location( $venue ) )
								->withGeographicPosition( new GeographicPosition( (float) $location['lat'], (float) $location['long'] ) )
						);
					} else {
						$event->setLocation( new Location( $venue ) );
					}
				}

				// 2. Create Calendar domain entity.
				$calendar = new Calendar( [ $event ] );

				// 3. Transform domain entity into an iCalendar component
				$componentFactory  = new CalendarFactory();
				$calendarComponent = $componentFactory->createCalendar( $calendar );

				$ics = (string) $calendarComponent;


				// 4. Set HTTP headers.
				header( 'Content-Type: text/calendar; charset=utf-8' );
				header( sprintf( 'Content-Disposition: attachment; filename=event-%s.ics', $data['icalendar_uid'] ) );
				// 5. Output.
				echo $ics;

				exit();
			}
		}
	}
}

