<?php
/**
 * Page template.
 *
 * Page layouts are built from native blocks, each of which renders its own
 * full-bleed section and .container — so no wrapper here.
 *
 * @package lc-mdsx2026
 */

get_header();

while ( have_posts() ) {
	the_post();
	the_content();
}

get_footer();
