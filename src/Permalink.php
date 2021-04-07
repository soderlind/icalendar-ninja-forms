<?php
/**
 * iCalendar for Ninja Forms: Permalink.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\iCalendar;

use DateTime;

/**
 * Permalink class.
 */
class Permalink {

	/**
	 * Permalink rule.
	 *
	 * @var string
	 */
	protected $permalink_rule = 'event-(.*)\.ics$';
	/**
	 * Query variable.
	 *
	 * @var string
	 */
	private $query_var = 'icalendar';

	/**
	 * Add hooks.
	 */
	public function __construct() {
		add_action( 'parse_request', [ $this, 'action_reference_parse_request' ] );
		add_filter( 'generate_rewrite_rules', [ $this, 'action_reference_generate_rewrite_rules' ] );
		add_filter( 'query_vars', [ $this, 'filter_query_vars' ] );
		add_action( 'admin_init', [ $this, 'flush_rewrite_rule' ] );
	}

	/**
	 * Fires as an admin screen or script is being initialized.
	 */
	public function flush_rewrite_rule() : void {
		$rules = $GLOBALS['wp_rewrite']->wp_rewrite_rules();
		if ( ! isset( $rules[ $this->permalink_rule ] ) ) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}
	}

	/**
	 * Fires after the rewrite rules are generated.
	 *
	 * @param \WP_Rewrite $wp_rewrite Current WP_Rewrite instance (passed by reference).
	 */
	public function action_reference_generate_rewrite_rules( \WP_Rewrite $wp_rewrite ) {
		$new_rules         = [ $this->permalink_rule => sprintf( 'index.php?icalendar=%s', $wp_rewrite->preg_index( 1 ) ) ];
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
		return $wp_rewrite->rules;
	}

	/**
	 * Filters the query variables allowed before processing.
	 *
	 * @param string[] $public_query_vars The array of allowed query variable names.
	 * @return string[] The array of allowed query variable names
	 */
	public function filter_query_vars( array $public_query_vars ) : array {
		$public_query_vars[] = $this->query_var;
		return $public_query_vars;
	}

	/**
	 * Parse query variables and return iCalendar file if match is set.
	 *
	 * @param \WP $wp Current WordPress environment instance (passed by reference).
	 */
	public function action_reference_parse_request( \WP $wp ) : void {
		if ( isset( $wp->query_vars[ $this->query_var ] ) ) {
			require_once \plugin_dir_path( ICALENDAR_FILE ) . 'include/icalendar/zapcallib.php';

			$form_uid  = $wp->query_vars[ $this->query_var ];
			$form_uids = array_flip( get_option( 'ical_link_form_id', [] ) );

			if ( ! isset( $form_uids[ $form_uid ] ) ) {
				// Cheating, are we?
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				nocache_headers();
				get_template_part( 404 );

				die();
			} else {

				$form_id     = $form_uids[ $form_uid ];
				$data        = get_option( 'ical_form_' . $form_id );
				$title       = wp_strip_all_tags( $data['icalendar_title'] );
				$event_start = wp_date( sprintf( '%s %s', $data['icalendar_date'], $data['icalendar_time_start'] ) );
				$event_end   = wp_date( sprintf( '%s %s', $data['icalendar_date'], $data['icalendar_time_end'] ) );

				$message = '';
				if ( isset( $data['icalendar_add_message'], $data['icalendar_message'] ) && '1' === $data['icalendar_add_message'] ) {
					$message .= wp_strip_all_tags( $data['icalendar_message'] );
				}
				if ( isset( $data['icalendar_append_url'], $data['icalendar_post_id'] ) && '1' === $data['icalendar_append_url'] ) {
					$url      = get_permalink( $data['icalendar_post_id'] );
					$message .= sprintf( "\n%s %s", __( 'More information at:', 'icalendar-ninja-forms' ), $url );
				}

				$icalobj  = new \ZCiCal();
				$eventobj = new \ZCiCalNode( 'VEVENT', $icalobj->curnode );
				$eventobj->addNode( new \ZCiCalDataNode( 'SUMMARY:' . $title ) );
				$eventobj->addNode( new \ZCiCalDataNode( 'DTSTART:' . \ZCiCal::fromSqlDateTime( $event_start ) ) );
				$eventobj->addNode( new \ZCiCalDataNode( 'DTEND:' . \ZCiCal::fromSqlDateTime( $event_end ) ) );
				$eventobj->addNode( new \ZCiCalDataNode( 'ORGANIZER:' . $data['icalendar_organizer'] ) );

				$uid = 'icalendar-ninja-forms-' . $data['icalendar_uid'];
				$eventobj->addNode( new \ZCiCalDataNode( 'UID:' . $uid ) );

				// DTSTAMP is a required item in VEVENT.
				$utc_now = wp_date( 'Y-m-d H:i:s', time(), new \DateTimeZone( 'UTC' ) );
				$eventobj->addNode( new \ZCiCalDataNode( 'DTSTAMP:' . \ZCiCal::fromSqlDateTime( $utc_now ) ) );

				if ( '' !== $message ) {
					$eventobj->addNode( new \ZCiCalDataNode( 'DESCRIPTION:' . \ZCiCal::formatContent( $message ) ) );
				}

				header( 'Content-type: text/calendar; charset=utf-8' );
				header( 'Content-Disposition: inline; filename=calendar.ics' );

				echo esc_html( $icalobj->export() );
				exit();
			}
		}
	}
}
