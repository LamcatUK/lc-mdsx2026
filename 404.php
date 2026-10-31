<?php
/**
 * 404 template.
 *
 * @package lc-mdsx2026
 */

get_header();
?>

<div class="container pt-nav">
	<h1><?php esc_html_e( 'Page not found', 'lc-mdsx2026' ); ?></h1>
	<p><?php esc_html_e( "The page you're looking for doesn't exist.", 'lc-mdsx2026' ); ?></p>
</div>

<?php
get_footer();
