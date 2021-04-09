<?php
/**
 * iCalendar for Ninja Forms: Action fields.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\iCalendar;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters(
	'ninja_forms_action_icalendar_settings',
	[
		// Primary settings.
		'date'                   => [
			'name'     => 'date',
			'type'     => 'fieldset',
			'label'    => esc_html__( 'Event Date', 'icalendar-ninja-forms' ),
			'width'    => 'full',
			'group'    => 'primary',
			'settings' => [
				[
					'name'           => 'icalendar_date',
					'type'           => 'textbox',
					'group'          => 'primary',
					'label'          => __( 'Date', 'icalendar-ninja-forms' ),
					'placeholder'    => __( 'YYYY-MM-DD', 'icalendar-ninja-forms' ),
					'width'          => 'one-third',
					'help'           => __( 'Add event date in the date format YYYY-MM-DD, eg 2021-03-20', 'icalendar-ninja-forms' ),
					'use_merge_tags' => false,
				],
				[
					'name'           => 'icalendar_time_start',
					'type'           => 'textbox',
					'group'          => 'primary',
					'label'          => __( 'Start', 'icalendar-ninja-forms' ),
					'placeholder'    => __( 'HH:MM', 'icalendar-ninja-forms' ),
					'width'          => 'one-third',
					'help'           => __( 'Add time in the time format HH:MM, eg 14:10', 'icalendar-ninja-forms' ),
					'use_merge_tags' => false,
				],
				[
					'name'           => 'icalendar_time_end',
					'type'           => 'textbox',
					'group'          => 'primary',
					'label'          => __( 'End', 'icalendar-ninja-forms' ),
					'placeholder'    => __( 'HH:MM', 'icalendar-ninja-forms' ),
					'width'          => 'one-third',
					'help'           => __( 'Add time in the time format HH:MM, eg 14:10', 'icalendar-ninja-forms' ),
					'use_merge_tags' => false,
				],
			],
		],
		'event_message'          => [
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
					'help'  => __( 'Append the event URL to tne calendar', 'icalendar-ninja-forms' ),
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
					'name'  => 'icalendar_message',
					'type'  => 'textarea',
					'label' => esc_html__( 'Message', 'icalendar-ninja-forms' ),
					'width' => 'full',
					'group' => 'primary',
					'deps'  => [
						'icalendar_add_message' => 1,
					],
				],
			],
		],
		'event_url'              => [
			'name'     => 'event_url',
			'type'     => 'fieldset',
			'label'    => esc_html__( 'Event URL', 'icalendar-ninja-forms' ),
			'width'    => 'full',
			'group'    => 'primary',
			'settings' => [
				[
					'name'  => 'icalendar_append_url',
					'type'  => 'toggle',
					'label' => esc_html__( 'Append URL to event page', 'icalendar-ninja-forms' ),
					'width' => 'one-third',
					'group' => 'primary',
					'value' => false,
					'help'  => __( 'Append the event URL to the calendar', 'icalendar-ninja-forms' ),
				],
				$event_pages,
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
		],
		// Advanced settings.
		'icalendar_link_text' => [
			'name'           => 'icalendar_link_text',
			'type'           => 'textbox',
			'group'          => 'advanced',
			'label'          => esc_html__( 'iCAL Link text', 'icalendar-ninja-forms' ),
			'value'          => esc_html__( 'Add the event to your calendar', 'icalendar-ninja-forms' ),
			'width'          => 'one-half',
			'help'           => esc_html__( 'Link text to iCAL for this event.', 'icalendar-ninja-forms' ),
			'use_merge_tags' => false,
		],
	]
);
