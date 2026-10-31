<?php
/**
 * Block template for Site Search.
 *
 * A real GET form to /search/?q=… that also works without JavaScript (results are rendered here from
 * the query string); src/js/site-search.js upgrades it to live results.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$query   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only search term.
$terms   = lc_mdsx2026_search_terms( $query );
$results = $terms ? lc_mdsx2026_search( $query ) : array();
$groups  = array();
foreach ( $results as $result ) {
	$groups[ $result['type'] ][] = $result;
}
$input_id = wp_unique_id( 'site-search-' );
?>
<section <?php echo lc_mdsx2026_wrapper( 'site-search section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?> data-site-search data-endpoint="<?php echo esc_url( rest_url( 'lc-mdsx2026/v1/search' ) ); ?>">
	<div class="container">
		<form class="site-search__form" role="search" action="<?php echo esc_url( lc_mdsx2026_page_url( 'search' ) ); ?>" method="get">
			<label class="visually-hidden" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Search the site', 'lc-mdsx2026' ); ?></label>
			<?php echo get_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG. ?>
			<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="q" value="<?php echo esc_attr( $query ); ?>" placeholder="<?php echo esc_attr( $attributes['placeholder'] ?? '' ); ?>" autocomplete="off" autofocus data-search-input>
			<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Search', 'lc-mdsx2026' ); ?><i class="arrow" aria-hidden="true"></i></button>
		</form>

		<div class="site-search__filters" data-search-filters <?php echo $results ? '' : 'hidden'; ?>>
			<?php if ( $results ) { ?>
				<button class="chip" type="button" data-type="" aria-pressed="true"><?php echo esc_html( sprintf( /* translators: %d: number of results */ __( 'All (%d)', 'lc-mdsx2026' ), count( $results ) ) ); ?></button>
				<?php foreach ( lc_mdsx2026_search_types() as $type => $label ) { ?>
					<?php if ( ! empty( $groups[ $type ] ) ) { ?>
						<button class="chip" type="button" data-type="<?php echo esc_attr( $type ); ?>" aria-pressed="false"><?php echo esc_html( sprintf( '%s (%d)', $label, count( $groups[ $type ] ) ) ); ?></button>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</div>

		<div class="site-search__results" data-search-results aria-live="polite">
			<?php if ( $terms ) { ?>
				<p class="site-search__status" role="status">
					<?php
					echo esc_html(
						$results
							/* translators: %d: number of results */
							? sprintf( _n( '%d result', '%d results', count( $results ), 'lc-mdsx2026' ), count( $results ) )
							: __( 'No results. Try different or fewer words.', 'lc-mdsx2026' )
					);
					?>
				</p>
				<?php foreach ( lc_mdsx2026_search_types() as $type => $label ) { ?>
					<?php if ( ! empty( $groups[ $type ] ) ) { ?>
						<section class="site-search__group" data-type="<?php echo esc_attr( $type ); ?>">
							<h2><?php echo esc_html( $label ); ?></h2>
							<ul class="site-search__list">
								<?php foreach ( $groups[ $type ] as $item ) { ?>
									<li class="site-search__item<?php echo $item['image'] ? ' has-thumb' : ''; ?>">
										<?php if ( $item['image'] ) { ?>
											<a class="site-search__thumb pg pg--half" href="<?php echo esc_url( $item['url'] ); ?>" tabindex="-1" aria-hidden="true"><div><img src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" decoding="async"></div></a>
										<?php } ?>
										<div class="site-search__text">
											<h3><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo lc_mdsx2026_search_highlight( $item['title'], $terms ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></a></h3>
											<p><?php echo lc_mdsx2026_search_highlight( $item['snippet'], $terms ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></p>
										</div>
									</li>
								<?php } ?>
							</ul>
						</section>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</section>
