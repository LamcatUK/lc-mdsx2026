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
			<div class="profile-hero__photo reveal"><?php lc_mdsx2026_image( (int) ( $attributes['photoId'] ?? 0 ), $attributes['photoUrl'] ?? '', '', 'large', 'eager' ); ?></div>
		<?php } ?>
		<div class="profile-hero__copy">
			<?php lc_mdsx2026_breadcrumb( $crumb, $attributes['crumbUrl'] ?? '', $name ); ?>
			<h1 class="baseline reveal"><?php echo esc_html( $name ); ?></h1>
			<?php if ( ! empty( $attributes['role'] ) ) { ?>
				<p class="t-label profile-hero__role reveal" style="--i:1"><?php echo esc_html( $attributes['role'] ); ?></p>
			<?php } ?>
			<?php
			$email    = is_email( $attributes['email'] ?? '' ) ? $attributes['email'] : '';
			$linkedin = esc_url( $attributes['linkedin'] ?? '', array( 'https', 'http' ) );
			if ( $email || $linkedin ) {
				?>
				<ul class="profile-hero__contact reveal" style="--i:1">
					<?php if ( $email ) { ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
					<?php } ?>
					<?php if ( $linkedin ) { ?>
						<li><a class="profile-hero__linkedin" href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo get_icon( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG. ?>
							<span><?php esc_html_e( 'LinkedIn', 'lc-mdsx2026' ); ?></span>
							<span class="visually-hidden"><?php echo esc_html( sprintf( /* translators: %s: person's name */ __( '(%s’s profile, opens in a new tab)', 'lc-mdsx2026' ), $name ) ); ?></span>
						</a></li>
					<?php } ?>
				</ul>
			<?php } ?>
			<?php if ( ! empty( $attributes['bio'] ) ) { ?>
				<div class="profile-hero__bio reveal" style="--i:2"><?php echo wp_kses_post( $attributes['bio'] ); ?></div>
			<?php } ?>
		</div>
	</div>
</section>
