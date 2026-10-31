<?php
/**
 * Block editor tweaks. Standing per-theme convention for this user — not
 * covered by the lcp-blog-options plugin (which handles comments/tags/emoji
 * site-wide, but not this).
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load only the small editor-only stylesheet into the block editor iframe.
 *
 * Deliberately NOT theme.min.css (a departure from the skeleton, which loads
 * the frontend stylesheet for full parity): MDSX blocks edit through field
 * controls rather than a WYSIWYG preview, and the frontend's dark surface and
 * display type made those controls unreadable. The editor keeps WordPress's
 * normal look. Relies on the 'editor-styles' support in inc/setup.php.
 *
 * @return void
 */
function lc_mdsx2026_add_editor_styles() {
	add_editor_style( 'css/editor.min.css' );
}
add_action( 'after_setup_theme', 'lc_mdsx2026_add_editor_styles' );

/**
 * Disable the block editor's fullscreen mode by default.
 *
 * @return void
 */
// phpcs:disable
function lc_mdsx2026_disable_editor_fullscreen_by_default() {
	$script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
	wp_add_inline_script( 'wp-blocks', $script );
}
add_action( 'enqueue_block_editor_assets', 'lc_mdsx2026_disable_editor_fullscreen_by_default' );
// phpcs:enable

/**
 * Disable the block inserter's extra Media/Openverse panel.
 *
 * This theme keeps the editor pared back and does not use WordPress's stock
 * remote media suggestions.
 *
 * @param array $settings Block editor settings.
 * @return array
 */
function lc_mdsx2026_disable_openverse_media_category( $settings ) {
	$settings['enableOpenverseMediaCategory'] = false;

	return $settings;
}
add_filter( 'block_editor_settings_all', 'lc_mdsx2026_disable_openverse_media_category' );

/**
 * Remove the block directory upsell from the inserter.
 *
 * This keeps clients out of WordPress's install-more-blocks prompt.
 *
 * @return void
 */
function lc_mdsx2026_disable_block_directory_inserter() {
	remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
}
add_action( 'after_setup_theme', 'lc_mdsx2026_disable_block_directory_inserter' );

/**
 * Hide the Author column on the All Pages list by default.
 *
 * Pages here are built by one team, so the column is noise. This only sets the
 * default: anyone can switch it back on under Screen Options, and a user who
 * has already saved their own column choices keeps them.
 *
 * @param string[]  $hidden Columns hidden by default.
 * @param WP_Screen $screen Current screen.
 * @return string[]
 */
function lc_mdsx2026_hide_pages_author_column_by_default( $hidden, $screen ) {
	if ( $screen && 'edit-page' === $screen->id ) {
		$hidden[] = 'author';
	}

	return $hidden;
}
add_filter( 'default_hidden_columns', 'lc_mdsx2026_hide_pages_author_column_by_default', 10, 2 );
