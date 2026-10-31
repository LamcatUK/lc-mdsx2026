<?php
/**
 * Block template for Hero Reel.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$slides = array_filter( (array) ( $attributes['slides'] ?? array() ), static fn( $r ) => ! empty( $r['imageUrl'] ) || ! empty( $r['image'] ) );
$title  = $attributes['title'] ?? '';
$lede   = $attributes['lede'] ?? '';
?>
<section <?php echo lc_mdsx2026_wrapper( 'hero-reel', 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?> aria-label="<?php esc_attr_e( 'Featured work', 'lc-mdsx2026' ); ?>">
	<div class="hero-reel__slides" data-reel>
		<?php
		foreach ( array_values( $slides ) as $i => $slide ) {
			?>
			<figure class="hero-reel__slide<?php echo 0 === $i ? ' is-active' : ''; ?>">
				<?php lc_mdsx2026_image( (int) ( $slide['image'] ?? 0 ), $slide['imageUrl'] ?? '', $slide['alt'] ?? '', 'full', 0 === $i ? 'eager' : 'lazy' ); ?>
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
	<?php if ( ! empty( $attributes['cardTitle'] ) ) { ?>
		<aside class="hero-reel__card pg" aria-live="polite">
			<div class="hero-reel__card-inner">
				<?php if ( ! empty( $attributes['cardLabel'] ) ) { ?>
					<small><?php echo esc_html( $attributes['cardLabel'] ); ?></small>
				<?php } ?>
				<strong><?php echo esc_html( $attributes['cardTitle'] ); ?></strong>
				<?php if ( ! empty( $attributes['cardMeta'] ) ) { ?>
					<span><?php echo esc_html( $attributes['cardMeta'] ); ?></span>
				<?php } ?>
				<?php if ( ! empty( $attributes['cardLinkUrl'] ) ) { ?>
					<a class="textlink" href="<?php echo esc_url( $attributes['cardLinkUrl'] ); ?>"><?php echo esc_html( $attributes['cardLinkText'] ? $attributes['cardLinkText'] : __( 'View project', 'lc-mdsx2026' ) ); ?><i class="arrow" aria-hidden="true"></i></a>
				<?php } ?>
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
