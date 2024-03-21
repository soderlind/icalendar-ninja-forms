<?php
/**
 * iCalendar for Ninja Forms. @codingStandardsIgnoreLine.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare(strict_types=1);

namespace Soderlind\NinjaForms\iCalendar;

/**
 * Main class, iCalendar.
 */
final class iCalendar {//phpcs:ignore 

	/**
	 * Object instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Load instances once.
	 *
	 * @return object
	 */
	public static function instance(): object {
		if ( ! ( self::$instance instanceof iCalendar ) ) {
			self::$instance = new iCalendar();
			self::$instance->init();
			$invitation = new Invitation();
			new Permalink( $invitation );
		}
		return self::$instance;
	}

	/**
	 * Add hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'ninja_forms_register_actions', [ $this, 'register_actions' ] );
		add_filter( 'ninja_forms_register_merge_tags', [ $this, 'register_tag' ], 20 );
		add_action( 'nf_admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'ninja_forms_validate_fields', [ $this, 'set_form_id_in_merge_tag' ], 90, 2 );
		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
		add_filter( 'ninja_forms_action_email_attachments', [ $this, 'attach_catendar' ], 10, 3 );

		Helper\Hooks::wp_mail();

	}

	/**
	 * Add action.
	 *
	 * @param array<string,object> $actions List of Ninja Form actions.
	 *
	 * @return array<string,object>
	 */
	public function register_actions( array $actions ): array {
		$actions['icalendar'] = new Action();
		return $actions;
	}

	/**
	 * Register form tags.
	 *
	 * @param array<string,object> $tags Existing tags.
	 *
	 * @return array<string,object>
	 */
	public function register_tag( array $tags ): array {
		$tags['icalendar'] = new Tags();
		return $tags;
	}

	/**
	 * Set form ID in Tags.
	 *
	 * @param bool         $ok True or false. The value is returned unmodified.
	 * @param array<mixed> $form_data Data (Misc.) passed back to the client in the Response.
	 *
	 * @return bool
	 */
	public function set_form_id_in_merge_tag( $ok, $form_data ) {
		$ical_merge_tags = \Ninja_Forms()->merge_tags['icalendar']; //phpcs:ignore WordPress.undefined.function
		$ical_merge_tags->set_form_id( $form_data['form_id'] );

		return $ok;
	}

	/**
	 * Attach calendar to email.
	 *
	 * @param array<string> $attachments Attachments.
	 * @param array<string> $data Data.
	 * @param array<string> $settings Settings.
	 *
	 * @return array<string>
	 */
	public function attach_catendar( array $attachments, array $data, array $settings ): array {
		$form_id = $data['form_id'];
		$ical    = get_option( 'ical_form_' . $form_id );

		if ( isset ( $ical['icalendar_attach_ical'] ) && '1' !== $ical['icalendar_attach_ical'] ) {
			return $attachments;
		}
		// $calendar_file = sprintf( '%s-%s.ics', sanitize_title( $ical['icalendar_title'] ), untrailingslashit( $ical['icalendar_uid'] ) );
		$calendar_file = sprintf( 'event-%s.ics', untrailingslashit( $ical['icalendar_uid'] ) );
		$calendar_url  = sprintf( '%s/%s', get_home_url(), $calendar_file );

		$calendar_attachment = [ 
			'string'      => file_get_contents( $calendar_url ), // String attachment data (required)
			'filename'    => $calendar_file, // Name of the attachment (required)
			'encoding'    => 'base64', // File encoding (defaults to 'base64')
			'type'        => 'text/calendar', // File MIME type (if left unspecified, PHPMailer will try to work it out from the file name)
			'disposition' => 'attachment', // Disposition to use (defaults to 'attachment')
		];

		$attachments[] = $calendar_attachment;

		return $attachments;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'icalendar-action', plugin_dir_url( ICALENDAR_FILE ) . 'include/js/action.js', [], ICALENDAR_VERSION, true );
	}

	/**
	 * Load translation.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain( 'icalendar-ninja-forms', false, basename( dirname( ICALENDAR_FILE ) ) . '/languages' );
	}
}
