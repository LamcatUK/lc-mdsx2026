<?php
/**
 * Block template for Stage: Community (also used, labelled "Research", for location research).
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$stage  = lc_mdsx2026_next_stage( __( 'Community', 'lc-mdsx2026' ) );
$images = array_slice( array_values( array_filter( (array) ( $attributes['images'] ?? array() ), static fn( $r ) => ! empty( $r['imageUrl'] ) || ! empty( $r['image'] ) ) ), 0, 3 );
$quote  = $attributes['quote'] ?? '';
if ( ! $images && ! $quote ) {
	return;
}
?>
<section <?php echo lc_mdsx2026_stage_attrs( $stage, 'stage-community', 'sea' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php lc_mdsx2026_stage_head( $stage, $attributes['caption'] ?? '' ); ?>
		<div class="community__grid">
			<?php if ( $quote ) { ?>
				<blockquote class="community__quote reveal">
					<p><?php echo esc_html( $quote ); ?></p>
					<?php if ( ! empty( $attributes['cite'] ) ) { ?>
						<footer class="t-meta"><?php echo esc_html( $attributes['cite'] ); ?></footer>
					<?php } ?>
				</blockquote>
			<?php } ?>
			<?php if ( $images ) { ?>
				<div class="community__pics">
					<?php foreach ( $images as $i => $img ) { ?>
						<?php if ( 0 === $i ) { ?>
							<div class="community__lead pg pg--half reveal"><div><?php lc_mdsx2026_lb_image( (int) ( $img['image'] ?? 0 ), $img['imageUrl'] ?? '', $img['alt'] ?? '', $stage['label'], '(min-width: 861px) 45vw, 100vw' ); ?></div></div>
						<?php } else { ?>
							<div class="reveal" style="--i:<?php echo (int) $i; ?>"><?php lc_mdsx2026_lb_image( (int) ( $img['image'] ?? 0 ), $img['imageUrl'] ?? '', $img['alt'] ?? '', $stage['label'], '(min-width: 861px) 22vw, 50vw', 'portfolio-1024' ); ?></div>
						<?php } ?>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
