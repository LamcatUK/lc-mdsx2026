<?php
/**
 * Block template for Profile Hero.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$name    = ! empty( $attributes['name'] ) ? $attributes['name'] : get_the_title();
$crumb   = $attributes['crumbText'] ?? '';
$has_img = ! empty( $attributes['photoUrl'] ) || ! empty( $attributes['photoId'] );
?>
<section <?php echo lc_mdsx2026_wrapper( 'profile-hero', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container profile-hero__grid<?php echo $has_img ? '' : ' is-no-photo'; ?>">
		<?php if ( $has_img ) { ?>
			<div class="profile-hero__photo reveal"><?php lc_mdsx2026_image( (int) ( $attributes['photoId'] ?? 0 ), $attributes['photoUrl'] ?? '', $name, 'large', 'eager' ); ?></div>
		<?php } ?>
		<div class="profile-hero__copy">
			<?php if ( $crumb ) { ?>
				<?php if ( ! empty( $attributes['crumbUrl'] ) ) { ?>
					<a class="page-hero__crumb" href="<?php echo esc_url( $attributes['crumbUrl'] ); ?>"><?php echo esc_html( $crumb ); ?></a>
				<?php } else { ?>
					<span class="page-hero__crumb"><?php echo esc_html( $crumb ); ?></span>
				<?php } ?>
			<?php } ?>
			<h1 class="baseline reveal"><?php echo esc_html( $name ); ?></h1>
			<?php if ( ! empty( $attributes['role'] ) ) { ?>
				<p class="t-label profile-hero__role reveal" style="--i:1"><?php echo esc_html( $attributes['role'] ); ?></p>
			<?php } ?>
			<?php if ( ! empty( $attributes['bio'] ) ) { ?>
				<div class="profile-hero__bio reveal" style="--i:2"><?php echo wp_kses_post( $attributes['bio'] ); ?></div>
			<?php } ?>
		</div>
	</div>
</section>
