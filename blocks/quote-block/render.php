<?php
/**
 * Block template for Quote.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['surface'] ?? 'nero' );
if ( empty( $attributes['quote'] ) ) {
	return;
}
?>
<section <?php echo lc_mdsx2026_wrapper( 'quote-block section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<figure class="reveal">
			<blockquote><?php echo esc_html( $attributes['quote'] ); ?></blockquote>
			<?php if ( ! empty( $attributes['cite'] ) ) { ?>
				<figcaption><?php echo esc_html( $attributes['cite'] ); ?></figcaption>
			<?php } ?>
		</figure>
	</div>
</section>
