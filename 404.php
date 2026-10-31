<?php
/**
 * 404 template.
 *
 * @package lc-mdsx2026
 */

get_header();

$links = array(
	'portfolio'     => __( 'Portfolio', 'lc-mdsx2026' ),
	'services'      => __( 'Services', 'lc-mdsx2026' ),
	'about-us'      => __( 'About us', 'lc-mdsx2026' ),
	'team'          => __( 'Our team', 'lc-mdsx2026' ),
	'news'          => __( 'News', 'lc-mdsx2026' ),
);
?>
<section class="error-404 page-hero s-nero has-shape has-shape--right">
	<div class="page-hero__inner container">
		<p class="error-404__code reveal" aria-hidden="true">404</p>
		<h1 class="baseline reveal" style="--i:1"><?php esc_html_e( 'This page has moved on.', 'lc-mdsx2026' ); ?></h1>
		<p class="page-hero__intro t-subhead reveal" style="--i:2"><?php esc_html_e( "The link may be out of date, or the page may have been renamed. Head back to the studio, or pick up from one of these.", 'lc-mdsx2026' ); ?></p>
		<div class="page-hero__actions reveal" style="--i:3">
			<?php
			lc_mdsx2026_link( __( 'Back to home', 'lc-mdsx2026' ), home_url( '/' ), 'btn btn--primary' );
			lc_mdsx2026_link( __( 'See our work', 'lc-mdsx2026' ), lc_mdsx2026_page_url( 'portfolio' ), 'textlink' );
			?>
		</div>
		<nav class="error-404__links reveal" style="--i:4" aria-label="<?php esc_attr_e( 'Pages you might want', 'lc-mdsx2026' ); ?>">
			<ul>
				<?php foreach ( $links as $path => $label ) { ?>
					<li><a href="<?php echo esc_url( lc_mdsx2026_page_url( $path ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
				<?php } ?>
			</ul>
		</nav>
	</div>
</section>
<?php
echo do_blocks( '<!-- wp:lc-mdsx2026/cta-band ' . wp_json_encode( array( 'heading' => __( 'Looking for something specific? Tell us.', 'lc-mdsx2026' ), 'ctaText' => __( 'Contact us', 'lc-mdsx2026' ), 'ctaUrl' => lc_mdsx2026_page_url( 'contact-us' ) ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block render.

get_footer();
