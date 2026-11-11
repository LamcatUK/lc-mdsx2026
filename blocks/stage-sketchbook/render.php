<?php
/**
 * Block template for Stage: Concept (sketchbook strip). On desktop src/js/sketchbook.js pins the strip
 * and scrubs it sideways with the page scroll; without JS, on mobile or with reduced motion it is a
 * native scroll-snap row.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$stage  = lc_mdsx2026_next_stage( __( 'Concept', 'lc-mdsx2026' ) );
$images = array_values( array_filter( (array) ( $attributes['images'] ?? array() ), static fn( $r ) => ! empty( $r['imageUrl'] ) || ! empty( $r['image'] ) ) );
if ( ! $images ) {
	return;
}
$count = count( $images );

// A lone portrait or square sketch stays capped and centred; anything landscape fills the container like the Design images.
$tall = false;
if ( 1 === $count ) {
	$meta = ! empty( $images[0]['image'] ) ? wp_get_attachment_metadata( (int) $images[0]['image'] ) : array();
	$tall = ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && $meta['width'] / $meta['height'] < 1.2;
}
?>
<section <?php echo lc_mdsx2026_stage_attrs( $stage, 'stage-sketchbook', 'solitude' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container"><?php lc_mdsx2026_stage_head( $stage, $attributes['caption'] ?? '' ); ?></div>
	<div class="sketchbook__track<?php echo $count < 3 ? ' sketchbook__track--few sketchbook__track--n' . (int) $count . ( $tall ? ' sketchbook__track--tall' : '' ) : ''; ?>" data-sketchbook>
		<div class="sketchbook__pin">
			<div class="sketchbook__scroller" data-sketchbook-scroller<?php echo $count >= 3 ? ' tabindex="0" role="group" aria-label="' . esc_attr( $stage['label'] ) . '"' : ''; ?>>
				<div class="sketchbook__strip" data-sketchbook-strip>
					<?php foreach ( $images as $i => $img ) { ?>
						<figure>
							<?php lc_mdsx2026_lb_image( (int) ( $img['image'] ?? 0 ), $img['imageUrl'] ?? '', $img['alt'] ?? '', $stage['label'], '(min-width: 761px) 60vh, 85vw', 'portfolio-1024' ); ?>
							<?php if ( $count >= 3 || ! empty( $img['caption'] ) ) { ?>
								<figcaption><?php if ( $count >= 3 ) { ?><b><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></b><?php } ?><?php echo esc_html( $img['caption'] ?? '' ); ?></figcaption>
							<?php } ?>
						</figure>
					<?php } ?>
				</div>
			</div>
			<div class="container sketchbook__meta"<?php echo $count < 3 ? ' hidden' : ''; ?>>
				<span data-sketchbook-count aria-hidden="true">01 / <?php echo esc_html( str_pad( (string) $count, 2, '0', STR_PAD_LEFT ) ); ?></span>
				<span class="sketchbook__track-bar" aria-hidden="true"><i data-sketchbook-bar></i></span>
				<span class="sketchbook__hint" aria-hidden="true"><span class="sketchbook__hint-d"><?php esc_html_e( 'Drag or scroll', 'lc-mdsx2026' ); ?></span><span class="sketchbook__hint-m"><?php esc_html_e( 'Swipe', 'lc-mdsx2026' ); ?></span></span>
			</div>
		</div>
	</div>
</section>
