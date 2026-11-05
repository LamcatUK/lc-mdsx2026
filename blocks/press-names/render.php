<?php
/**
 * Block template for Press Names: outlet names, each optionally linked to the article.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$items   = array_values( array_filter( (array) ( $attributes['items'] ?? array() ), static fn( $r ) => ! empty( $r['name'] ) ) );
if ( ! $items ) {
	return;
}
$home = wp_parse_url( home_url(), PHP_URL_HOST );

if ( ! function_exists( 'lc_mdsx2026_press_name' ) ) {
	/**
	 * One outlet: its logo when supplied (the outlet name is the alt text), otherwise its name as text.
	 *
	 * @param array $item Row.
	 * @param bool  $logo Whether the row has a logo.
	 * @return void
	 */
	function lc_mdsx2026_press_name( $item, $logo ) {
		if ( $logo ) {
			lc_mdsx2026_image( (int) ( $item['logo'] ?? 0 ), $item['logoUrl'] ?? '', $item['name'], 'medium', 'lazy' );
			return;
		}
		echo esc_html( $item['name'] );
	}
}
?>
<section <?php echo lc_mdsx2026_wrapper( 'press-names section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="press-names__heading t-sub reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<ul class="press-names__list reveal" style="--i:1">
			<?php
			foreach ( $items as $item ) {
				$url      = $item['link'] ?? '';
				$headline = $item['linkText'] ?? '';
				$logo     = ! empty( $item['logo'] ) || ! empty( $item['logoUrl'] );
				$external = $url && wp_parse_url( $url, PHP_URL_HOST ) && wp_parse_url( $url, PHP_URL_HOST ) !== $home;
				?>
				<li>
					<?php if ( $url ) { ?>
						<a href="<?php echo esc_url( $url ); ?>"<?php echo $headline ? ' title="' . esc_attr( $headline ) . '"' : ''; ?><?php echo $external ? ' target="_blank" rel="noopener"' : ''; ?>>
							<?php lc_mdsx2026_press_name( $item, $logo ); ?>
							<?php if ( $external ) { ?>
								<span class="visually-hidden"><?php esc_html_e( '(opens in a new tab)', 'lc-mdsx2026' ); ?></span>
							<?php } ?>
						</a>
					<?php } else { ?>
						<?php lc_mdsx2026_press_name( $item, $logo ); ?>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
