<?php
/**
 * Block template for Hero Reel. Slides are the chosen portfolio projects'
 * featured images, in the order picked; the card tracks the current one
 * (src/js/hero-reel.js).
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$slides = array();
foreach ( array_unique( array_filter( array_map( 'intval', (array) ( $attributes['projects'] ?? array() ) ) ) ) as $project_id ) {
	if ( 'publish' === get_post_status( $project_id ) && has_post_thumbnail( $project_id ) ) {
		$slides[] = $project_id;
	}
}

$title     = $attributes['title'] ?? '';
$lede      = $attributes['lede'] ?? '';
$show_card = $attributes['showCard'] ?? true;
$first     = $slides ? $slides[0] : 0;
?>
<section <?php echo lc_mdsx2026_wrapper( 'hero-reel', 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?> aria-label="<?php esc_attr_e( 'Featured work', 'lc-mdsx2026' ); ?>">
	<div class="hero-reel__slides">
		<?php foreach ( $slides as $i => $project_id ) { ?>
			<figure class="hero-reel__slide<?php echo 0 === $i ? ' is-active' : ''; ?>"
				data-title="<?php echo esc_attr( get_the_title( $project_id ) ); ?>"
				data-meta="<?php echo esc_attr( get_the_excerpt( $project_id ) ); ?>"
				data-url="<?php echo esc_url( get_permalink( $project_id ) ); ?>">
				<?php echo get_the_post_thumbnail( $project_id, 'full', array( 'alt' => get_the_title( $project_id ), 'loading' => 0 === $i ? 'eager' : 'lazy', 'sizes' => '100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup. ?>
			</figure>
		<?php } ?>
	</div>
	<div class="hero-reel__copy container">
		<?php if ( $title ) { ?>
			<h1 class="baseline reveal"><?php echo esc_html( $title ); ?></h1>
		<?php } ?>
		<?php if ( $lede ) { ?>
			<p class="t-lede reveal" style="--i:1"><?php echo esc_html( $lede ); ?></p>
		<?php } ?>
		<div class="hero-reel__actions reveal" style="--i:2">
			<?php lc_mdsx2026_link( $attributes['cta1Text'] ?? '', $attributes['cta1Url'] ?? '', 'btn btn--primary' ); ?>
			<?php lc_mdsx2026_link( $attributes['cta2Text'] ?? '', $attributes['cta2Url'] ?? '', 'textlink' ); ?>
		</div>
	</div>
	<?php if ( $show_card && $first ) { ?>
		<aside class="hero-reel__card reveal" style="--i:3" aria-live="polite" data-reel-card>
			<div class="hero-reel__card-inner">
				<img class="hero-reel__card-logo" src="<?php echo esc_url( lc_mdsx2026_img_url( 'logos/mdsx-badge-primary.svg' ) ); ?>" alt="" width="64" height="46">
				<small><?php echo esc_html( $attributes['cardLabel'] ?? __( 'Now showing', 'lc-mdsx2026' ) ); ?></small>
				<strong><a href="<?php echo esc_url( get_permalink( $first ) ); ?>" data-card-title><?php echo esc_html( get_the_title( $first ) ); ?></a></strong>
				<span data-card-meta><?php echo esc_html( get_the_excerpt( $first ) ); ?></span>
				<div class="hero-reel__bar" aria-hidden="true"><i data-card-bar></i></div>
			</div>
		</aside>
	<?php } ?>
	<?php if ( count( $slides ) > 1 ) { ?>
		<button class="hero-reel__pause" type="button" data-reel-pause aria-pressed="false">
			<span class="visually-hidden"><?php esc_html_e( 'Pause background images', 'lc-mdsx2026' ); ?></span>
			<svg width="14" height="16" viewBox="0 0 14 16" aria-hidden="true"><rect class="p1" x="0" y="0" width="4" height="16"/><rect class="p2" x="9" y="0" width="4" height="16"/></svg>
		</button>
	<?php } ?>
</section>
