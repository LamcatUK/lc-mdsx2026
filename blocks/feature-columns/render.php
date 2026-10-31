<?php
/**
 * Block template for Feature Columns.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['surface'] ?? 'charcoal', 'charcoal' );
$items   = array_values( array_filter( (array) ( $attributes['items'] ?? array() ), static fn( $r ) => ! lc_mdsx2026_row_empty( $r ) ) );
$marks   = $attributes['marks'] ?? 'mark';
$min     = max( 8, (float) ( $attributes['minWidth'] ?? 18 ) );
$colours = array( 'mark', 'mark mark--crusta', 'mark mark--solitude' );
?>
<section <?php echo lc_mdsx2026_wrapper( 'feature-columns section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<?php if ( ! empty( $attributes['intro'] ) ) { ?>
			<div class="t-lede reveal"><?php echo wp_kses_post( $attributes['intro'] ); ?></div>
		<?php } ?>
		<div class="feature-columns__grid" style="--col-min:<?php echo esc_attr( $min ); ?>rem">
			<?php foreach ( $items as $i => $item ) { ?>
				<div class="feature-columns__item reveal" style="--i:<?php echo (int) $i; ?>">
					<?php if ( 'mark' === $marks ) { ?>
						<span class="<?php echo esc_attr( $colours[ $i % 3 ] ); ?> feature-columns__mark" aria-hidden="true"></span>
					<?php } elseif ( 'number' === $marks ) { ?>
						<span class="feature-columns__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<?php } ?>
					<h3><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
					<?php if ( ! empty( $item['text'] ) ) { ?>
						<p><?php echo esc_html( $item['text'] ); ?></p>
					<?php } ?>
					<?php lc_mdsx2026_link( $item['linkText'] ?? '', $item['link'] ?? '', 'textlink' ); ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
