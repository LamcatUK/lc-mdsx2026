<?php
/**
 * Block template for Split Intro.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$facts   = array_filter( (array) ( $attributes['facts'] ?? array() ), static fn( $r ) => ! lc_mdsx2026_row_empty( $r ) );
?>
<section <?php echo lc_mdsx2026_wrapper( 'split-intro section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<div class="split-intro__grid">
			<?php if ( ! empty( $attributes['statement'] ) ) { ?>
				<h2 class="split-intro__statement baseline reveal"><?php echo esc_html( $attributes['statement'] ); ?></h2>
			<?php } ?>
			<div class="split-intro__body reveal" style="--i:1">
				<?php if ( ! empty( $attributes['body'] ) ) { ?>
					<div class="t-lede"><?php echo wp_kses_post( $attributes['body'] ); ?></div>
				<?php } ?>
				<?php lc_mdsx2026_link( $attributes['ctaText'] ?? '', $attributes['ctaUrl'] ?? '', 'textlink' ); ?>
			</div>
		</div>
		<?php if ( $facts ) { ?>
			<dl class="split-intro__facts<?php echo ! empty( $attributes['factsFlag'] ) ? ' has-flags' : ''; ?><?php echo ! empty( $attributes['factsDividers'] ) ? ' has-dividers' : ''; ?>">
				<?php foreach ( $facts as $i => $fact ) { ?>
					<div class="reveal" style="--i:<?php echo (int) $i; ?>">
						<dt><?php echo esc_html( $fact['title'] ?? '' ); ?></dt>
						<dd><?php echo esc_html( $fact['text'] ?? '' ); ?></dd>
					</div>
				<?php } ?>
			</dl>
		<?php } ?>
	</div>
</section>
