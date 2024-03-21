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

/**
 * Hooks	
 */
class Hooks {


	/**
	 * Add wp_mail hook.
	 */
	public static function wp_mail(): void {
		/**
		 * Adds support for defining attachments as data arrays in wp_mail().
		 * Allows us to send string-based or binary attachments (non-filesystem)
		 * and gives us more control over the attachment data.
		 * 
		 * @param array  $atts  Array of the `wp_mail()` arguments.
		 *     - string|string[] $to          Array or comma-separated list of email addresses to send message.
		 *     - string          $subject     Email subject.
		 *     - string          $message     Message contents.
		 *     - string|string[] $headers     Additional headers.
		 *     - string|string[] $attachments Paths to files to attach.
		 *
		 * @see https://gist.github.com/thomasfw/5df1a041fd8f9c939ef9d88d887ce023/
		 */
		add_filter( 'wp_mail', function ($atts) {
			$attachment_arrays = [];
			if ( array_key_exists( 'attachments', $atts ) && isset ( $atts['attachments'] ) && $atts['attachments'] ) {
				$attachments = $atts['attachments'];
				if ( is_array( $attachments ) ) {
					// Is the $attachments array a single array of attachment data, or an array containing multiple arrays of 
					// attachment data? (note that the array may also be a one-dimensional array of file paths, as-per default usage).
					$is_multidimensional_array = count( $attachments ) == count( $attachments, COUNT_RECURSIVE ) ? false : true;
					if ( ! $is_multidimensional_array )
						$attachments = [ $attachments ];
					// Work out which attachments we want to process here. If the value is an array with either 
					// a 'path' or 'path' key, then we'll process it separately and remove it from the 
					// $atts['attachments'] so that WP doesn't try to process it in wp_mail().
					foreach ( $attachments as $index => $attachment ) {
						if ( is_array( $attachment ) && ( array_key_exists( 'path', $attachment ) || array_key_exists( 'string', $attachment ) ) ) {
							$attachment_arrays[] = $attachment;
							if ( $is_multidimensional_array ) {
								unset ( $atts['attachments'][ $index ] );
							} else {
								$atts['attachments'] = [];
							}
						}
					}
				}

				// Set the $wp_mail_attachments global to our attachment data.
				// We'll read this later to check if any extra attachments should
				// be added to the email. The value will be reset every time wp_mail()
				// is called.
				global $wp_mail_attachments;
				$wp_mail_attachments = $attachment_arrays;

				// We can't use the global $phpmailer to add our attachments directly in the 'wp_mail' filter callback because WP calls $phpmailer->clearAttachments() 
				// after this filter runs. Instead, we now hook into the 'phpmailer_init' action (triggered right before the email is sent), and read 
				// the $wp_mail_attachments global to check for any additional attachments to add. 
				add_action( 'phpmailer_init', function (\PHPMailer\PHPMailer\PHPMailer $phpmailer) {
					// Check the $wp_mail_attachments global for any attachment data, and reset it for good measure.
					$attachment_arrays = [];
					if ( array_key_exists( 'wp_mail_attachments', $GLOBALS ) ) {
						global $wp_mail_attachments;
						$attachment_arrays   = $wp_mail_attachments;
						$wp_mail_attachments = [];
					}

					// Loop through our attachment arrays and attempt to add them using PHPMailer::addAttachment() or PHPMailer::addStringAttachment():
					foreach ( $attachment_arrays as $attachment ) {
						$is_filesystem_attachment = array_key_exists( 'path', $attachment ) ? true : false;
						try {
							$encoding    = $attachment['encoding'] ?? $phpmailer::ENCODING_BASE64;
							$type        = $attachment['type'] ?? '';
							$disposition = $attachment['disposition'] ?? 'attachment';
							if ( $is_filesystem_attachment ) {
								$phpmailer->addAttachment( ( $attachment['path'] ?? null ), ( $attachment['name'] ?? '' ), $encoding, $type, $disposition );
							} else {
								$phpmailer->addStringAttachment( ( $attachment['string'] ?? null ), ( $attachment['filename'] ?? '' ), $encoding, $type, $disposition );
							}
						} catch (\PHPMailer\PHPMailer\Exception $e) {
							continue;
						}
					}
					// var_dump( $phpmailer->getAttachments() ); // Debug the mail attachments, including those parsed by WP.
				} );
			}
			return $atts;
		} );

	}
}