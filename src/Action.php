<?php
/**
 * iCalendar for Ninja Forms: Action.
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
class Action {

	/**
	 * Register action and enqueue script.
	 */
	public function __construct() {
		add_filter( 'ninja_forms_register_actions', [ $this, 'register_actions' ] );
		add_action( 'nf_admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Add action.
	 *
	 * @param array $actions List of Ninja Form actions.
	 *
	 * @return array
	 */
	public function register_actions( array $actions ) :array {
		$actions['icalendar'] = new class() extends \NF_Abstracts_Action { // phpcs:ignore Anonymous class, PHP 7.x required.

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

				$event_pages     = $this->get_posts();
				$settings        = self::config( 'iCalendarConfig', $event_pages );
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

			/**
			 * Load the config file.
			 *
			 * @param string $file_name Config file name, without file extension.
			 * @param array  $event_pages List with event pages.
			 *
			 * @return array
			 */
			protected static function config( string $file_name, array $event_pages ) : array {
				return include \plugin_dir_path( ICALENDAR_FILE ) . 'include/config/' . $file_name . '.php';
			}

			/**
			 * Create event page list.
			 *
			 * @return Array   Event list setting
			 */
			protected function get_posts() {
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
						'help'        => __( 'Select the page that describe the event', 'icalendar-ninja-forms' ),
						'deps'        => [
							'icalendar_append_url' => 1,
						],
					];
				} else {
					return [];
				}

			}
		};

		return $actions;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'icalendar-action', plugin_dir_url( ICALENDAR_FILE ) . 'include/js/action.js', [], ICALENDAR_VERSION, true );
		wp_localize_script(
			'icalendar-action',
			'icalnfi18n',
			[
				'errorInvalidDateFormat' => esc_html__( 'Invalid date format', 'icalendar-ninja-forms' ),
				'errorInvalidTimeFormat' => esc_html__( 'Invalid time format', 'icalendar-ninja-forms' ),
			]
		);
	}
}
