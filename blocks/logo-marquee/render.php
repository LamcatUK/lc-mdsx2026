<?php
/**
 * Block template for Logo Marquee.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$logos = array_values( array_filter( (array) ( $attributes['logos'] ?? array() ), static fn( $r ) => ! empty( $r['imageUrl'] ) || ! empty( $r['image'] ) ) );
if ( ! $logos ) {
	return;
}
$is_grid = 'grid' === ( $attributes['layout'] ?? 'marquee' );
?>
<section <?php echo lc_mdsx2026_wrapper( 'logo-marquee' . ( $is_grid ? ' logo-marquee--grid section' : '' ), 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?> aria-label="<?php echo esc_attr( $attributes['label'] ?? __( 'Clients', 'lc-mdsx2026' ) ); ?>">
	<?php if ( $is_grid ) { ?>
		<div class="container">
			<ul class="logo-marquee__grid">
				<?php foreach ( $logos as $i => $logo ) { ?>
					<?php // Stagger within each row of four, so later rows don't wait on the earlier ones. ?>
					<li class="reveal" style="--i:<?php echo (int) ( $i % 4 ); ?>"><?php lc_mdsx2026_image( (int) ( $logo['image'] ?? 0 ), $logo['imageUrl'] ?? '', $logo['alt'] ?? '', 'medium', 'lazy' ); ?></li>
				<?php } ?>
			</ul>
		</div>
	<?php } else { ?>
	<div class="logo-marquee__track" data-marquee>
		<?php for ( $copy = 0; $copy < 2; $copy++ ) { ?>
			<ul class="logo-marquee__set"<?php echo $copy ? ' aria-hidden="true"' : ''; ?>>
				<?php foreach ( $logos as $logo ) { ?>
					<li><?php lc_mdsx2026_image( (int) ( $logo['image'] ?? 0 ), $logo['imageUrl'] ?? '', $copy ? '' : ( $logo['alt'] ?? '' ), 'medium', 'lazy', $copy ); ?></li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>
	<?php } ?>
</section>
