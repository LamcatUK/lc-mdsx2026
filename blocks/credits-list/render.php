<?php
/**
 * Block template for Credits List.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['surface'] ?? 'nero' );
$items   = array_filter( (array) ( $attributes['items'] ?? array() ), static fn( $r ) => ! lc_mdsx2026_row_empty( $r ) );
if ( ! $items ) {
	return;
}
?>
<section <?php echo lc_mdsx2026_wrapper( 'credits-list section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="t-h3 reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<ul class="credits-list__list">
			<?php foreach ( $items as $item ) { ?>
				<li><b><?php echo esc_html( $item['role'] ?? '' ); ?></b><?php echo esc_html( $item['name'] ?? '' ); ?></li>
			<?php } ?>
		</ul>
	</div>
</section>
