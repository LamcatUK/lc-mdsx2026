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
?>
<section <?php echo lc_mdsx2026_wrapper( 'logo-marquee', 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?> aria-label="<?php echo esc_attr( $attributes['label'] ?? __( 'Clients', 'lc-mdsx2026' ) ); ?>">
	<div class="logo-marquee__track" data-marquee>
		<?php for ( $copy = 0; $copy < 2; $copy++ ) { ?>
			<ul class="logo-marquee__set"<?php echo $copy ? ' aria-hidden="true"' : ''; ?>>
				<?php foreach ( $logos as $logo ) { ?>
					<li><?php lc_mdsx2026_image( (int) ( $logo['image'] ?? 0 ), $logo['imageUrl'] ?? '', $copy ? '' : ( $logo['alt'] ?? '' ), 'medium' ); ?></li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>
</section>
