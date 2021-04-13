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
 * Add and parse custom permalink.
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
	public static $query_var = 'icalendar';

	/**
	 * Add hooks.
	 *
	 * @uses Invitation->card
	 *
	 * @param Invitation $invitation
	 */
	public function __construct( Invitation $invitation) {
		add_action( 'parse_request', [ $invitation, 'card' ] );
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
		$public_query_vars[] = self::$query_var;
		return $public_query_vars;
	}
}
