<?php
/**
 * Contact Form 7 integration: the Contact Form block embeds a CF7 form, and the
 * theme supplies the styling (css/theme.min.css) instead of CF7's own stylesheet.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

// The theme styles CF7 markup itself (src/blocks/contact-form.css).
add_filter( 'wpcf7_load_css', '__return_false' );

// The form template is written as explicit <p>/<label> markup; don't let CF7 add its own paragraphs and <br>s.
add_filter( 'wpcf7_autop_or_not', '__return_false' );
