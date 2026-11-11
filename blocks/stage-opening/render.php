<?php
/**
 * Block template for Stage: Opening day. Three optional parts (cinematic image, "on the floor" reel, mosaic),
 * or, for a project still in development, an "Opening [date]" panel in their place.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$stage    = lc_mdsx2026_next_stage( __( 'Opening day', 'lc-mdsx2026' ) );
$upcoming = 'upcoming' === ( $attributes['state'] ?? 'open' );
$cine_id  = (int) ( $attributes['cineId'] ?? 0 );
$cine_url = $attributes['cineUrl'] ?? '';
$reel     = array_slice( array_values( array_filter( (array) ( $attributes['reel'] ?? array() ), static fn( $r ) => ! empty( $r['imageUrl'] ) || ! empty( $r['image'] ) || ! empty( $r['webm'] ) || ! empty( $r['mp4'] ) ) ), 0, 8 );
$mosaic   = array_slice( array_values( array_filter( (array) ( $attributes['mosaic'] ?? array() ), static fn( $r ) => ! empty( $r['imageUrl'] ) || ! empty( $r['image'] ) ) ), 0, 8 );
$heading  = $attributes['reelHeading'] ?? '';
?>
<section <?php echo lc_mdsx2026_stage_attrs( $stage, 'stage-opening', 'nero', $upcoming ? 'is-upcoming' : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container"><?php lc_mdsx2026_stage_head( $stage, $attributes['caption'] ?? '' ); ?></div>

	<?php if ( $upcoming ) { ?>
		<div class="container opening__panel reveal">
			<span class="opening__marker" aria-hidden="true"></span>
			<div>
				<p class="opening__date t-h2"><?php /* translators: %s: opening date. */ printf( esc_html__( 'Opening %s', 'lc-mdsx2026' ), esc_html( $attributes['openingDate'] ?? '[date to be confirmed]' ) ); ?></p>
				<?php if ( ! empty( $attributes['upcomingLine'] ) ) { ?>
					<p class="t-lede"><?php echo esc_html( $attributes['upcomingLine'] ); ?></p>
				<?php } ?>
				<?php lc_mdsx2026_link( $attributes['ctaText'] ?? '', $attributes['ctaUrl'] ?? '', 'btn btn--primary' ); ?>
			</div>
		</div>
	<?php } else { ?>

	<?php if ( $cine_id || $cine_url ) { ?>
		<div class="cine" data-cine>
			<?php
			$attrs = lc_mdsx2026_lb_attrs( $stage['label'] );
			lc_mdsx2026_image( $cine_id, $cine_url, $attributes['cineAlt'] ?? '', 'portfolio-2400', 'lazy', false, array_merge( $attrs, array( 'sizes' => '100vw', 'data-cine-img' => '' ) ) );
			?>
			<?php if ( ! empty( $attributes['cineLine'] ) ) { ?>
				<div class="cine__copy container"><p><?php echo esc_html( $attributes['cineLine'] ); ?></p></div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if ( $reel ) { ?>
		<div class="floor">
			<div class="container floor__head">
				<?php if ( $heading ) { ?>
					<h3><?php echo esc_html( $heading ); ?></h3>
				<?php } ?>
				<div class="reel-ctrl">
					<button type="button" data-reel="-1" aria-label="<?php esc_attr_e( 'Previous', 'lc-mdsx2026' ); ?>"><i aria-hidden="true"></i></button>
					<button type="button" data-reel="1" aria-label="<?php esc_attr_e( 'Next', 'lc-mdsx2026' ); ?>"><i aria-hidden="true"></i></button>
				</div>
			</div>
			<div class="reel" data-reel-row tabindex="0" role="group" aria-label="<?php echo esc_attr( $heading ? $heading : $stage['label'] ); ?>">
				<?php foreach ( $reel as $item ) { ?>
					<?php
					$is_clip = ! empty( $item['webm'] ) || ! empty( $item['mp4'] );
					$ratio   = $is_clip ? lc_mdsx2026_video_ratio( $item ) : 0;
					$shape   = $ratio > 2.2 ? ' reel__clip--wide' : '';
					?>
					<figure<?php echo $is_clip ? ' class="reel__clip' . $shape . '"' : ''; ?>>
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
							?>
							<span class="tape tape--live"><?php echo esc_html( ! empty( $item['tape'] ) ? $item['tape'] : __( 'Live', 'lc-mdsx2026' ) ); ?></span>
						<?php } else { ?>
							<?php lc_mdsx2026_lb_image( (int) ( $item['image'] ?? 0 ), $item['imageUrl'] ?? '', $item['alt'] ?? '', $stage['label'], '(min-width: 861px) 60vh, 85vw', 'portfolio-1024' ); ?>
						<?php } ?>
					</figure>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php
	if ( $mosaic ) {
		// Slots a to e from the mockup, spanning a 6-column grid. The last row is stretched to fill, so a short list never leaves a gap.
		$slots = array(
			array( 4, 2 ),
			array( 2, 2 ),
			array( 2, 1 ),
			array( 2, 1 ),
			array( 2, 1 ),
			array( 3, 2 ),
			array( 3, 2 ),
			array( 6, 2 ),
		);
		$layout = array();
		$fill   = 0;
		foreach ( $mosaic as $i => $tile ) {
			$layout[ $i ] = $slots[ $i ];
			$fill        += $slots[ $i ][0];
			if ( $fill >= 6 ) {
				$fill = 0;
			}
		}
		if ( $fill && $layout ) {
			$last                   = count( $layout ) - 1;
			$layout[ $last ][0]    += 6 - $fill;
		}
		?>
		<div class="container mosaic">
			<?php foreach ( $mosaic as $i => $tile ) { ?>
				<figure class="reveal" style="--i:<?php echo (int) ( $i % 3 ); ?>;grid-column:span <?php echo (int) $layout[ $i ][0]; ?>;grid-row:span <?php echo (int) $layout[ $i ][1]; ?>">
					<?php lc_mdsx2026_lb_image( (int) ( $tile['image'] ?? 0 ), $tile['imageUrl'] ?? '', $tile['alt'] ?? '', $stage['label'], '(min-width: 861px) ' . ( $layout[ $i ][0] * 16 ) . 'vw, 100vw' ); ?>
				</figure>
			<?php } ?>
		</div>
	<?php } ?>
	<?php } ?>
</section>
