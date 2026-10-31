<?php
/**
 * Block template for Contact Form. Submissions are handled by
 * inc/contact-form.php (admin-post.php), which redirects back here with
 * ?contact=sent|invalid|limit|failed.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$routes  = array_values( array_filter( (array) ( $attributes['routes'] ?? array() ), static fn( $r ) => ! empty( $r['label'] ) ) );
$status  = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
$topic   = isset( $_GET['topic'] ) ? sanitize_text_field( wp_unslash( $_GET['topic'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
$phone   = $attributes['phone'] ?? '';
$email   = $attributes['email'] ?? '';

$errors = array(
	'invalid' => __( 'Please check your name, email and message, and tick the box to agree. Then send it again.', 'lc-mdsx2026' ),
	'limit'   => __( 'You have sent several messages in a short time. Please try again later, or email us directly.', 'lc-mdsx2026' ),
	'failed'  => __( 'Sorry, your message could not be sent. Please email us directly instead.', 'lc-mdsx2026' ),
);
?>
<section <?php echo lc_mdsx2026_wrapper( 'contact-form section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?> id="contact-form">
	<div class="container contact-form__grid">
		<div class="contact-form__details">
			<?php if ( ! empty( $attributes['heading'] ) ) { ?>
				<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
			<?php } ?>
			<?php if ( ! empty( $attributes['intro'] ) ) { ?>
				<div class="t-lede reveal"><?php echo wp_kses_post( $attributes['intro'] ); ?></div>
			<?php } ?>
			<address class="contact-form__address reveal" style="--i:1">
				<?php if ( ! empty( $attributes['address'] ) ) { ?>
					<p><?php echo nl2br( esc_html( $attributes['address'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br of escaped text. ?></p>
				<?php } ?>
				<?php if ( $phone ) { ?>
					<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
				<?php } ?>
				<?php if ( $email ) { ?>
					<p><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
				<?php } ?>
			</address>
		</div>

		<div class="contact-form__panel">
			<?php if ( 'sent' === $status ) { ?>
				<div class="contact-form__notice is-sent" role="status" tabindex="-1" data-contact-notice>
					<p><?php echo esc_html( $attributes['success'] ?? __( 'Thank you. We have your message and will reply soon.', 'lc-mdsx2026' ) ); ?></p>
				</div>
				<script>
					window.dataLayer = window.dataLayer || [];
					window.dataLayer.push( { event: 'generate_lead', form_id: 'contact', enquiry_topic: <?php echo wp_json_encode( $topic ); ?> } );
				</script>
			<?php } else { ?>
				<?php if ( isset( $errors[ $status ] ) ) { ?>
					<div class="contact-form__notice is-error" role="alert" tabindex="-1" data-contact-notice>
						<p><?php echo esc_html( $errors[ $status ] ); ?></p>
					</div>
				<?php } ?>
				<form class="contact-form__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="lc_mdsx2026_contact">
					<input type="hidden" name="page_id" value="<?php echo (int) get_the_ID(); ?>">
					<input type="hidden" name="started" value="<?php echo (int) time(); ?>">
					<?php wp_nonce_field( 'lc_mdsx2026_contact' ); ?>
					<div class="contact-form__trap" aria-hidden="true">
						<label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
					</div>

					<div class="contact-form__row">
						<p>
							<label for="cf-name"><?php esc_html_e( 'Your name', 'lc-mdsx2026' ); ?> <span aria-hidden="true">*</span></label>
							<input id="cf-name" name="your_name" type="text" autocomplete="name" required>
						</p>
						<p>
							<label for="cf-company"><?php esc_html_e( 'Company or organization', 'lc-mdsx2026' ); ?></label>
							<input id="cf-company" name="company" type="text" autocomplete="organization">
						</p>
					</div>
					<div class="contact-form__row">
						<p>
							<label for="cf-email"><?php esc_html_e( 'Email', 'lc-mdsx2026' ); ?> <span aria-hidden="true">*</span></label>
							<input id="cf-email" name="your_email" type="email" autocomplete="email" required>
						</p>
						<p>
							<label for="cf-phone"><?php esc_html_e( 'Phone', 'lc-mdsx2026' ); ?></label>
							<input id="cf-phone" name="phone" type="tel" autocomplete="tel">
						</p>
					</div>
					<?php if ( count( $routes ) > 1 ) { ?>
						<p>
							<label for="cf-topic"><?php esc_html_e( 'I would like to talk about', 'lc-mdsx2026' ); ?></label>
							<select id="cf-topic" name="topic">
								<?php foreach ( $routes as $i => $route ) { ?>
									<option value="<?php echo (int) $i; ?>"><?php echo esc_html( $route['label'] ); ?></option>
								<?php } ?>
							</select>
						</p>
					<?php } ?>
					<p>
						<label for="cf-message"><?php esc_html_e( 'Your message', 'lc-mdsx2026' ); ?> <span aria-hidden="true">*</span></label>
						<textarea id="cf-message" name="message" rows="6" required></textarea>
					</p>
					<p class="contact-form__consent">
						<label>
							<input type="checkbox" name="consent" value="1" required>
							<span>
								<?php
								printf(
									/* translators: %s: link to the privacy policy. */
									esc_html__( 'I agree to MDSX storing my details to reply to this enquiry. See the %s.', 'lc-mdsx2026' ),
									'<a href="' . esc_url( lc_mdsx2026_page_url( 'privacy-policy' ) ) . '">' . esc_html__( 'privacy policy', 'lc-mdsx2026' ) . '</a>'
								);
								?>
							</span>
						</label>
					</p>
					<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Send message', 'lc-mdsx2026' ); ?><i class="arrow" aria-hidden="true"></i></button>
				</form>
			<?php } ?>
		</div>
	</div>
</section>
