<?php
/**
 * Block template for Page Hero.
 *
 * Variants: "title" (plain title), "image" (full-bleed image with the title over it) and
 * "split" (the service-pillar hero: copy bottom-left, image full-bleed to the top and right
 * edges with an angled left edge, as in the Concept A service page).
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$variant = $attributes['variant'] ?? 'title';
$title   = $attributes['title'] ?? '';
$title   = $title ? $title : get_the_title();
$intro   = $attributes['intro'] ?? '';
$has_img = ! empty( $attributes['imageUrl'] ) || ! empty( $attributes['imageId'] );
$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );

ob_start();
lc_mdsx2026_breadcrumb( $attributes['crumbText'] ?? '', $attributes['crumbUrl'] ?? '' );
$crumbs = ob_get_clean();

ob_start();
?>
	<?php echo $crumbs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by lc_mdsx2026_breadcrumb(). ?>
	<h1 class="baseline reveal"><?php echo esc_html( $title ); ?></h1>
	<?php if ( $intro ) { ?>
		<div class="page-hero__intro t-lede reveal" style="--i:1"><?php echo wp_kses_post( $intro ); ?></div>
	<?php } ?>
	<?php if ( ! empty( $attributes['ctaUrl'] ) || ! empty( $attributes['cta2Url'] ) ) { ?>
		<div class="page-hero__actions reveal" style="--i:2">
			<?php lc_mdsx2026_link( $attributes['ctaText'] ?? '', $attributes['ctaUrl'] ?? '', 'btn btn--primary' ); ?>
			<?php lc_mdsx2026_link( $attributes['cta2Text'] ?? '', $attributes['cta2Url'] ?? '', 'textlink' ); ?>
		</div>
	<?php } ?>
<?php
$copy = ob_get_clean();
?>
<section <?php echo lc_mdsx2026_wrapper( 'page-hero page-hero--' . $variant, $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<?php if ( 'split' === $variant ) { ?>
		<div class="page-hero__copy"><?php echo $copy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></div>
		<?php if ( $has_img ) { ?>
			<div class="page-hero__image pg"><div><?php lc_mdsx2026_image( (int) ( $attributes['imageId'] ?? 0 ), $attributes['imageUrl'] ?? '', $attributes['imageAlt'] ?? '', 'full', 'eager' ); ?></div></div>
		<?php } ?>
	<?php } else { ?>
		<?php if ( 'image' === $variant && $has_img ) { ?>
			<div class="page-hero__media"><?php lc_mdsx2026_image( (int) ( $attributes['imageId'] ?? 0 ), $attributes['imageUrl'] ?? '', $attributes['imageAlt'] ?? '', 'full', 'eager' ); ?></div>
		<?php } ?>
		<div class="page-hero__inner container">
			<div class="page-hero__copy"><?php echo $copy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></div>
		</div>
	<?php } ?>
</section>
