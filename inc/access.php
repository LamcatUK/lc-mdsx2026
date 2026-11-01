<?php
/**
 * Gated portfolio content.
 *
 * Each project has one of three access states:
 *
 *  - Public.
 *  - Lead capture ("email"): the hero and intro stay public as a teaser; the case-study body is
 *    withheld until the visitor gives a valid email address in a Contact Form 7 form (which mails and
 *    stores it like any other form, cfdb7 included). Giving it once unlocks every lead-capture project
 *    for 90 days through a signed cookie. A soft gate, by design.
 *  - Password: WordPress's own post password (Status > Visibility > Password protected). The project
 *    is hidden everywhere (grid, selected work, reel, search, sitemap, REST) and the page shows only
 *    a password form, with an optional "request access" Contact Form 7 form.
 *
 * Separately, any project can be hidden from listings (grid, selected work, hero reel, picks, next
 * project, search, sitemap).
 *
 * The locked content is never sent to the browser: the_content is rewritten on the server (so the
 * REST API and feeds are covered too), so removing the modal or overlay in the inspector reveals
 * nothing. See src/js/project-gate.js and src/blocks/access-gate.css for the front end.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

const LC_MDSX2026_ACCESS_META  = '_lc_access';
const LC_MDSX2026_HIDE_META    = '_lc_hide_listing';
const LC_MDSX2026_REQUEST_META = '_lc_offer_request';
const LC_MDSX2026_LEAD_COOKIE  = 'lc_mdsx2026_lead';
const LC_MDSX2026_LEAD_DAYS    = 90;

/**
 * Access mode of a project: 'public', 'email' (lead capture) or 'password'.
 *
 * @param int|WP_Post $post Project.
 * @return string
 */
function lc_mdsx2026_access_mode( $post ) {
	$post = get_post( $post );
	if ( ! $post || 'portfolio' !== $post->post_type ) {
		return 'public';
	}
	if ( '' !== (string) $post->post_password ) {
		return 'password';
	}

	return 'email' === get_post_meta( $post->ID, LC_MDSX2026_ACCESS_META, true ) ? 'email' : 'public';
}

/**
 * Is the project kept out of every listing (password-protected, or hidden by choice)?
 *
 * @param int|WP_Post $post Project.
 * @return bool
 */
function lc_mdsx2026_is_unlisted( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	return 'password' === lc_mdsx2026_access_mode( $post ) || '1' === get_post_meta( $post->ID, LC_MDSX2026_HIDE_META, true );
}

/**
 * Add the "listed projects only" conditions to a get_posts()/WP_Query argument array.
 *
 * @param array $args Query arguments.
 * @return array
 */
function lc_mdsx2026_listing_args( $args ) {
	$args['has_password'] = false;

	$args['meta_query'] = array_merge( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small portfolio set.
		(array) ( $args['meta_query'] ?? array() ),
		array(
			array(
				'relation' => 'OR',
				array( 'key' => LC_MDSX2026_HIDE_META, 'compare' => 'NOT EXISTS' ),
				array( 'key' => LC_MDSX2026_HIDE_META, 'value' => '1', 'compare' => '!=' ),
			),
		)
	);

	return $args;
}

/**
 * Signed value for the lead cookie: "expiry|hash".
 *
 * @param int $expires Unix expiry.
 * @return string
 */
function lc_mdsx2026_lead_token( $expires ) {
	return $expires . '|' . hash_hmac( 'sha256', 'lead|' . $expires, wp_salt( 'auth' ) );
}

/**
 * Has this visitor already unlocked the lead-capture projects?
 *
 * @return bool
 */
function lc_mdsx2026_has_lead_access() {
	$cookie = isset( $_COOKIE[ LC_MDSX2026_LEAD_COOKIE ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ LC_MDSX2026_LEAD_COOKIE ] ) ) : '';
	$parts  = explode( '|', $cookie );

	return 2 === count( $parts ) && (int) $parts[0] > time() && hash_equals( lc_mdsx2026_lead_token( (int) $parts[0] ), $cookie );
}

/**
 * Should this request see the gate rather than the full project? Editors see the full page unless
 * they add ?lc_gate=1 to preview what a visitor sees.
 *
 * @param WP_Post $post Project.
 * @return bool
 */
