<?php
/**
 * iCalendar for Ninja Forms: Action fields. @codingStandardsIgnoreLine.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\iCalendar\Helper;

use DateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Action fields
 */
class Config {

	/**
	 * Get action settings
	 *
	 * @param array $event_pages Event page settings.
	 *
	 * @return array
	 */
	public static function get( array $event_pages = [] ) : array {
		return apply_filters(
			'ninja_forms_action_icalendar_settings',
			[
				// Primary settings.
				'date'                => [
					'name'     => 'date',
					'type'     => 'fieldset',
					'label'    => esc_html__( 'Event Date', 'icalendar-ninja-forms' ),
					'width'    => 'full',
					'group'    => 'primary',
					'help'     => esc_html__( 'Select the fields that contain the event date', 'icalendar-ninja-forms' ),
					'settings' => [
						[
							'name'           => 'icalendar_date',
							'type'           => 'textbox',
							'group'          => 'primary',
							'label'          => __( 'Start', 'icalendar-ninja-forms' ),
							'width'          => 'one-half',
							'use_merge_tags' => false,
						],
						[
							'name'           => 'icalendar_end_date',
							'type'           => 'textbox',
							'group'          => 'primary',
							'label'          => __( 'End', 'icalendar-ninja-forms' ),
							'width'          => 'one-half',
							'use_merge_tags' => false,
						],
					],
				],
				'event_message'       => [
					'name'     => 'event_message',
					'type'     => 'fieldset',
					'label'    => esc_html__( 'Event Information', 'icalendar-ninja-forms' ),
					'width'    => 'full',
					'group'    => 'primary',
					'settings' => [
						[
							'name'  => 'icalendar_title',
							'type'  => 'textbox',
							'label' => esc_html__( 'Event title', 'icalendar-ninja-forms' ),
							'width' => 'full',
							'group' => 'primary',
							'help'  => __( 'MANDATORY. If empty, "Event invitation" will be used', 'icalendar-ninja-forms' ),
						],
						[
							'name'  => 'icalendar_add_message',
							'type'  => 'toggle',
							'label' => esc_html__( 'Add Message', 'icalendar-ninja-forms' ),
							'width' => 'one-third',
							'group' => 'primary',
							'value' => false,
						],
						[
							'name'           => 'icalendar_message',
							'type'           => 'textarea',
							'label'          => esc_html__( 'Message', 'icalendar-ninja-forms' ),
							'width'          => 'full',
							'group'          => 'primary',
							'deps'           => [
								'icalendar_add_message' => 1,
							],
							'use_merge_tags' => true,
						],
					],
				],
				'event_url'           => [
					'name'     => 'event_url',
					'type'     => 'fieldset',
					'label'    => esc_html__( 'Event URL', 'icalendar-ninja-forms' ),
					'width'    => 'full',
					'group'    => 'primary',
					'settings' => [
						[
							'name'  => 'icalendar_append_url',
							'type'  => 'toggle',
							'label' => esc_html__( 'Append URL', 'icalendar-ninja-forms' ),
							'width' => 'one-third',
							'group' => 'primary',
							'value' => false,
							'help'  => __( 'Append the event URL to the calendar', 'icalendar-ninja-forms' ),
						],
						$event_pages, // see Pages.php.
						[
							'name'           => 'icalendar_url_prepend',
							'type'           => 'textbox',
							'width'          => 'one-half',
							'group'          => 'primary',
							'label'          => esc_html__( 'Link text', 'icalendar-ninja-forms' ),
							'value'          => esc_html__( 'More information', 'icalendar-ninja-forms' ),
							'help'           => esc_html__( 'Link text to iCAL for this event.', 'icalendar-ninja-forms' ),
							'deps'           => [
								'icalendar_append_url' => 1,
							],
							'use_merge_tags' => true,
						],
					],

				],
				'icalendar_organizer' => [
					'name'           => 'icalendar_organizer',
					'type'           => 'textbox',
					'label'          => esc_html__( 'Organizer email', 'icalendar-ninja-forms' ),
					'width'          => 'full',
					'group'          => 'primary',
					'value'          => '{wp:admin_email}',
					'use_merge_tags' => true,
					'help'           => __( 'MANDATORY. If empty, the wp admin email address will be used.', 'icalendar-ninja-forms' ),
				],
				// Advanced settings.
				'icalendar_link_text' => [
					'name'           => 'icalendar_link_text',
					'type'           => 'textbox',
					'group'          => 'primary',
					'label'          => esc_html__( 'iCAL Link text', 'icalendar-ninja-forms' ),
					'value'          => esc_html__( 'Add the event to your calendar', 'icalendar-ninja-forms' ),
					'width'          => 'full',
					'help'           => esc_html__( 'Link text to iCAL for this event.', 'icalendar-ninja-forms' ),
					'use_merge_tags' => false,
				],
				/*
				'o_date'              => [
					'name'     => 'o_date',
					'type'     => 'fieldset',
					'label'    => esc_html__( 'Override Event Date using merge tags', 'icalendar-ninja-forms' ),
					'width'    => 'full',
					'group'    => 'advanced',
					'help'     => esc_html__( 'Override Event Date using merge tags', 'icalendar-ninja-forms' ),
					'settings' => [
						[
							'name'           => 'o_icalendar_date_start',
							'type'           => 'textbox',
							'group'          => 'advanced',
							'label'          => __( 'Start', 'icalendar-ninja-forms' ),
							'help'           => esc_html__( 'Must be in a valid PHP DateTime format, including hour', 'icalendar-ninja-forms' ),
							'width'          => 'one-half',
							'use_merge_tags' => true,
						],
						[
							'name'           => 'o_icalendar_date_end',
							'type'           => 'textbox',
							'group'          => 'advanced',
							'label'          => __( 'End', 'icalendar-ninja-forms' ),
							'help'           => esc_html__( 'Must be in a valid PHP DateTime format, including hour', 'icalendar-ninja-forms' ),
							'width'          => 'one-half',
							'use_merge_tags' => true,
						],
					],
				],
				*/
			]
		);
	}
}
