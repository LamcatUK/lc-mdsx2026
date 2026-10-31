<?php
/**
 * Single post template (News articles).
 *
 * Articles are built in the editor like every other page: a Page Hero block (title,
 * standfirst, and for posts a date / author / reading-time line), ordinary core blocks for
 * the story (Paragraph, Heading, Quote, Image, List…), then theme blocks such as In the
 * press, a related-link band and the call to action.
 *
 * Runs of consecutive core blocks are wrapped in one reading column; theme blocks render
 * full-bleed as normal.
 *
 * @package lc-mdsx2026
 */

get_header();

while ( have_posts() ) {
	the_post();

	$prose = '';
	$flush = static function () use ( &$prose ) {
		if ( '' !== trim( $prose ) ) {
			echo '<section class="news-prose section s-nero"><div class="container"><div class="news-prose__content">' . $prose . '</div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks.
		}
		$prose = '';
	};

	foreach ( parse_blocks( get_the_content() ) as $block ) {
		if ( empty( $block['blockName'] ) ) {
			continue; // Whitespace between blocks.
		}
		$html = render_block( $block );
		if ( 0 === strpos( $block['blockName'], 'lc-mdsx2026/' ) ) {
			$flush();
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered block.
		} else {
			$prose .= $html;
		}
	}
	$flush();
}

get_footer();
