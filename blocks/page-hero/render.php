<?php
/**
 * Block template for Page Hero.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$variant = $attributes['variant'] ?? 'title';
$title   = $attributes['title'] ?? '';
$title   = $title ? $title : get_the_title();
$intro   = $attributes['intro'] ?? '';
$has_img = ! empty( $attributes['imageUrl'] ) || ! empty( $attributes['imageId'] );
$crumb   = $attributes['crumbText'] ?? '';
$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$crumb_html = '';
if ( $crumb ) {
	$crumb_html = ! empty( $attributes['crumbUrl'] )
		? '<a class="page-hero__crumb" href="' . esc_url( $attributes['crumbUrl'] ) . '">' . esc_html( $crumb ) . '</a>'
		: '<span class="page-hero__crumb">' . esc_html( $crumb ) . '</span>';
}
?>
<section <?php echo lc_mdsx2026_wrapper( 'page-hero page-hero--' . $variant, $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<?php if ( 'image' === $variant && $has_img ) { ?>
		<div class="page-hero__media"><?php lc_mdsx2026_image( (int) $attributes['imageId'], $attributes['imageUrl'], $attributes['imageAlt'] ?? '', 'full', 'eager' ); ?></div>
	<?php } ?>
	<div class="page-hero__inner container">
		<div class="page-hero__copy">
			<?php echo $crumb_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
			<h1 class="baseline reveal"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $intro ) { ?>
				<div class="page-hero__intro t-lede reveal" style="--i:1"><?php echo wp_kses_post( $intro ); ?></div>
			<?php } ?>
			<?php if ( ! empty( $attributes['ctaUrl'] ) ) { ?>
				<div class="reveal" style="--i:2"><?php lc_mdsx2026_link( $attributes['ctaText'] ?? '', $attributes['ctaUrl'], 'btn btn--primary' ); ?></div>
			<?php } ?>
		</div>
		<?php if ( 'split' === $variant && $has_img ) { ?>
			<div class="page-hero__image pg pg--left"><div><?php lc_mdsx2026_image( (int) $attributes['imageId'], $attributes['imageUrl'], $attributes['imageAlt'] ?? '', 'large', 'eager' ); ?></div></div>
		<?php } ?>
	</div>
</section>
