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
			add_action(
				'init',
				function() : void {
					load_plugin_textdomain( 'date-range-ninja-forms', false, \plugin_dir_path( ICALENDAR_FILE ) . 'languages' );
				}
			);
			self::$instance = new iCalendar();

			self::$instance->objects['tags']     = new Tags();
			self::$instance->objects['action']   = new Action();
			self::$instance->objects['permlink'] = new Permalink();
		}
		return self::$instance;
	}
}
