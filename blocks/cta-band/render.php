<?php
/**
 * Block template for CTA Band.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'crusta', 'crusta' );
?>
<section <?php echo lc_mdsx2026_wrapper( 'cta-band section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container cta-band__inner">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<div class="reveal" style="--i:1"><?php lc_mdsx2026_link( $attributes['ctaText'] ?? '', $attributes['ctaUrl'] ?? '', lc_mdsx2026_btn_class( $surface ) . ' btn--l' ); ?></div>
	</div>
</section>
