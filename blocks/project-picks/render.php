<?php
/**
 * Block template for Project Picks.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface  = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$projects = array();
foreach ( array_unique( array_filter( array_map( 'intval', (array) ( $attributes['projects'] ?? array() ) ) ) ) as $id ) {
	if ( 'publish' === get_post_status( $id ) && ! lc_mdsx2026_is_unlisted( $id ) ) {
		$projects[] = $id;
	}
}
if ( ! $projects ) {
	return;
}
?>
<section <?php echo lc_mdsx2026_wrapper( 'project-picks section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<ul class="project-picks__list">
			<?php foreach ( $projects as $i => $id ) { ?>
				<li class="reveal" style="--i:<?php echo (int) ( $i % 3 ); ?>">
					<a class="work-tile" href="<?php echo esc_url( get_permalink( $id ) ); ?>">
						<div class="pg pg--half"><div class="work-tile__img"><?php echo get_the_post_thumbnail( $id, 'large', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup. ?></div></div>
						<h3><?php echo esc_html( get_the_title( $id ) ); ?></h3>
						<?php if ( has_excerpt( $id ) ) { ?>
							<p><?php echo esc_html( get_the_excerpt( $id ) ); ?></p>
						<?php } ?>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
