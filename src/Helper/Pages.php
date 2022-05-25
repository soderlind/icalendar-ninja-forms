<?php
/**
 * iCalendar for Ninja Forms: Pages @codingStandardsIgnoreLine.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\iCalendar\Helper;

/**
 * Get public pages
 */
class Pages {
	/**
	 * Get list of pages.
	 *
	 * @return array<mixed>   Event list setting
	 */
	public static function get() : array {
		$events = get_posts(
			[
				'post_type'   => 'page',
				'post_status' => 'publish',
				'numberposts' => -1,
			]
		);

		$options = [];
		$value   = '';
		if ( $events ) {
			foreach ( $events as  $event ) {
				if ( '' === $value ) {
					$value = $event->ID;
				}
				$options[] = [
					'label' => $event->post_title,
					'value' => $event->ID,
				];
			}
			return [
				'name'        => 'icalendar_post_id',
				'type'        => 'select',
				'label'       => __( 'Event page', 'date-range-ninja-forms' ),
				'placeholder' => __( 'Select Post', 'icalendar-ninja-forms' ),
				'width'       => 'one-half',
				'group'       => 'primary',
				'options'     => $options,
				'value'       => $value,
				'help'        => __( 'Select the page that describes the event', 'icalendar-ninja-forms' ),
				'deps'        => [
					'icalendar_append_url' => 1,
				],

			];
		} else {
			return [];
		}

	}
}
