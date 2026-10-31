<?php
/**
 * Block template for Portfolio Grid. Filtering is client-side (src/js/portfolio-filter.js).
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$show_filters = $attributes['showFilters'] ?? true;
$projects     = get_posts(
	array(
		'post_type'      => 'portfolio',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	)
);

$groups = array(
	'discipline' => __( 'Discipline', 'lc-mdsx2026' ),
	'sector'     => __( 'Sector', 'lc-mdsx2026' ),
);
$terms  = array();
foreach ( array_keys( $groups ) as $tax ) {
	$terms[ $tax ] = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => true ) );
	if ( is_wp_error( $terms[ $tax ] ) ) {
		$terms[ $tax ] = array();
	}
}
?>
<section <?php echo lc_mdsx2026_wrapper( 'portfolio-grid', 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?> data-portfolio-grid>
	<?php if ( $show_filters && ( $terms['discipline'] || $terms['sector'] ) ) { ?>
		<div class="portfolio-grid__filters">
			<div class="container portfolio-grid__filter-bar">
				<?php foreach ( $groups as $tax => $label ) { ?>
					<?php if ( $terms[ $tax ] ) { ?>
						<fieldset data-filter-group="<?php echo esc_attr( $tax ); ?>">
							<legend><?php echo esc_html( $label ); ?></legend>
							<button class="chip" type="button" data-filter="" aria-pressed="true"><?php esc_html_e( 'All', 'lc-mdsx2026' ); ?></button>
							<?php foreach ( $terms[ $tax ] as $term ) { ?>
								<button class="chip" type="button" data-filter="<?php echo esc_attr( $term->slug ); ?>" aria-pressed="false"><?php echo esc_html( $term->name ); ?></button>
							<?php } ?>
						</fieldset>
					<?php } ?>
				<?php } ?>
				<p class="portfolio-grid__count" role="status" aria-live="polite" data-count data-template="<?php echo esc_attr__( '%d projects', 'lc-mdsx2026' ); ?>"></p>
			</div>
		</div>
	<?php } ?>
	<div class="container">
		<h2 class="visually-hidden"><?php esc_html_e( 'Projects', 'lc-mdsx2026' ); ?></h2>
		<ul class="portfolio-grid__list">
			<?php
			foreach ( $projects as $project ) {
				$tags = array();
				$data = '';
				foreach ( array_keys( $groups ) as $tax ) {
					$ts = get_the_terms( $project, $tax );
					$ts = is_array( $ts ) ? $ts : array();
					foreach ( $ts as $t ) {
						$tags[] = $t->name;
					}
					$data .= ' data-' . $tax . '="' . esc_attr( implode( ' ', wp_list_pluck( $ts, 'slug' ) ) ) . '"';
				}
				?>
				<li class="portfolio-grid__item"<?php echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped while built. ?>>
					<a class="work-tile" href="<?php echo esc_url( get_permalink( $project ) ); ?>">
						<div class="pg pg--half"><div class="work-tile__img"><?php echo get_the_post_thumbnail( $project, 'large', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup. ?></div></div>
						<h3><?php echo esc_html( get_the_title( $project ) ); ?></h3>
						<?php if ( has_excerpt( $project ) ) { ?>
							<p><?php echo esc_html( get_the_excerpt( $project ) ); ?></p>
						<?php } ?>
						<?php if ( $tags ) { ?>
							<p class="work-tile__tags"><?php echo esc_html( implode( ' · ', $tags ) ); ?></p>
						<?php } ?>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
