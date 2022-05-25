<?php
/**
 * iCalendar for Ninja Forms @codingStandardsIgnoreLine.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Ninja Forms - iCalendar
 * Plugin URI: https://github.com/soderlind/icalendar-ninja-forms
 * GitHub Plugin URI: https://github.com/soderlind/icalendar-ninja-forms
 * Description: Add an Event to your Ninja Forms.
 * Version:     2.1.1
 * Author:      Per Søderlind
 * Author URI:  https://soderlind.no
 * Text Domain: icalendar-ninja-forms
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\iCalendar;

if ( ! defined( 'ABSPATH' ) ) {
	\wp_die();
}
const ICALENDAR_FILE    = __FILE__;
const ICALENDAR_VERSION = '2.1.1';

require_once \plugin_dir_path( ICALENDAR_FILE ) . 'vendor/autoload.php';


/**
 * Load iCalendar.
 *
 * Use instead of global. See \Soderlind\NinjaForms\iCalendar\Permalink().
 *
 * @return object
 */
function iCalendar() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return iCalendar::instance();
}
iCalendar();
