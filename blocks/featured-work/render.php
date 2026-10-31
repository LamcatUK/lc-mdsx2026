<?php
/**
 * Block template for Featured Work.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$ids  = array_filter( array_map( 'intval', array( $attributes['project1'] ?? 0, $attributes['project2'] ?? 0, $attributes['project3'] ?? 0 ) ) );
$flip = ! empty( $attributes['flip'] );

$tile = static function ( $id, $size, $extra = '' ) {
	$sub = get_the_excerpt( $id );
	?>
	<a class="work-tile <?php echo esc_attr( $extra ); ?>" href="<?php echo esc_url( get_permalink( $id ) ); ?>">
		<div class="pg pg--half"><div class="work-tile__img"><?php echo get_the_post_thumbnail( $id, $size, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup. ?></div></div>
		<h3><?php echo esc_html( get_the_title( $id ) ); ?></h3>
		<?php if ( $sub ) { ?>
			<p><?php echo esc_html( $sub ); ?></p>
		<?php } ?>
	</a>
	<?php
};
?>
<section <?php echo lc_mdsx2026_wrapper( 'featured-work section', 'nero', $flip ? 'is-flipped' : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) || ! empty( $attributes['ctaUrl'] ) ) { ?>
			<div class="featured-work__head">
				<?php if ( ! empty( $attributes['heading'] ) ) { ?>
					<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
				<?php } ?>
				<?php lc_mdsx2026_link( $attributes['ctaText'] ?? '', $attributes['ctaUrl'] ?? '', 'textlink' ); ?>
			</div>
		<?php } ?>
		<?php if ( $ids ) { ?>
			<div class="featured-work__grid">
				<?php $tile( reset( $ids ), 'large', 'is-big' ); ?>
				<div class="featured-work__stack">
					<?php foreach ( array_slice( $ids, 1, 2 ) as $id ) { ?>
						<?php $tile( $id, 'large' ); ?>
					<?php } ?>
				</div>
			</div>
		<?php } elseif ( current_user_can( 'edit_posts' ) ) { ?>
			<p><em><?php esc_html_e( 'Choose projects in the block settings.', 'lc-mdsx2026' ); ?></em></p>
		<?php } ?>
	</div>
</section>
