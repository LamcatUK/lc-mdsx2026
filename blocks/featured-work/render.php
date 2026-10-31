<?php
/**
 * Block template for Featured Work. Shows the newest published portfolio
 * projects automatically, in groups of three (one large + two stacked),
 * alternating which side the large tile is on.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$count    = max( 1, min( 12, (int) ( $attributes['count'] ?? 6 ) ) );
$projects = get_posts(
	array(
		'post_type'      => 'portfolio',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'meta_key'       => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- only projects with an image make a tile.
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	)
);

$tile = static function ( $project, $extra = '' ) {
	?>
	<a class="work-tile <?php echo esc_attr( $extra ); ?>" href="<?php echo esc_url( get_permalink( $project ) ); ?>">
		<div class="pg pg--half"><div class="work-tile__img"><?php echo get_the_post_thumbnail( $project, 'large', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup. ?></div></div>
		<h3><?php echo esc_html( get_the_title( $project ) ); ?></h3>
		<?php if ( has_excerpt( $project ) ) { ?>
			<p><?php echo esc_html( get_the_excerpt( $project ) ); ?></p>
		<?php } ?>
	</a>
	<?php
};
?>
<section <?php echo lc_mdsx2026_wrapper( 'featured-work section', 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) || ! empty( $attributes['ctaUrl'] ) ) { ?>
			<div class="featured-work__head">
				<?php if ( ! empty( $attributes['heading'] ) ) { ?>
					<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
				<?php } ?>
				<?php lc_mdsx2026_link( $attributes['ctaText'] ?? '', $attributes['ctaUrl'] ?? '', 'textlink' ); ?>
			</div>
		<?php } ?>
		<?php foreach ( array_chunk( $projects, 3 ) as $i => $group ) { ?>
			<div class="featured-work__grid<?php echo $i % 2 ? ' is-flipped' : ''; ?>">
				<?php $tile( $group[0], 'is-big' ); ?>
				<?php if ( count( $group ) > 1 ) { ?>
					<div class="featured-work__stack">
						<?php foreach ( array_slice( $group, 1 ) as $project ) { ?>
							<?php $tile( $project ); ?>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>
