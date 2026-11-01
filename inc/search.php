<?php
/**
 * Site search: pages, projects, people and news.
 *
 *   GET /wp-json/lc-mdsx2026/v1/search?q=gateway
 *   -> { "total": n, "results": [ { id, type, label, title, url, snippet, image }, … ] }
 *
 * Content here lives in block attributes (headings, bios, intros), not in post_content text, so
 * searching the database with LIKE would miss most of it and match block markup. Instead a text
 * index is built once from each published item (title, excerpt, taxonomy terms and every readable
 * string in its blocks), cached in a transient and cleared whenever content changes. Every word of
 * the query must appear somewhere in an item; title matches rank first. The client highlights the
 * matches in <mark> (src/js/site-search.js); render.php of the Site Search block does the same in
 * PHP so /search/?q=… works without JavaScript.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

const LC_MDSX2026_SEARCH_INDEX = 'lc_mdsx2026_search_index';

/**
 * Searchable post types, in display order: slug => group label.
 *
 * @return string[]
 */
function lc_mdsx2026_search_types() {
	return array(
		'portfolio' => __( 'Projects', 'lc-mdsx2026' ),
		'page'      => __( 'Pages', 'lc-mdsx2026' ),
		'team'      => __( 'People', 'lc-mdsx2026' ),
		'post'      => __( 'News', 'lc-mdsx2026' ),
	);
}

/**
 * Is this block attribute string worth searching (readable text, not a URL, slug or colour)?
 *
 * @param string $value Attribute value.
 * @return bool
 */
function lc_mdsx2026_search_is_text( $value ) {
	$value = trim( $value );

	if ( strlen( $value ) < 3 ) {
		return false;
	}
	if ( preg_match( '~^(https?:)?//|^/|^mailto:|^#|@[a-z0-9-]+\.~i', $value ) ) {
		return false;
	}

	return ! preg_match( '/^[a-z0-9_-]+$/', $value );
}

/**
 * Collect readable strings from a block attribute value (recursing into repeater rows).
 *
 * @param mixed    $value Attribute value.
 * @param string[] $out   Collected text, by reference.
 * @param string|int $key Attribute name this value sits under.
 * @return void
 */
function lc_mdsx2026_search_collect( $value, &$out, $key = '' ) {
	// Image descriptions and breadcrumb labels repeat across pages and only add noise to snippets.
	if ( is_string( $key ) && preg_match( '/(^alt$|Alt$|^crumb)/', $key ) ) {
		return;
	}

	if ( is_array( $value ) ) {
		foreach ( $value as $k => $item ) {
			lc_mdsx2026_search_collect( $item, $out, $k );
		}
	} elseif ( is_string( $value ) && lc_mdsx2026_search_is_text( $value ) ) {
		$out[] = wp_strip_all_tags( $value );
	}
}

/**
 * Walk parsed blocks, gathering attribute text and static HTML text.
 *
 * @param array    $blocks Parsed blocks.
 * @param string[] $out    Collected text, by reference.
 * @return void
 */
function lc_mdsx2026_search_walk( $blocks, &$out ) {
	foreach ( $blocks as $block ) {
		lc_mdsx2026_search_collect( $block['attrs'] ?? array(), $out );

		if ( ! empty( $block['innerHTML'] ) ) {
			$out[] = wp_strip_all_tags( $block['innerHTML'] );
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			lc_mdsx2026_search_walk( $block['innerBlocks'], $out );
		}
	}
}

/**
 * Plain text of a post's body: every readable block attribute plus static block HTML.
 *
 * @param WP_Post $post Post.
 * @return string
 */
function lc_mdsx2026_search_text( $post ) {
	$out = array();
	lc_mdsx2026_search_walk( parse_blocks( $post->post_content ), $out );

	$text = html_entity_decode( implode( ' ', array_unique( $out ) ), ENT_QUOTES, 'UTF-8' );

	return trim( (string) preg_replace( '/\s+/u', ' ', $text ) );
}

/**
 * The cached search index: one entry per published, searchable item.
 *
 * @return array[]
 */
