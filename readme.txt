=== iCalendar for Ninja Forms ===
Contributors: PerS
Donate link: https://soderlind.no/donate/
Tags: date
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 2.2.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a iCalendar to Ninja Forms.

== Description ==

Add a iCalendar to your Ninja Forms.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/icalendar-ninja-forms` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use Ninja Forms to add the iCalendar.

== Screenshots ==

1. Settings.
2. Using Ninja Forms to add the iCalendar.
3. iCalendar at the front-end.

== Changelog ==

= 2.2.0 =

* Create the calendar using [Eluceo\iCal](https://github.com/markuspoerschke/iCal) 
	- multiline message 
	- link to event page 
	- add event location, inclusive latitude and longitude
	- attach calendar to email
* Update translation file.
	- Add Norwegian translation
* Update dependencies
* Housekeeping


= 2.1.1 =

* PHPStan it.

= 2.1.0 =

* Enable conditional logic for the iCalendar.

= 2.0.0 =

* Note, this is a breaking change. You can now select a date range and a time range. 

= 1.3.4 =

* Remove incompatible attribute.

= 1.3.3 =

* Rename method in `include/js/actions.js`

= 1.3.2 =

* Set default title in vcalendar
* Mark mandatory settings

= 1.3.0 =

* Refactor away from anonymous class.

= 1.2.0 =

* Refactor, move create VCALENDAR to card() in class Invitation.

= 1.1.4 =

* Update translation file.

= 1.1.3 =

* Rename methods in JavaScript
* Lint JavaScript using [Rome](https://rome.tools/#installation-and-usage)

= 1.1.2 =

* Remove wp_localize_script
* Rename style object

= 1.1.1 =

* Add "now" as default date and time.

= 1.1.0 =

* Add date and time picker

= 1.0.0 =

* Initial release.


