<?php
/**
 * Block template for Stage: Design (drawings grid): the first image spans two rows, up to four more beside it.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$stage  = lc_mdsx2026_next_stage( __( 'Design', 'lc-mdsx2026' ) );
$images = array_slice( array_values( array_filter( (array) ( $attributes['images'] ?? array() ), static fn( $r ) => ! empty( $r['imageUrl'] ) || ! empty( $r['image'] ) ) ), 0, 5 );
if ( ! $images ) {
	return;
}
?>
<section <?php echo lc_mdsx2026_stage_attrs( $stage, 'stage-drawings', 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php lc_mdsx2026_stage_head( $stage, $attributes['caption'] ?? '' ); ?>
		<div class="drawings drawings--<?php echo (int) count( $images ); ?>">
			<?php foreach ( $images as $i => $img ) { ?>
				<figure class="reveal" style="--i:<?php echo (int) min( $i, 3 ); ?>">
					<?php lc_mdsx2026_lb_image( (int) ( $img['image'] ?? 0 ), $img['imageUrl'] ?? '', $img['alt'] ?? '', $stage['label'], 0 === $i ? '(min-width: 861px) 45vw, 100vw' : '(min-width: 861px) 28vw, 50vw' ); ?>
					<?php if ( ! empty( $img['caption'] ) ) { ?>
						<span class="stage-cap"><?php echo esc_html( $img['caption'] ); ?></span>
					<?php } ?>
				</figure>
			<?php } ?>
		</div>
	</div>
</section>
