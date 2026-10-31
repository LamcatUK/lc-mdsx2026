<?php
/**
 * Footer template.
 *
 * @package lc-mdsx2026
 */

$services = get_pages(
	array(
		'parent'      => ( get_page_by_path( 'services' ) ? get_page_by_path( 'services' )->ID : -1 ),
		'sort_column' => 'menu_order,post_title',
	)
);
$email    = lc_mdsx2026_get_setting( 'email' );
$phone    = lc_mdsx2026_get_setting( 'phone' );
?>
</main>

<footer id="footer">
	<div class="container">
		<div class="footer-grid">
			<div class="footer-brand">
				<img src="<?php echo esc_url( lc_mdsx2026_img_url( 'logos/mdsx-wordmark-sea.svg' ) ); ?>" alt="MDSX" width="142" height="40">
				<p><?php esc_html_e( 'Immersive museums, exhibitions and attractions, designed with the people and communities they serve.', 'lc-mdsx2026' ); ?></p>
			</div>
			<nav aria-labelledby="footer-explore">
				<h2 id="footer-explore"><?php esc_html_e( 'Explore', 'lc-mdsx2026' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'menu_class'     => 'footer-menu',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
			<?php if ( $services ) { ?>
				<nav aria-labelledby="footer-services">
					<h2 id="footer-services"><?php esc_html_e( 'Services', 'lc-mdsx2026' ); ?></h2>
					<ul>
						<?php foreach ( $services as $service ) { ?>
							<li><a href="<?php echo esc_url( get_permalink( $service ) ); ?>"><?php echo esc_html( get_the_title( $service ) ); ?></a></li>
						<?php } ?>
					</ul>
				</nav>
			<?php } ?>
			<div>
				<h2><?php esc_html_e( 'Contact', 'lc-mdsx2026' ); ?></h2>
				<ul>
					<li>1134 Western Way, Orlando FL</li>
					<?php if ( $email ) { ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
					<?php } ?>
					<?php if ( $phone ) { ?>
						<li><a href="tel:<?php echo esc_attr( parse_phone( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
					<?php } ?>
				</ul>
				<?php echo do_shortcode( '[social_icons]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the shortcode. ?>
			</div>
		</div>
		<div id="colophon">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> MDSX Creative</span>
			<span>
				<a href="<?php echo esc_url( lc_mdsx2026_page_url( 'privacy-policy' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'lc-mdsx2026' ); ?></a> &middot;
				<a href="<?php echo esc_url( lc_mdsx2026_page_url( 'cookie-policy' ) ); ?>"><?php esc_html_e( 'Cookie Policy', 'lc-mdsx2026' ); ?></a>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
