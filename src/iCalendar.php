<?php
/**
 * iCalendar for Ninja Forms.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\iCalendar;

/**
 * iCalendar.
 */
final class iCalendar {

	/**
	 * Object instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * All objects.
	 *
	 * @var array
	 */
	public $objects = [];

	/**
	 * Load instances once.
	 *
	 * @return object
	 */
	public static function instance() : object {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof iCalendar ) ) {
			self::$instance = new iCalendar();
			self::$instance->init();
			// self::$instance->objects['tags']       = new Tags();
			// self::$instance->objects['action']     = new Action();
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
		add_filter( 'ninja_forms_validate_fields', [ $this, 'set_form_id_in_merge_tag' ], 90,2 );
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/**
	 * Add action.
	 *
	 * @param array $actions List of Ninja Form actions.
	 *
	 * @return array
	 */
	public function register_actions( array $actions ) :array {
		$actions['icalendar'] = new Action();
		return $actions;
	}

	/**
	 * Register form tags.
	 *
	 * @param array $tags Existing tags.
	 *
	 * @return array
	 */
	public function register_tag( array $tags ) : array {
		$tags['icalendar'] = new Tags();
		return $tags;
	}

	/**
	 * Set form ID in Tags.
	 *
	 * @param bool $ok
	 * @param array $form_data Data (Misc.) passed back to the client in the Response.
	 *
	 * @return bool
	 */
	public function set_form_id_in_merge_tag( $ok, $form_data ) {
		$ical_merge_tags = \Ninja_Forms()->merge_tags['icalendar'];
		$ical_merge_tags->set_form_id( $form_data['form_id'] );

		return $ok;
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
	public function load_textdomain() : void {
		load_plugin_textdomain( 'icalendar-ninja-forms', false, \plugin_dir_path( ICALENDAR_FILE ) . 'languages' );
	}
}
