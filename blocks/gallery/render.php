<?php
/**
 * Block template for Gallery Row.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$layout = $attributes['layout'] ?? 'three';
$images = array_values( array_filter( (array) ( $attributes['images'] ?? array() ), static fn( $r ) => ! empty( $r['imageUrl'] ) || ! empty( $r['image'] ) ) );
if ( ! $images ) {
	return;
}
$limit  = array( 'three' => 3, 'two' => 2, 'full' => 1 );
$images = array_slice( $images, 0, $limit[ $layout ] ?? 3 );
?>
<section <?php echo lc_mdsx2026_wrapper( 'gallery gallery--' . sanitize_html_class( $layout ), 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container gallery__row">
		<?php foreach ( $images as $i => $img ) { ?>
			<figure class="reveal" style="--i:<?php echo (int) $i; ?>"><?php lc_mdsx2026_image( (int) ( $img['image'] ?? 0 ), $img['imageUrl'] ?? '', $img['alt'] ?? '', 'full' === $layout ? 'full' : 'large' ); ?></figure>
		<?php } ?>
	</div>
</section>