function lc_mdsx2026_search_index() {
	$cached = get_transient( LC_MDSX2026_SEARCH_INDEX );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$search_page = get_page_by_path( 'search' );
	$exclude     = array_filter( array( $search_page ? $search_page->ID : 0 ) );
	$index       = array();

	$posts = get_posts(
		array(
			'post_type'        => array_keys( lc_mdsx2026_search_types() ),
			'post_status'      => 'publish',
			'numberposts'      => -1,
			'post__not_in'     => $exclude,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'no_found_rows'    => true,
			'suppress_filters' => true,
		)
	);

	foreach ( $posts as $post ) {
		if ( 'portfolio' === $post->post_type && lc_mdsx2026_is_unlisted( $post ) ) {
			continue; // Password-protected and hidden projects are not searchable.
		}

		$terms = array();
		foreach ( get_object_taxonomies( $post->post_type ) as $taxonomy ) {
			if ( in_array( $taxonomy, array( 'category', 'discipline', 'sector' ), true ) ) {
				foreach ( (array) get_the_terms( $post, $taxonomy ) as $term ) {
					if ( $term instanceof WP_Term && 'uncategorized' !== $term->slug ) {
						$terms[] = $term->name;
					}
				}
			}
		}

		$title = html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' );
		// Hero blocks repeat the title as the first words of the body; drop it so snippets start with something new.
		$source = $post;
		if ( 'email' === lc_mdsx2026_access_mode( $post ) ) {
			// Lead-capture projects: only the public teaser is searchable.
			$source               = clone $post;
			$source->post_content = serialize_blocks( lc_mdsx2026_gate_split( $post )['teaser'] );
		}
		$body = (string) preg_replace( '/^' . preg_quote( $title, '/' ) . '\s*/iu', '', lc_mdsx2026_search_text( $source ) );

		$index[] = array(
			'id'      => $post->ID,
			'type'    => $post->post_type,
			'title'   => $title,
			'excerpt' => html_entity_decode( wp_strip_all_tags( implode( ' ', array_filter( array( $post->post_excerpt, implode( ' ', $terms ) ) ) ) ), ENT_QUOTES, 'UTF-8' ),
			'text'    => $body,
			'url'     => get_permalink( $post ),
			'image'   => (string) get_the_post_thumbnail_url( $post, 'medium' ),
			'time'    => (int) get_post_time( 'U', true, $post ),
		);
	}

	set_transient( LC_MDSX2026_SEARCH_INDEX, $index, DAY_IN_SECONDS );

	return $index;
}

/**
 * Clear the cached index whenever content or its terms change.
 *
 * @return void
 */
function lc_mdsx2026_search_flush() {
	delete_transient( LC_MDSX2026_SEARCH_INDEX );
}
add_action( 'save_post', 'lc_mdsx2026_search_flush' );
add_action( 'deleted_post', 'lc_mdsx2026_search_flush' );
add_action( 'trashed_post', 'lc_mdsx2026_search_flush' );
add_action( 'set_object_terms', 'lc_mdsx2026_search_flush' );
add_action( 'edited_term', 'lc_mdsx2026_search_flush' );
add_action( 'after_switch_theme', 'lc_mdsx2026_search_flush' );

/**
 * Split a query into words (2+ characters, at most six).
 *
 * @param string $query Raw query.
 * @return string[]
 */
