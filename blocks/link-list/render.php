<?php
/**
 * Block template for Link List.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'charcoal', 'charcoal' );
$items   = array_values( array_filter( (array) ( $attributes['items'] ?? array() ), static fn( $r ) => ! lc_mdsx2026_row_empty( $r ) ) );
?>
<section <?php echo lc_mdsx2026_wrapper( 'link-list section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<?php if ( ! empty( $attributes['intro'] ) ) { ?>
			<div class="t-subhead reveal"><?php echo wp_kses_post( $attributes['intro'] ); ?></div>
		<?php } ?>
		<ul class="link-list__list">
			<?php
			foreach ( $items as $i => $item ) {
				$url   = $item['link'] ?? '';
				$tag   = $url ? 'a' : 'div';
				$attrs = $url ? ' href="' . esc_url( $url ) . '"' : '';
				?>
				<li class="reveal" style="--i:<?php echo (int) min( $i, 6 ); ?>">
					<<?php echo $tag . $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag is a literal, href escaped above. ?> class="link-list__row">
						<b><?php echo esc_html( $item['label'] ?? '' ); ?></b>
						<span><?php echo esc_html( $item['title'] ?? '' ); ?></span>
						<?php if ( ! empty( $item['meta'] ) ) { ?>
							<time><?php echo esc_html( $item['meta'] ); ?></time>
						<?php } ?>
						<?php if ( $url ) { ?>
							<i class="arrow" aria-hidden="true"></i>
						<?php } ?>
					</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal. ?>>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
