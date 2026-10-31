<?php
/**
 * Block template for Text Chapter.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['surface'] ?? 'nero' );
?>
<section <?php echo lc_mdsx2026_wrapper( 'text-chapter section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container text-chapter__grid">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<div class="text-chapter__body reveal" style="--i:1"><?php echo wp_kses_post( $attributes['body'] ?? '' ); ?></div>
	</div>
</section>
