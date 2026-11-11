<?php
/**
 * Block template for Compare Slider (idea to reality). Pointer drag anywhere, arrow keys on the grip,
 * and one sweep on first entry (src/js/compare.js).
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$has_before = ! empty( $attributes['beforeId'] ) || ! empty( $attributes['beforeUrl'] );
$has_after  = ! empty( $attributes['afterId'] ) || ! empty( $attributes['afterUrl'] );
if ( ! $has_before || ! $has_after ) {
	return;
}
$sizes = '(min-width: 1320px) 1320px, 100vw';
?>
<section <?php echo lc_mdsx2026_wrapper( 'compare-slider section', 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<div class="compare-slider__head reveal">
			<?php if ( ! empty( $attributes['heading'] ) ) { ?>
				<h2 class="baseline"><?php echo esc_html( $attributes['heading'] ); ?></h2>
			<?php } ?>
			<?php if ( ! empty( $attributes['intro'] ) ) { ?>
				<p><?php echo esc_html( $attributes['intro'] ); ?></p>
			<?php } ?>
		</div>
		<div class="compare" data-compare role="group" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: before label, 2: after label. */ __( '%1$s compared with %2$s', 'lc-mdsx2026' ), $attributes['beforeLabel'] ?? '', $attributes['afterLabel'] ?? '' ) ); ?>">
			<?php lc_mdsx2026_image( (int) $attributes['afterId'], $attributes['afterUrl'] ?? '', $attributes['afterAlt'] ?? '', 'portfolio-2400', 'lazy', false, array( 'sizes' => $sizes ) ); ?>
			<div class="compare__before">
				<?php lc_mdsx2026_image( (int) $attributes['beforeId'], $attributes['beforeUrl'] ?? '', $attributes['beforeAlt'] ?? '', 'portfolio-2400', 'lazy', false, array( 'sizes' => $sizes ) ); ?>
			</div>
			<?php if ( ! empty( $attributes['beforeLabel'] ) ) { ?>
				<span class="compare__label compare__label--l" aria-hidden="true"><?php echo esc_html( $attributes['beforeLabel'] ); ?></span>
			<?php } ?>
			<?php if ( ! empty( $attributes['afterLabel'] ) ) { ?>
				<span class="compare__label compare__label--r" aria-hidden="true"><?php echo esc_html( $attributes['afterLabel'] ); ?></span>
			<?php } ?>
			<div class="compare__handle">
				<button class="compare__grip" type="button" role="slider" aria-label="<?php esc_attr_e( 'Move comparison slider', 'lc-mdsx2026' ); ?>" aria-orientation="horizontal" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50" data-compare-grip><i></i><i></i></button>
			</div>
		</div>
	</div>
</section>
