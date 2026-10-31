<?php
/**
 * Contact form handler for the Contact Form block (blocks/contact-form).
 *
 * The block posts to admin-post.php. Recipients are read server-side from the
 * block's own attributes on the page the form was submitted from (the visitor
 * only ever posts a topic index, never an address). Spam protection: nonce,
 * honeypot, minimum fill time and a per-visitor rate limit.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Find the Contact Form block's attributes in a post.
 *
 * @param int $post_id Post ID.
 * @return array|null
 */
function lc_mdsx2026_find_contact_block( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return null;
	}
	$stack = parse_blocks( $post->post_content );
	while ( $stack ) {
		$block = array_shift( $stack );
		if ( 'lc-mdsx2026/contact-form' === $block['blockName'] ) {
			return $block['attrs'];
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$stack = array_merge( $stack, $block['innerBlocks'] );
		}
	}
	return null;
}

/**
 * Redirect back to the form with a status flag.
 *
 * @param int    $post_id Page the form is on.
 * @param string $status  'sent' or an error code.
 * @param string $topic   Topic label, for conversion tracking on success.
 * @return void
 */
function lc_mdsx2026_contact_redirect( $post_id, $status, $topic = '' ) {
	$args = array( 'contact' => $status );
	if ( $topic ) {
		$args['topic'] = $topic;
	}
	wp_safe_redirect( add_query_arg( $args, get_permalink( $post_id ) ) . '#contact-form' );
	exit;
}

/**
 * Handle a submission.
 *
 * @return void
 */
function lc_mdsx2026_handle_contact_form() {
	$post_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified below.
	if ( ! $post_id || ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'lc_mdsx2026_contact' ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}

	// Honeypot filled, or submitted faster than a person can type: pretend success.
	$started = isset( $_POST['started'] ) ? absint( $_POST['started'] ) : 0;
	if ( ! empty( $_POST['website'] ) || ( $started && time() - $started < 4 ) ) {
		lc_mdsx2026_contact_redirect( $post_id, 'sent' );
	}

	$ip_key = 'lc_mdsx2026_contact_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	if ( (int) get_transient( $ip_key ) >= 5 ) {
		lc_mdsx2026_contact_redirect( $post_id, 'limit' );
	}

	$name    = isset( $_POST['your_name'] ) ? sanitize_text_field( wp_unslash( $_POST['your_name'] ) ) : '';
	$company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
	$email   = isset( $_POST['your_email'] ) ? sanitize_email( wp_unslash( $_POST['your_email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$topic_i = isset( $_POST['topic'] ) ? absint( $_POST['topic'] ) : 0;

	if ( '' === $name || ! is_email( $email ) || '' === $message || empty( $_POST['consent'] ) ) {
		lc_mdsx2026_contact_redirect( $post_id, 'invalid' );
	}
	if ( preg_match_all( '#https?://#i', $message ) > 2 ) {
		lc_mdsx2026_contact_redirect( $post_id, 'sent' ); // Link-heavy: almost certainly spam.
	}

	$attrs  = lc_mdsx2026_find_contact_block( $post_id );
	$routes = is_array( $attrs ) && ! empty( $attrs['routes'] ) ? array_values( $attrs['routes'] ) : array();
	$route  = $routes[ $topic_i ] ?? ( $routes[0] ?? array() );
	$topic  = isset( $route['label'] ) ? $route['label'] : __( 'Website enquiry', 'lc-mdsx2026' );

	$to = array();
	foreach ( explode( ',', isset( $route['emails'] ) ? $route['emails'] : '' ) as $candidate ) {
		$candidate = sanitize_email( trim( $candidate ) );
		if ( is_email( $candidate ) ) {
			$to[] = $candidate;
		}
	}
	if ( ! $to ) {
		$fallback = lc_mdsx2026_get_setting( 'email' );
		$to[]     = is_email( $fallback ) ? $fallback : get_option( 'admin_email' );
	}

	/* translators: %s: enquiry topic. */
	$subject = sprintf( __( 'Website enquiry: %s', 'lc-mdsx2026' ), $topic );
	$body    = implode(
		"\n",
		array(
			'Topic: ' . $topic,
			'Name: ' . $name,
			'Company: ' . $company,
			'Email: ' . $email,
			'Phone: ' . $phone,
			'',
			$message,
			'',
			'Sent from ' . get_permalink( $post_id ),
		)
	);
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );
	set_transient( $ip_key, (int) get_transient( $ip_key ) + 1, HOUR_IN_SECONDS );

	if ( ! $sent ) {
		lc_mdsx2026_contact_redirect( $post_id, 'failed' );
	}
	lc_mdsx2026_contact_redirect( $post_id, 'sent', $topic );
}
add_action( 'admin_post_nopriv_lc_mdsx2026_contact', 'lc_mdsx2026_handle_contact_form' );
add_action( 'admin_post_lc_mdsx2026_contact', 'lc_mdsx2026_handle_contact_form' );
