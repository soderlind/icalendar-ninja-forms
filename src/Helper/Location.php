<?php
/**
 * iCalendar for Ninja Forms: Pages @codingStandardsIgnoreLine.
 *
 * @package Soderlind\NinjaForms\iCalendar
 * @author Per Søderlind
 * @copyright 2021 Per Søderlind
 * @license GPL-2.0+
 */

declare(strict_types=1);

namespace Soderlind\NinjaForms\iCalendar\Helper;

class Location {

	/**
	 * Get lat/long from address.
	 *
	 * @param string $address Address.
	 * 
	 * @return bool|array<string>
	 */
	public static function get_lat_lng( string $address ) {
		$address = rawurlencode( $address );
		$coord   = get_transient( 'geocode_' . md5( $address ) );
		if ( false === $coord ) {
			$url      = self::build_url( $address );
			$response = wp_remote_get( $url );
			if ( is_wp_error( $response ) ) {
				return false;
			}
			if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				return false;
			}

			$body = wp_remote_retrieve_body( $response );

			$data = json_decode( $body, true );
			if ( ! isset ( $data[0]['lat'], $data[0]['lon'] ) ) {
				return false;
			}

			$coord = self::set_coordinates( $data[0] );
			set_transient( 'geocode_' . md5( $address ), $coord, DAY_IN_SECONDS * 90 );
		}

		return $coord;
	}

	/**
	 * Build URL.
	 *
	 * @param string $address Address.
	 * 
	 * @return string
	 */
	private static function build_url( string $address ): string {
		return 'https://nominatim.openstreetmap.org/search?format=jsonv2&addressdetails=1&q=' . $address . '&limit=1';
	}

	/**
	 * Set coordinates.
	 *
	 * @param array<string> $cord Coordinates.
	 * 
	 * @return array<string>
	 */
	private static function set_coordinates( array $cord ): array {
		if ( isset ( $cord['lat'], $cord['lon'] ) ) {
			return [ 
				'lat'  => $cord['lat'],
				'long' => $cord['lon'],
			];

		}
		return [];
	}
}