function lc_mdsx2026_gate_applies( $post ) {
	if ( ! $post || 'email' !== lc_mdsx2026_access_mode( $post ) || is_admin() || lc_mdsx2026_has_lead_access() ) {
		return false;
	}
	if ( current_user_can( 'edit_post', $post->ID ) && empty( $_GET['lc_gate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- preview switch only.
		return false;
	}

	return true;
}

/**
 * Split a project's blocks into the public teaser (up to and including the Project Intro) and the
 * gated body, keeping a trailing Next Project / CTA public.
 *
 * @param WP_Post $post Project.
 * @return array[] { teaser: block[], tail: block[] }
 */
function lc_mdsx2026_gate_split( $post ) {
	$blocks = array_values(
		array_filter(
			parse_blocks( $post->post_content ),
			static function ( $block ) {
				return null !== $block['blockName'] || '' !== trim( $block['innerHTML'] );
			}
		)
	);

	$cut = 1;
	foreach ( $blocks as $i => $block ) {
		if ( 'lc-mdsx2026/project-intro' === $block['blockName'] ) {
			$cut = $i + 1;
			break;
		}
	}

	$tail_from = count( $blocks );
	while ( $tail_from > $cut && in_array( $blocks[ $tail_from - 1 ]['blockName'], array( 'lc-mdsx2026/next-project', 'lc-mdsx2026/cta-band' ), true ) ) {
		--$tail_from;
	}

	return array(
		'teaser' => array_slice( $blocks, 0, $cut ),
		'tail'   => array_slice( $blocks, $tail_from ),
	);
}

/**
 * Withhold the gated body of a lead-capture project on the server.
 *
 * @param string $content Post content (raw blocks).
 * @return string
 */
function lc_mdsx2026_gate_content( $content ) {
	$post = get_post();
	if ( ! $post || ! lc_mdsx2026_gate_applies( $post ) ) {
		return $content;
	}

	$parts = lc_mdsx2026_gate_split( $post );

	return serialize_blocks( $parts['teaser'] ) . "\n\n<!-- wp:lc-mdsx2026/access-gate /-->\n\n" . serialize_blocks( $parts['tail'] );
}
add_filter( 'the_content', 'lc_mdsx2026_gate_content', 8 );

/**
 * The gate itself (a server-rendered block, never stored in content): a placeholder where the case
 * study would be, a button, and a modal holding the lead-capture form.
 */
add_action(
	'init',
	static function () {
		register_block_type(
			'lc-mdsx2026/access-gate',
			array(
				'api_version'     => 3,
				'title'           => 'Access gate',
				'supports'        => array( 'inserter' => false ),
				'render_callback' => 'lc_mdsx2026_render_access_gate',
			)
		);
	}
);

/**
 * Print a Contact Form 7 form by the ID saved in Site-Wide Settings.
 *
 * @param string $setting Settings key holding the form ID.
 * @return string
 */
function lc_mdsx2026_access_form( $setting ) {
	$id   = (int) lc_mdsx2026_get_setting( $setting );
	$form = ( $id && class_exists( 'WPCF7_ContactForm' ) ) ? WPCF7_ContactForm::get_instance( $id ) : null;

	if ( $form ) {
		return do_shortcode( $form->shortcode() );
	}

	return current_user_can( 'edit_posts' ) ? '<p><em>' . esc_html__( 'Set the Contact Form 7 form ID in Site-Wide Settings (General).', 'lc-mdsx2026' ) . '</em></p>' : '';
}

/**
 * Render the lead-capture gate.
 *
 * @return string
 */
function lc_mdsx2026_render_access_gate() {
	ob_start();
	?>
	<section class="access-gate section s-nero" id="project-details">
		<div class="container">
			<div class="access-gate__ghost" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
			<div class="access-gate__card">
				<h2 class="baseline"><?php esc_html_e( 'See the full case study', 'lc-mdsx2026' ); ?></h2>
				<p class="t-lede"><?php esc_html_e( 'Enter your email address to unlock the process, the imagery and the credits for this project, and the rest of our case studies.', 'lc-mdsx2026' ); ?></p>
				<button class="btn btn--primary btn--l" type="button" data-dialog-target="project-gate"><?php esc_html_e( 'Unlock case study', 'lc-mdsx2026' ); ?><i class="arrow" aria-hidden="true"></i></button>
			</div>
		</div>
		<dialog id="project-gate" class="access-modal s-nero" aria-labelledby="project-gate-title" data-gate-auto>
			<button class="access-modal__close" type="button" data-dialog-close aria-label="<?php esc_attr_e( 'Close', 'lc-mdsx2026' ); ?>"><span aria-hidden="true">&times;</span></button>
			<h2 id="project-gate-title"><?php esc_html_e( 'Unlock this case study', 'lc-mdsx2026' ); ?></h2>
			<p><?php esc_html_e( 'Where should we send our latest work? One address unlocks every case study.', 'lc-mdsx2026' ); ?></p>
			<div class="access-modal__form contact-form" data-gate-form>
				<?php echo lc_mdsx2026_access_form( 'gate_form_id' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contact Form 7 output. ?>
			</div>
		</dialog>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Give the signed cookie when the lead-capture form passes validation and spam checks. Runs inside
 * Contact Form 7's REST call, before any output, so the cookie reaches the browser with the response.
 * A failed email delivery still unlocks: the visitor gave a valid address, the mail problem is ours.
 *
 * @param WPCF7_ContactForm $form   Form that was sent.
 * @param array             $result Submission result (status, message …).
 * @return void
 */
function lc_mdsx2026_grant_lead_access( $form, $result ) {
	$gate_form = (int) lc_mdsx2026_get_setting( 'gate_form_id' );
	if ( ! $gate_form || (int) $form->id() !== $gate_form || headers_sent() || ! in_array( $result['status'] ?? '', array( 'mail_sent', 'mail_failed' ), true ) ) {
		return;
	}

	$expires = time() + LC_MDSX2026_LEAD_DAYS * DAY_IN_SECONDS;
	setcookie(
		LC_MDSX2026_LEAD_COOKIE,
		lc_mdsx2026_lead_token( $expires ),
		array(
			'expires'  => $expires,
			'path'     => COOKIEPATH ? COOKIEPATH : '/',
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		)
	);
}
add_action( 'wpcf7_submit', 'lc_mdsx2026_grant_lead_access', 10, 2 );

/**
 * Password projects: remember a correct password for 30 days (core default is 10).
 *
 * @return int
 */
add_filter(
	'post_password_expires',
	static function () {
		return time() + 30 * DAY_IN_SECONDS;
	}
);

/**
 * The password page: a heading that doesn't name the project, the password form, and (if switched
 * on for this project) a request-access form.
 *
 * @param string  $output Core's form markup.
 * @param WP_Post $post   Project.
 * @return string
 */
function lc_mdsx2026_password_form( $output, $post ) {
	if ( ! $post || 'portfolio' !== $post->post_type ) {
		return $output;
	}

	$wrong = isset( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] ); // Core sets the cookie even for a wrong password.
	$id    = wp_unique_id( 'access-pw-' );

	ob_start();
	?>
	<section class="access-private page-hero s-nero">
		<div class="container access-private__inner">
			<h1 class="baseline"><?php esc_html_e( 'This project is private', 'lc-mdsx2026' ); ?></h1>
			<p class="t-subhead"><?php esc_html_e( 'Enter the password you were given to view it.', 'lc-mdsx2026' ); ?></p>
			<form class="access-private__form" action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post">
				<label class="visually-hidden" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Password', 'lc-mdsx2026' ); ?></label>
				<input type="password" name="post_password" id="<?php echo esc_attr( $id ); ?>" placeholder="<?php esc_attr_e( 'Password', 'lc-mdsx2026' ); ?>" autocomplete="off" required <?php echo $wrong ? 'aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-error"' : ''; ?>>
				<button class="btn btn--primary" type="submit"><?php esc_html_e( 'View project', 'lc-mdsx2026' ); ?><i class="arrow" aria-hidden="true"></i></button>
			</form>
			<?php if ( $wrong ) { ?>
				<p class="access-private__error" id="<?php echo esc_attr( $id ); ?>-error" role="alert"><?php esc_html_e( 'That password is not right. Please try again.', 'lc-mdsx2026' ); ?></p>
			<?php } ?>
			<?php if ( '1' === get_post_meta( $post->ID, LC_MDSX2026_REQUEST_META, true ) && (int) lc_mdsx2026_get_setting( 'access_form_id' ) ) { ?>
				<div class="access-private__request">
					<h2 class="t-h3"><?php esc_html_e( 'Need access?', 'lc-mdsx2026' ); ?></h2>
					<p><?php esc_html_e( 'Tell us who you are and we will send you the password.', 'lc-mdsx2026' ); ?></p>
					<div class="contact-form"><?php echo lc_mdsx2026_access_form( 'access_form_id' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contact Form 7 output. ?></div>
				</div>
			<?php } ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_filter( 'the_password_form', 'lc_mdsx2026_password_form', 10, 2 );

/**
 * Password pages and gated projects: no caching, nothing that names the hidden project in the head,
 * and no indexing.
 */
add_action(
	'template_redirect',
	static function () {
		if ( ! is_singular( 'portfolio' ) ) {
			return;
		}

		$post = get_queried_object();
		$mode = lc_mdsx2026_access_mode( $post );
		if ( 'public' !== $mode ) {
			nocache_headers();
		}

		if ( 'password' === $mode && post_password_required( $post ) ) {
			// The SEO plugin would print this project's own title, description, image and schema.
			remove_action( 'wp_head', 'lcp_yeast_seo_render_frontend_meta', 1 );
			remove_action( 'wp_head', 'lcp_yeast_seo_render', 20 );
			add_filter(
				'pre_get_document_title',
				static function () {
					return __( 'Private project', 'lc-mdsx2026' ) . ' | ' . get_bloginfo( 'name' );
				},
				99
			);
			add_filter(
				'wp_robots',
				static function ( $robots ) {
					unset( $robots['index'] );
					$robots['noindex']  = true;
					$robots['nofollow'] = true;
					return $robots;
				},
				99
			);
		}
	}
);

/**
 * Keep the project's titles clear of core's "Protected:" prefix.
 */
add_filter(
	'protected_title_format',
	static function () {
		return '%s';
	}
);

/**
 * REST: unlisted projects are invisible to visitors (not in the collection, 404 by ID).
 */
add_filter(
	'rest_portfolio_query',
	static function ( $args ) {
		return current_user_can( 'edit_posts' ) ? $args : lc_mdsx2026_listing_args( $args );
	}
);
add_filter(
	'rest_pre_dispatch',
	static function ( $result, $server, $request ) {
		if ( null === $result && preg_match( '#^/wp/v2/portfolio/(\d+)#', $request->get_route(), $m ) && lc_mdsx2026_is_unlisted( (int) $m[1] ) && ! current_user_can( 'edit_post', (int) $m[1] ) ) {
			return new WP_Error( 'rest_post_invalid_id', __( 'Invalid post ID.', 'lc-mdsx2026' ), array( 'status' => 404 ) );
		}
		return $result;
	},
	10,
	3
);

/* ---------------------------------------------------------------------------------------------
 * Editor: the "Project access" box.
 * ------------------------------------------------------------------------------------------- */

add_action(
	'add_meta_boxes_portfolio',
	static function () {
		add_meta_box( 'lc-mdsx2026-access', __( 'Project access', 'lc-mdsx2026' ), 'lc_mdsx2026_render_access_box', 'portfolio', 'side', 'high' );
	}
);

/**
 * Render the Project access meta box.
 *
 * @param WP_Post $post Project.
 * @return void
 */
function lc_mdsx2026_render_access_box( $post ) {
	wp_nonce_field( 'lc_mdsx2026_access', 'lc_mdsx2026_access_nonce' );
	$mode = lc_mdsx2026_access_mode( $post );
	?>
	<p><strong><?php esc_html_e( 'Who can read the case study?', 'lc-mdsx2026' ); ?></strong></p>
	<p>
		<label><input type="radio" name="lc_access" value="" <?php checked( 'email' !== $mode ); ?> <?php disabled( 'password' === $mode ); ?>> <?php esc_html_e( 'Everyone', 'lc-mdsx2026' ); ?></label><br>
		<label><input type="radio" name="lc_access" value="email" <?php checked( 'email', $mode ); ?> <?php disabled( 'password' === $mode ); ?>> <?php esc_html_e( 'Lead capture: teaser, then an email address', 'lc-mdsx2026' ); ?></label>
	</p>
	<?php if ( 'password' === $mode ) { ?>
		<p class="description"><strong><?php esc_html_e( 'Password protected.', 'lc-mdsx2026' ); ?></strong> <?php esc_html_e( 'Hidden everywhere; visitors only see a password form. Change it under Status → Visibility.', 'lc-mdsx2026' ); ?></p>
		<p><label><input type="checkbox" name="lc_request" value="1" <?php checked( '1', get_post_meta( $post->ID, LC_MDSX2026_REQUEST_META, true ) ); ?>> <?php esc_html_e( 'Offer a "request access" form', 'lc-mdsx2026' ); ?></label></p>
	<?php } else { ?>
		<p class="description"><?php esc_html_e( 'For a private, invite-only project, set a password under Status → Visibility → Password protected.', 'lc-mdsx2026' ); ?></p>
		<input type="hidden" name="lc_request" value="<?php echo esc_attr( get_post_meta( $post->ID, LC_MDSX2026_REQUEST_META, true ) ); ?>">
	<?php } ?>
	<hr>
	<p><label><input type="checkbox" name="lc_hide" value="1" <?php checked( '1', get_post_meta( $post->ID, LC_MDSX2026_HIDE_META, true ) ); ?> <?php disabled( 'password' === $mode ); ?>> <?php esc_html_e( 'Hide from listings', 'lc-mdsx2026' ); ?></label></p>
	<p class="description"><?php esc_html_e( 'Keeps it out of the portfolio grid, selected work, the hero reel, search and the sitemap. The page still works for anyone with the link.', 'lc-mdsx2026' ); ?></p>
	<?php if ( 'email' === $mode ) { ?>
		<p class="description"><a href="<?php echo esc_url( add_query_arg( 'lc_gate', '1', get_permalink( $post ) ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Preview as a visitor', 'lc-mdsx2026' ); ?></a></p>
	<?php } ?>
	<?php
}

/**
 * Save the Project access box, and keep the SEO plugin's indexing setting in step: an unlisted project
 * is set to noindex automatically (and put back only if we were the ones who set it).
 *
 * @param int $post_id Project ID.
 * @return void
 */
function lc_mdsx2026_save_access( $post_id ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['lc_mdsx2026_access_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lc_mdsx2026_access_nonce'] ) ), 'lc_mdsx2026_access' ) ) {
		if ( ! empty( $_POST['lc_access'] ) && 'email' === $_POST['lc_access'] ) {
			update_post_meta( $post_id, LC_MDSX2026_ACCESS_META, 'email' );
		} elseif ( isset( $_POST['lc_access'] ) ) {
			delete_post_meta( $post_id, LC_MDSX2026_ACCESS_META );
		}
		if ( ! empty( $_POST['lc_hide'] ) ) {
			update_post_meta( $post_id, LC_MDSX2026_HIDE_META, '1' );
		} elseif ( 'password' !== lc_mdsx2026_access_mode( $post_id ) ) {
			delete_post_meta( $post_id, LC_MDSX2026_HIDE_META ); // The box's checkbox is disabled (not sent) for password projects.
		}
		if ( ! empty( $_POST['lc_request'] ) ) {
			update_post_meta( $post_id, LC_MDSX2026_REQUEST_META, '1' );
		} else {
			delete_post_meta( $post_id, LC_MDSX2026_REQUEST_META );
		}
	}

	if ( defined( 'LCP_YEAST_SEO_ROBOTS_META_KEY' ) ) {
		if ( lc_mdsx2026_is_unlisted( $post_id ) ) {
			if ( 'noindex' !== get_post_meta( $post_id, LCP_YEAST_SEO_ROBOTS_META_KEY, true ) ) {
				update_post_meta( $post_id, LCP_YEAST_SEO_ROBOTS_META_KEY, 'noindex' );
				update_post_meta( $post_id, '_lc_auto_noindex', '1' );
			}
		} elseif ( get_post_meta( $post_id, '_lc_auto_noindex', true ) ) {
			delete_post_meta( $post_id, LCP_YEAST_SEO_ROBOTS_META_KEY );
			delete_post_meta( $post_id, '_lc_auto_noindex' );
		}
	}
}
add_action( 'save_post_portfolio', 'lc_mdsx2026_save_access', 99 );

/**
 * The Portfolio page's stored schema lists its projects; rebuild that list from the listed projects
 * whenever a project is saved, so a hidden or password-protected one is never named in the page source.
 *
 * @return void
 */
function lc_mdsx2026_refresh_portfolio_schema() {
	$page = get_page_by_path( 'portfolio' );
	if ( ! $page || ! defined( 'LCP_YEAST_SEO_SCHEMA_META_KEY' ) ) {
		return;
	}

	$data = json_decode( (string) get_post_meta( $page->ID, LCP_YEAST_SEO_SCHEMA_META_KEY, true ), true );
	if ( ! is_array( $data ) || empty( $data['mainEntity']['itemListElement'] ) ) {
		return;
	}

	$works = get_posts(
		lc_mdsx2026_listing_args(
			array(
				'post_type'      => 'portfolio',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			)
		)
	);

	$data['mainEntity']['itemListElement'] = array_map(
		static function ( $work, $i ) {
			return array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'url'      => get_permalink( $work ),
				'name'     => html_entity_decode( get_the_title( $work ), ENT_QUOTES, 'UTF-8' ),
			);
		},
		$works,
		array_keys( $works )
	);

	update_post_meta( $page->ID, LCP_YEAST_SEO_SCHEMA_META_KEY, wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) ) );
}
add_action( 'save_post_portfolio', 'lc_mdsx2026_refresh_portfolio_schema', 100 );
