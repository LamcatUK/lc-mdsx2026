<?php
/**
 * Block template for People Strip.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['surface'] ?? 'nero' );
$people  = array_values( array_filter( (array) ( $attributes['people'] ?? array() ), static fn( $r ) => ! lc_mdsx2026_row_empty( $r ) ) );
$press   = lc_mdsx2026_lines( $attributes['press'] ?? '' );
?>
<section <?php echo lc_mdsx2026_wrapper( 'people-strip section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<div class="people-strip__list">
			<?php foreach ( $people as $i => $p ) { ?>
				<article class="people-strip__person reveal" style="--i:<?php echo (int) $i; ?>">
					<div class="people-strip__photo"><?php lc_mdsx2026_image( (int) ( $p['photo'] ?? 0 ), $p['photoUrl'] ?? '', $p['name'] ?? '', 'medium' ); ?></div>
					<div>
						<h3><?php echo esc_html( $p['name'] ?? '' ); ?></h3>
						<?php if ( ! empty( $p['role'] ) ) { ?>
							<p class="t-label"><?php echo esc_html( $p['role'] ); ?></p>
						<?php } ?>
						<?php if ( ! empty( $p['bio'] ) ) { ?>
							<p class="people-strip__bio"><?php echo esc_html( $p['bio'] ); ?></p>
						<?php } ?>
						<?php lc_mdsx2026_link( $p['linkText'] ?? '', $p['link'] ?? '', 'textlink' ); ?>
					</div>
				</article>
			<?php } ?>
		</div>
		<?php if ( $press ) { ?>
			<ul class="people-strip__press">
				<?php foreach ( $press as $name ) { ?>
					<li><?php echo esc_html( $name ); ?></li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>
</section>
