<?php
/**
 * iCalendar for Ninja Forms: Tags.
 *
 * @package     Soderlind\NinjaForms\iCalendar
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\iCalendar;

/**
 * Create form tags.
 */
class Tags extends \NF_Abstracts_MergeTags {

	/**
	 * Tag ID.
	 *
	 * @var string
	 */
	protected $id = 'icalendar';

	/**
	 * Form ID.
	 *
	 * @var int
	 */
	public $form_id;

	/**
	 * Add new tags.
	 */
	public function __construct() {
		parent::__construct();

		$this->title      = esc_html__( 'iCalendar', 'icalendar-ninja-forms' );
		$this->merge_tags = [
			'icalendar_link' => [
				'id'       => 'link',
				'tag'      => '{ical:link}',
				'label'    => esc_html__( 'Link', 'icalendar-ninja-forms' ),
				'callback' => 'ical_link',
			],
			'icalendar_url'  => [
				'id'       => 'url',
				'tag'      => '{ical:url}',
				'label'    => esc_html__( 'URL', 'icalendar-ninja-forms' ),
				'callback' => 'ical_url',
			],
		];
	}

	/**
	 * Callback for ical:link} tag.
	 *
	 * @return string
	 */
	public function ical_link() {
		$options = get_option( 'ical_form_' . $this->form_id, [] );
		$forms   = get_option( 'ical_link_form_id', [] );

		if ( isset( $this->form_id, $forms[ $this->form_id ], $options['icalendar_link_text'] ) ) {
			return sprintf( '<a href="%s/event-%s.ics">%s</a>', get_home_url(), untrailingslashit( $forms[ $this->form_id ] ), $options['icalendar_link_text'] );
		} else {
			return '';
		}
	}

	/**
	 * Callback for {ical:url} tag.
	 *
	 * @return string
	 */
	public function ical_url() {
		$forms = get_option( 'ical_link_form_id' );
		if ( isset( $this->form_id, $forms[ $this->form_id ] ) ) {
			return sprintf( '%s/event-%s.ics"', get_home_url(), untrailingslashit( $forms[ $this->form_id ] ) );
		} else {
			return '';
		}
	}

	/**
	 * Setter method for the form_id and callback for the nf_get_form_id action.
	 *
	 * @since 3.2.2
	 *
	 * @param string $form_id The ID of the current form.
	 * @return void
	 */
	public function set_form_id( $form_id ) {
		$this->form_id = $form_id;
	}
}
