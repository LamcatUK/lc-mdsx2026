<?php
/**
 * Header template.
 *
 * @package lc-mdsx2026
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>document.documentElement.classList.add('js');setTimeout(function(){var h=document.documentElement;if(!h.classList.contains('js-ready'))h.classList.remove('js');},4000);</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'lc-mdsx2026' ); ?></a>
<?php wp_body_open(); ?>

<!-- HEADER-NAV:START -->
<header id="masthead">
	<nav class="navbar container" aria-label="<?php esc_attr_e( 'Primary navigation', 'lc-mdsx2026' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="navbar-brand" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<img class="logo-light" src="<?php echo esc_url( lc_mdsx2026_img_url( 'logos/mdsx-badge-solitude.svg' ) ); ?>" alt="MDSX" width="64" height="46">
			<img class="logo-dark" src="<?php echo esc_url( lc_mdsx2026_img_url( 'logos/mdsx-badge-primary.svg' ) ); ?>" alt="" width="64" height="46" aria-hidden="true">
		</a>

		<button class="navbar-toggler" type="button" aria-expanded="false" aria-controls="primary-menu" aria-label="<?php esc_attr_e( 'Open menu', 'lc-mdsx2026' ); ?>" data-label-open="<?php esc_attr_e( 'Open menu', 'lc-mdsx2026' ); ?>" data-label-close="<?php esc_attr_e( 'Close menu', 'lc-mdsx2026' ); ?>">
			<span></span><span></span><span></span>
		</button>

		<div class="navbar-collapse" id="primary-menu" data-lenis-prevent>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'navbar-nav',
					'container'      => false,
					'fallback_cb'    => false,
					'walker'         => new LC_MDSX_2026_Nav_Walker(),
				)
			);
			?>
			<a class="btn btn--primary navbar-cta" href="<?php echo esc_url( lc_mdsx2026_page_url( 'contact-us' ) ); ?>"><?php esc_html_e( 'Start a project', 'lc-mdsx2026' ); ?></a>
			<a class="navbar-search" href="<?php echo esc_url( lc_mdsx2026_page_url( 'search' ) ); ?>"<?php echo is_page( 'search' ) ? ' aria-current="page"' : ''; ?>>
				<?php echo get_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG. ?>
				<span><?php esc_html_e( 'Search', 'lc-mdsx2026' ); ?></span>
			</a>
		</div>
	</nav>
</header>
<!-- HEADER-NAV:END -->

<main id="main">