function lc_mdsx2026_search_terms( $query ) {
	$query = mb_substr( trim( (string) $query ), 0, 100 );
	$words = preg_split( '/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY );

	return array_slice(
		array_values(
			array_filter(
				$words,
				static function ( $word ) {
					return mb_strlen( $word ) >= 2;
				}
			)
		),
		0,
		6
	);
}

/**
 * A window of text around the earliest match, falling back to the opening words.
 *
 * @param string   $text  Body text.
 * @param string[] $terms Search words.
 * @return string
 */
function lc_mdsx2026_search_snippet( $text, $terms ) {
	$pos = false;
	foreach ( $terms as $term ) {
		$found = mb_stripos( $text, $term );
		if ( false !== $found && ( false === $pos || $found < $pos ) ) {
			$pos = $found;
		}
	}

	if ( false === $pos ) {
		return wp_trim_words( $text, 28, '…' );
	}

	$start = max( 0, $pos - 70 );

	return ( $start > 0 ? '…' : '' ) . trim( mb_substr( $text, $start, 220 ) ) . ( $start + 220 < mb_strlen( $text ) ? '…' : '' );
}

/**
 * Which text to cut the snippet from: the body when a word appears in it, otherwise the short excerpt
 * (client, services) so a title-only match still reads sensibly.
 *
 * @param array    $item  Index entry.
 * @param string[] $terms Search words.
 * @return string
 */
function lc_mdsx2026_search_snippet_source( $item, $terms ) {
	foreach ( $terms as $term ) {
		if ( false !== mb_stripos( $item['text'], $term ) ) {
			return $item['text'];
		}
	}

	return $item['excerpt'] ? $item['excerpt'] : $item['text'];
}

/**
 * Run a search.
 *
 * @param string $query Raw query.
 * @return array[] Results, best first.
 */
function lc_mdsx2026_search( $query ) {
	$terms = lc_mdsx2026_search_terms( $query );
	if ( ! $terms ) {
		return array();
	}

	$labels  = lc_mdsx2026_search_types();
	$scored  = array();

	foreach ( lc_mdsx2026_search_index() as $item ) {
		$score = 0;

		foreach ( $terms as $term ) {
			$in_title   = false !== mb_stripos( $item['title'], $term );
			$in_excerpt = false !== mb_stripos( $item['excerpt'], $term );
			$in_text    = false !== mb_stripos( $item['text'], $term );

			if ( ! $in_title && ! $in_excerpt && ! $in_text ) {
				continue 2; // Every word must appear somewhere.
			}

			$score += ( $in_title ? 10 : 0 ) + ( $in_excerpt ? 4 : 0 ) + ( $in_text ? 1 : 0 );
		}

		$scored[] = array(
			'score' => $score,
			'time'  => $item['time'],
			'data'  => array(
				'id'      => $item['id'],
				'type'    => $item['type'],
				'label'   => $labels[ $item['type'] ] ?? '',
				'title'   => $item['title'],
				'url'     => $item['url'],
				'snippet' => lc_mdsx2026_search_snippet( lc_mdsx2026_search_snippet_source( $item, $terms ), $terms ),
				'image'   => $item['image'],
			),
		);
	}

	usort(
		$scored,
		static function ( $a, $b ) {
			return array( $b['score'], $b['time'] ) <=> array( $a['score'], $a['time'] );
		}
	);

	return array_column( array_slice( $scored, 0, 60 ), 'data' );
}

add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'lc-mdsx2026/v1',
			'/search',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'args'                => array(
					'q' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'callback'            => static function ( WP_REST_Request $request ) {
					$results = lc_mdsx2026_search( $request->get_param( 'q' ) );

					return new WP_REST_Response(
						array(
							'total'   => count( $results ),
							'results' => $results,
						)
					);
				},
			)
		);
	}
);

/**
 * Wrap the matched words in <mark> (server-side twin of the client highlighter).
 *
 * @param string   $text  Plain text.
 * @param string[] $terms Search words.
 * @return string Escaped HTML.
 */
function lc_mdsx2026_search_highlight( $text, $terms ) {
	$text = esc_html( $text );
	if ( ! $terms ) {
		return $text;
	}

	$pattern = '/(' . implode( '|', array_map( static fn( $t ) => preg_quote( esc_html( $t ), '/' ), $terms ) ) . ')/iu';

	return (string) preg_replace( $pattern, '<mark>$1</mark>', $text );
}

/**
 * The search page itself stays out of the sitemap (its robots setting is handled by the SEO plugin).
 */
add_filter(
	'wp_sitemaps_posts_query_args',
	static function ( $args, $post_type ) {
		$page = 'page' === $post_type ? get_page_by_path( 'search' ) : null;
		if ( $page ) {
			$args['post__not_in'] = array_merge( (array) ( $args['post__not_in'] ?? array() ), array( $page->ID ) );
		}
		return $args;
	},
	10,
	2
);

/**
 * Free /search/ for the Search page: WordPress claims that path for its own results ("/search/term"),
 * so move the core search base aside, and send any core search (?s=term) to the Search page.
 */
add_action(
	'init',
	static function () {
		global $wp_rewrite;
		$wp_rewrite->search_base = 'find';
	}
);
add_action(
	'template_redirect',
	static function () {
		if ( is_search() ) {
			wp_safe_redirect( add_query_arg( 'q', rawurlencode( get_search_query( false ) ), lc_mdsx2026_page_url( 'search' ) ), 302 );
			exit;
		}
	}
);
