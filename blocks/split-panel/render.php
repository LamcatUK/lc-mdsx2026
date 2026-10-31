<?php
/**
 * Block template for Split Panel.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['surface'] ?? 'sea', 'sea' );
$right   = ! empty( $attributes['imageRight'] );
$has_img = ! empty( $attributes['imageUrl'] ) || ! empty( $attributes['imageId'] );
?>
<section <?php echo lc_mdsx2026_wrapper( 'split-panel section', $surface, $right ? '' : 'is-image-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container split-panel__grid">
		<div class="split-panel__copy reveal">
			<?php if ( ! empty( $attributes['heading'] ) ) { ?>
				<h2><?php echo esc_html( $attributes['heading'] ); ?></h2>
			<?php } ?>
			<?php if ( ! empty( $attributes['body'] ) ) { ?>
				<div class="split-panel__body"><?php echo wp_kses_post( $attributes['body'] ); ?></div>
			<?php } ?>
			<?php lc_mdsx2026_link( $attributes['ctaText'] ?? '', $attributes['ctaUrl'] ?? '', 'textlink' ); ?>
		</div>
		<?php if ( $has_img ) { ?>
			<div class="split-panel__image pg reveal" style="--i:1"><div><?php lc_mdsx2026_image( (int) $attributes['imageId'], $attributes['imageUrl'], $attributes['imageAlt'] ?? '', 'large' ); ?></div></div>
		<?php } ?>
	</div>
</section>
