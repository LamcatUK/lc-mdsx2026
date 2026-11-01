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
