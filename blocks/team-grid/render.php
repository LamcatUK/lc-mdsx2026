<?php
/**
 * Block template for Team Grid. Role line = post excerpt; photo = featured image.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['surface'] ?? 'nero' );
$min     = max( 8, (float) ( $attributes['minWidth'] ?? 12 ) );
$team    = get_posts(
	array(
		'post_type'      => 'team',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	)
);
?>
<section <?php echo lc_mdsx2026_wrapper( 'team-grid section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<ul class="team-grid__list" style="--col-min:<?php echo esc_attr( $min ); ?>rem">
			<?php foreach ( $team as $i => $member ) { ?>
				<li class="reveal" style="--i:<?php echo (int) ( $i % 6 ); ?>">
					<a href="<?php echo esc_url( get_permalink( $member ) ); ?>">
						<span class="team-grid__photo"><?php echo get_the_post_thumbnail( $member, 'medium', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup. ?></span>
						<strong><?php echo esc_html( get_the_title( $member ) ); ?></strong>
						<?php if ( has_excerpt( $member ) ) { ?>
							<span><?php echo esc_html( get_the_excerpt( $member ) ); ?></span>
						<?php } ?>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
