<?php
/**
 * Single post template.
 *
 * News articles are built from blocks (page hero, article body, press coverage…),
 * each rendering its own full-bleed section and .container, so — like page.php —
 * there is no wrapper, title or date here; the Page Hero block carries the h1 and
 * the Article Body block shows the date.
 *
 * @package lc-mdsx2026
 */

get_header();

while ( have_posts() ) {
	the_post();
	the_content();
}

get_footer();
