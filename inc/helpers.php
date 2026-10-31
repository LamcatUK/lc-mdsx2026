<?php
/**
 * Project-specific helpers for block render templates.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Permalink for a page by its path (e.g. 'contact-us'), falling back to
 * home_url() when the page doesn't exist. Resolving through the page rather
 * than hardcoding a path keeps URLs correct if slugs change or a second
 * language is added (Polylang filters get_permalink()).
 *
 * @param string $path Page path.
 * @return string
 */
function lc_mdsx2026_page_url( $path ) {
	$page = get_page_by_path( $path );
	return $page ? get_permalink( $page ) : home_url( '/' . trim( $path, '/' ) . '/' );
}

/**
 * URL of a file in the theme's img/ folder.
 *
 * @param string $file Path relative to img/.
 * @return string
 */
function lc_mdsx2026_img_url( $file ) {
	return get_template_directory_uri() . '/img/' . ltrim( $file, '/' );
}

/**
 * Allowed surface (background) slugs for blocks.
 *
 * @param string $surface Requested surface.
 * @param string $default Fallback.
 * @return string
 */
function lc_mdsx2026_surface( $surface, $default = 'nero' ) {
	return in_array( $surface, array( 'nero', 'charcoal', 'sea', 'crusta', 'solitude' ), true ) ? $surface : $default;
}

/**
 * Surface-aware button class: Sea buttons on dark surfaces, Nero buttons on light/coloured ones.
 *
 * @param string $surface Surface slug.
 * @return string
 */
function lc_mdsx2026_btn_class( $surface ) {
	return in_array( $surface, array( 'nero', 'charcoal' ), true ) ? 'btn btn--primary' : 'btn btn--dark';
}

/**
 * Render a link as a parallelogram button (or a text link). Prints nothing if there is no URL.
 *
 * @param string $text  Link text.
 * @param string $url   Link URL.
 * @param string $class Extra classes (e.g. 'btn btn--primary' or 'textlink').
 * @return void
 */
function lc_mdsx2026_link( $text, $url, $class = 'btn btn--primary' ) {
	if ( ! $url ) {
		return;
	}
	printf(
		'<a class="%s" href="%s">%s<i class="arrow" aria-hidden="true"></i></a>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_html( $text ? $text : $url )
	);
}

/**
 * Print an <img> from block attributes, with the media-library responsive
 * markup when an attachment ID is known and a plain tag when only a URL is.
 *
 * @param int    $id      Attachment ID (0 if unknown).
 * @param string $url     Image URL fallback.
 * @param string $alt     Alt text.
 * @param string $size    Image size.
 * @param string $loading 'lazy' or 'eager'.
 * @return void
 */
function lc_mdsx2026_image( $id, $url, $alt = '', $size = 'large', $loading = 'lazy' ) {
	if ( $id ) {
		$alt_text = '' !== $alt ? $alt : (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		echo wp_get_attachment_image( $id, $size, false, array( 'alt' => $alt_text, 'loading' => $loading ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup.
	} elseif ( $url ) {
		printf( '<img src="%s" alt="%s" loading="%s" decoding="async">', esc_url( $url ), esc_attr( $alt ), esc_attr( $loading ) );
	}
}

/**
 * Print a block's opening <section> tag with surface class, optional extra
 * classes and the editor-set anchor.
 *
 * @param string $name    Block slug (becomes the class).
 * @param string $surface Surface slug, or '' for none.
 * @param string $extra   Extra classes.
 * @return string Attribute string from get_block_wrapper_attributes().
 */
function lc_mdsx2026_wrapper( $name, $surface = '', $extra = '' ) {
	$class = trim( $name . ( $surface ? ' s-' . lc_mdsx2026_surface( $surface ) : '' ) . ' ' . $extra );
	return get_block_wrapper_attributes( array( 'class' => $class ) );
}

/**
 * Split a textarea value into trimmed non-empty lines.
 *
 * @param string $text Raw text.
 * @return string[]
 */
function lc_mdsx2026_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\n|\r/', (string) $text ) ) ) );
}

/**
 * Is a repeater row empty (every value blank)?
 *
 * @param array $row Row.
 * @return bool
 */
function lc_mdsx2026_row_empty( $row ) {
	return ! array_filter( (array) $row, static function ( $v ) {
		return '' !== $v && 0 !== $v && null !== $v;
	} );
}

/**
 * Print one news article as a card (<li>), used by the News List block and the Posts page template.
 *
 * @param WP_Post $article Post to show.
 * @param int     $i       Position in the list, for the staggered reveal.
 * @return void
 */
function lc_mdsx2026_news_card( $article, $i = 0 ) {
	?>
	<li class="reveal" style="--i:<?php echo (int) ( $i % 3 ); ?>">
		<a class="news-card" href="<?php echo esc_url( get_permalink( $article ) ); ?>">
			<div class="pg pg--half"><div class="work-tile__img"><?php echo get_the_post_thumbnail( $article, 'large', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup. ?></div></div>
			<time datetime="<?php echo esc_attr( get_the_date( 'c', $article ) ); ?>"><?php echo esc_html( get_the_date( '', $article ) ); ?></time>
			<h3><?php echo esc_html( get_the_title( $article ) ); ?></h3>
			<?php if ( has_excerpt( $article ) ) { ?>
				<p><?php echo esc_html( get_the_excerpt( $article ) ); ?></p>
			<?php } ?>
		</a>
	</li>
	<?php
}
