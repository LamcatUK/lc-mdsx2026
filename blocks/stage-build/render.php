<?php
/**
 * Block template for Stage: Build (behind the scenes). A 12-column grid with staggered offsets and tape
 * labels; the span and offset cycle automatically, and a portrait still or clip is always a narrow tall slot.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$stage = lc_mdsx2026_next_stage( __( 'Build', 'lc-mdsx2026' ) );
$items = array_slice(
	array_values(
		array_filter(
			(array) ( $attributes['items'] ?? array() ),
			static fn( $r ) => ! empty( $r['imageUrl'] ) || ! empty( $r['image'] ) || ! empty( $r['webm'] ) || ! empty( $r['mp4'] )
		)
	),
	0,
	12
);
if ( ! $items ) {
	return;
}

// Span (of 12) per position in a repeating run of six, and which slots drop 72px.
$spans   = array( 7, 5, 3, 3, 3, 3 );
$dropped = array( 1, 3, 5 );
?>
<section <?php echo lc_mdsx2026_stage_attrs( $stage, 'stage-build', 'charcoal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php lc_mdsx2026_stage_head( $stage, $attributes['caption'] ?? '' ); ?>
		<div class="bts">
			<?php
			foreach ( $items as $i => $item ) {
				$pos    = $i % 6;
				$span   = $spans[ $pos ];
				$is_clip = ! empty( $item['webm'] ) || ! empty( $item['mp4'] );
				$ratio   = $is_clip ? lc_mdsx2026_video_ratio( $item ) : 0;
				if ( $ratio > 2.2 ) {
					$span = 12;
				}
				$meta   = ! empty( $item['image'] ) ? wp_get_attachment_metadata( (int) $item['image'] ) : array();
				$portrait = $is_clip || ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && $meta['height'] > $meta['width'] );
				$shape  = ( $portrait || ( $span <= 4 ) ) ? 'tall' : 'wide';
				$class  = 'bts__item w' . $span . ' ' . $shape . ( in_array( $pos, $dropped, true ) ? ' off' : '' ) . ' reveal';
				?>
				<figure class="<?php echo esc_attr( $class ); ?>" style="--i:<?php echo (int) ( $i % 3 ); ?>">
					<?php
					if ( $is_clip ) {
						lc_mdsx2026_video(
							array(
								'webm'      => $item['webm'] ?? 0,
								'mp4'       => $item['mp4'] ?? 0,
								'poster'    => $item['image'] ?? 0,
								'posterUrl' => $item['imageUrl'] ?? '',
								'label'     => $item['alt'] ?? '',
								'lbStage'   => $stage['label'],
							),
							'loop'
						);
					} else {
						lc_mdsx2026_lb_image( (int) ( $item['image'] ?? 0 ), $item['imageUrl'] ?? '', $item['alt'] ?? '', $stage['label'], '(min-width: 861px) ' . ( $span * 8 ) . 'vw, 50vw' );
					}
					?>
					<?php if ( ! empty( $item['tape'] ) ) { ?>
						<span class="tape<?php echo $is_clip ? ' tape--live' : ''; ?>"><?php echo esc_html( $item['tape'] ); ?></span>
					<?php } ?>
				</figure>
			<?php } ?>
		</div>
	</div>
</section>
