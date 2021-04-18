<?php
/**
 * iCalendar for Ninja Forms: Add action.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\iCalendar;

/**
 * Add Ninja Form Action.
 */
class Action extends \NF_Abstracts_Action {

	/**
	 * Action name.
	 *
	 * @var string
	 */
	protected $_name = 'icalendar'; // phpcs:ignore

	/**
	 * Tags.
	 *
	 * @var array
	 */
	protected $_tags = []; // phpcs:ignore

	/**
	 * Timing.
	 *
	 * @var string
	 */
	protected $_timing = 'late'; // phpcs:ignore

	/**
	 * Priority.
	 *
	 * @var int
	 */
	protected $_priority = '10'; // phpcs:ignore

	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct(); // phpcs:ignore

		$this->_nicename = esc_html__( 'iCalendar', 'icalendar-ninja-forms' );

		$event_pages     = Helper\Pages::get();
		$settings        = Helper\Config::get( $event_pages );
		$this->_settings = $settings;

		if ( empty( $this->form_id ) && isset( $_POST['form'] ) ) { // phpcs:ignore
			$form_data = json_decode( $_POST['form'], true ); // phpcs:ignore
			if ( isset( $form_data['id'] ) ) {
				$this->form_id = esc_html( $form_data['id'] );
			}
		}
	}

	/**
	 * Overloaded method. When a form is submitted, reads the action settings and processes them.
	 *
	 * @param array   $action_settings Actions settings.
	 * @param integer $form_id         Form ID.
	 * @param array   $data            Form data.
	 *
	 * @return array
	 */
	public function process( $action_settings, $form_id, $data ) {

		if ( isset( $action_settings['icalendar_title'] ) ) {

			$ical_data = get_option( 'ical_form_' . $form_id, [] );

			// Create uid if it doesn't exist.
			if ( ! isset( $ical_data['icalendar_uid'] ) ) {
				$uid = uniqid();
			} else {
				$uid = $ical_data['icalendar_uid'];
			}

			// Append array elements from the second array to the first array
			// while not overwriting the elements from the first array and not re-indexing,
			// using the + array union operator.
			$ical_data = array_filter(
				$action_settings,
				function ( $key ) {
					return( strpos( $key, 'icalendar_' ) !== false );
				},
				ARRAY_FILTER_USE_KEY
			) + $ical_data;

			$ical_data['icalendar_uid'] = $uid;

			if ( empty( $ical_data['icalendar_organizer'] ) ) {
				$ical_data['icalendar_organizer'] = get_option( 'admin_email' );
			}

			$ical_link_form_id = get_option( 'ical_link_form_id', [] ) + [
				$form_id => $uid,
			];

			update_option( 'ical_form_' . $form_id, $ical_data );
			update_option( 'ical_link_form_id', $ical_link_form_id );
		}

		return $data;
	}
}
