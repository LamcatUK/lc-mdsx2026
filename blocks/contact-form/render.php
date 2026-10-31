<?php
/**
 * Block template for Contact Form: contact details beside a Contact Form 7 form.
 * The form itself (fields, recipients, messages) lives in Contact > Contact Forms;
 * conversion tracking is in src/js/contact-form.js.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$phone   = $attributes['phone'] ?? '';
$email   = $attributes['email'] ?? '';
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
					<p><a href="tel:<?php echo esc_attr( parse_phone( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
				<?php } ?>
				<?php if ( $email ) { ?>
					<p><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
				<?php } ?>
			</address>
		</div>

		<div class="contact-form__panel">
			<?php
			$form_id = (int) ( $attributes['formId'] ?? 0 );
			$form    = ( $form_id && class_exists( 'WPCF7_ContactForm' ) ) ? WPCF7_ContactForm::get_instance( $form_id ) : null;
			if ( $form ) {
				echo do_shortcode( $form->shortcode() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contact Form 7 output.
			} elseif ( current_user_can( 'edit_posts' ) ) {
				?>
				<p><em><?php esc_html_e( 'Set the Contact Form 7 form ID in this block\'s settings (and make sure Contact Form 7 is active).', 'lc-mdsx2026' ); ?></em></p>
			<?php } ?>
		</div>
	</div>
</section>
