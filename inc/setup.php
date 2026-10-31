<?php
/**
 * Theme setup — supports, nav menus.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Core theme supports and nav menu locations.
 *
 * @return void
 */
function lc_mdsx2026_setup() {
	load_theme_textdomain( 'lc-mdsx2026', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' ); // Site title in <head> — no separate "site title" support needed beyond this.
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script' ) ); // Clean markup for enqueued tags. Not search-form/comment-form/comment-list/gallery/caption — none of those are in use.
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'disable-custom-colors' );

	// Rename/extend per project.
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'lc-mdsx2026' ),
			'footer'  => __( 'Footer Menu', 'lc-mdsx2026' ),
		)
	);
}
add_action( 'after_setup_theme', 'lc_mdsx2026_setup' );

/**
 * Mark the index page's menu item as an ancestor on single portfolio items, team members and news posts,
 * which WordPress doesn't do for post types whose index is a plain page (so /portfolio/ stays highlighted
 * on /portfolio/some-project/). Runs on wp_nav_menu_objects because the theme's walker builds its own
 * markup and never fires nav_menu_css_class.
 *
 * @param WP_Post[] $items Menu items.
 * @return WP_Post[]
 */
function lc_mdsx2026_nav_section_ancestor( $items ) {
	if ( ! is_singular() ) {
		return $items;
	}

	$index = array(
		'portfolio' => get_page_by_path( 'portfolio' ),
		'team'      => get_page_by_path( 'team' ),
		'post'      => get_post( (int) get_option( 'page_for_posts' ) ),
	);
	$page  = $index[ get_post_type() ] ?? null;

	foreach ( $items as $item ) {
		if ( $page && 'page' === $item->object && (int) $item->object_id === $page->ID ) {
			$item->classes[] = 'current-menu-ancestor';
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'lc_mdsx2026_nav_section_ancestor' );
