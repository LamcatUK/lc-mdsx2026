<?php
/**
 * Contact Form 7 integration: the Contact Form block embeds a CF7 form, and the
 * theme supplies the styling (css/theme.min.css) instead of CF7's own stylesheet.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

// The theme styles CF7 markup itself (src/blocks/contact-form.css).
add_filter( 'wpcf7_load_css', '__return_false' );

// The form template is written as explicit <p>/<label> markup; don't let CF7 add its own paragraphs and <br>s.
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/**
 * Thank-you page per form. Add a line to the form's Additional Settings tab:
 *
 *     thank_you_page: /thank-you/
 *
 * (a path on this site, or a full address on it). After a successful send the form's response
 * carries `redirect_url`, which src/js/contact-form.js follows. Forms without the line stay on the page.
 *
 * @param array $response Feedback response.
 * @param array $result   Submission result.
 * @return array
 */
function lc_mdsx2026_cf7_thank_you_redirect( $response, $result ) {
	if ( 'mail_sent' !== ( $result['status'] ?? '' ) || ! class_exists( 'WPCF7_ContactForm' ) ) {
		return $response;
	}

	$form   = WPCF7_ContactForm::get_current();
	$target = $form ? trim( (string) $form->pref( 'thank_you_page' ), " \t\"'" ) : '';
	if ( '' === $target ) {
		return $response;
	}

	// wp_validate_redirect() keeps it on this site.
	$url = wp_validate_redirect( esc_url_raw( 0 === strpos( $target, '/' ) ? home_url( $target ) : $target ), '' );
	if ( $url ) {
		$response['redirect_url'] = $url;
	}

	return $response;
}
add_filter( 'wpcf7_feedback_response', 'lc_mdsx2026_cf7_thank_you_redirect', 10, 2 );

/**
 * Route enquiries to extra people by topic. Add lines to a form's Additional Settings tab:
 *
 *     route: Press and media = press@mdsx-creative.com
 *     route: Working at MDSX = janelle@mdsx-creative.com, brian@mdsx-creative.com
 *
 * When the visitor picks the topic named on a line, those addresses are added to the form's main
 * email ("To" stays the default list, so everyone on it still gets every enquiry). The topic is read
 * from the field called `topic`; use `route_field: other-field-name` to key off another field. The
 * auto-reply to the visitor is never touched. Read by wpcf7_mail_components below.
 *
 * @param array             $components Mail components (subject, sender, body, recipient …).
 * @param WPCF7_ContactForm $form       The form.
 * @param WPCF7_Mail        $mail       The mail being built ('mail' or 'mail_2').
 * @return array
 */
function lc_mdsx2026_cf7_route_by_topic( $components, $form, $mail ) {
	if ( ! $form || ! $mail || 'mail' !== $mail->name() ) {
		return $components;
	}

	$lines      = $form->additional_setting( 'route', false );
	$submission = WPCF7_Submission::get_instance();
	if ( ! $lines || ! $submission ) {
		return $components;
	}

	$field  = trim( (string) $form->pref( 'route_field' ), " \t\"'" );
	$chosen = $submission->get_posted_data( '' !== $field ? $field : 'topic' );
	$chosen = mb_strtolower( trim( (string) ( is_array( $chosen ) ? reset( $chosen ) : $chosen ) ) );
	if ( '' === $chosen ) {
		return $components;
	}

	$recipients = array_filter( array_map( 'trim', explode( ',', (string) $components['recipient'] ) ) );

	foreach ( $lines as $line ) {
		$parts = explode( '=', (string) $line, 2 );
		if ( 2 !== count( $parts ) || mb_strtolower( trim( $parts[0], " \t\"'" ) ) !== $chosen ) {
			continue;
		}
		foreach ( explode( ',', $parts[1] ) as $address ) {
			$address = trim( $address );
			if ( is_email( $address ) && ! in_array( mb_strtolower( $address ), array_map( 'mb_strtolower', $recipients ), true ) ) {
				$recipients[] = $address;
			}
		}
	}

	$components['recipient'] = implode( ', ', $recipients );

	return $components;
}
add_filter( 'wpcf7_mail_components', 'lc_mdsx2026_cf7_route_by_topic', 10, 3 );
