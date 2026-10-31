<?php
/**
 * Site-Wide Settings page — plain Settings API, one array option
 * (lc_mdsx2026_site_settings). Replaces the old ACF options page; read
 * values elsewhere in the theme with lc_mdsx2026_get_setting( $key ).
 *
 * The Icons tab (SVG upload straight into img/icons/) from the ACF version
 * of this page is deliberately not ported here — deferred to a future
 * plugin rather than rebuilt as part of dropping ACF.
 *
 * Includes field types beyond plain text/email/url inputs — `textarea`/
 * `code` (multi-line values, e.g. a pasted vendor script) and `checkbox` —
 * plus a generic tabs pattern once there are enough sections to make one
 * long scrolling page unwieldy. The Scripts tab's three raw markup
 * slots (`custom_head`, `custom_body_open`, `custom_body_close`), gated by
 * `custom_scripts_logged_out_only`, are printed unescaped by design — see
 * inc/head-tags.php.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Option name for the single serialized settings array.
 *
 * @var string
 */
define( 'LC_MDSX2026_SETTINGS_OPTION', 'lc_mdsx2026_site_settings' );

/**
 * Read one Site-Wide Settings value.
 *
 * @param string $key     Setting key, e.g. 'ga_property'.
 * @param string $default Fallback if the key isn't set.
 * @return string
 */
function lc_mdsx2026_get_setting( $key, $default = '' ) {
	$settings = get_option( LC_MDSX2026_SETTINGS_OPTION, array() );
	return isset( $settings[ $key ] ) && '' !== $settings[ $key ] ? $settings[ $key ] : $default;
}

/**
 * Register the settings page, section, and fields.
 *
 * @return void
 */
function lc_mdsx2026_register_settings_page() {
	add_menu_page(
		'Site-Wide Settings',
		'Site-Wide Settings',
		'edit_posts',
		'theme-general-settings',
		'lc_mdsx2026_render_settings_page',
		'dashicons-admin-generic',
		80
	);

	register_setting( 'lc_mdsx2026_settings', LC_MDSX2026_SETTINGS_OPTION );

	add_settings_section( 'lc_mdsx2026_general', 'General', '__return_false', 'theme-general-settings' );
	add_settings_section( 'lc_mdsx2026_tracking', 'Tracking & Verification', '__return_false', 'theme-general-settings' );
	add_settings_section( 'lc_mdsx2026_scripts', 'Scripts', '__return_false', 'theme-general-settings' );

	$fields = array(
		'email'                     => array(
			'label'   => 'Email',
			'type'    => 'email',
			'section' => 'lc_mdsx2026_general',
		),
		'phone'                     => array(
			'label'   => 'Phone',
			'type'    => 'text',
			'section' => 'lc_mdsx2026_general',
		),
		'facebook_url'              => array(
			'label'       => 'Facebook URL',
			'type'        => 'url',
			'section'     => 'lc_mdsx2026_general',
			'placeholder' => 'https://facebook.com/...',
			'description' => 'Leave blank to hide this icon from [social_icons].',
		),
		'instagram_url'             => array(
			'label'       => 'Instagram URL',
			'type'        => 'url',
			'section'     => 'lc_mdsx2026_general',
			'placeholder' => 'https://instagram.com/...',
			'description' => 'Leave blank to hide this icon from [social_icons].',
		),
		'linkedin_url'              => array(
			'label'       => 'LinkedIn URL',
			'type'        => 'url',
			'section'     => 'lc_mdsx2026_general',
			'placeholder' => 'https://linkedin.com/company/...',
			'description' => 'Leave blank to hide this icon from [social_icons].',
		),
		'ga_property'               => array(
			'label'       => 'GA Property',
			'type'        => 'text',
			'section'     => 'lc_mdsx2026_tracking',
			'placeholder' => 'G-XXXXXXX',
			'description' => 'Google Analytics measurement ID. Only fires for logged-out visitors.',
		),
		'gtm_property'              => array(
			'label'       => 'GTM Property',
			'type'        => 'text',
			'section'     => 'lc_mdsx2026_tracking',
			'placeholder' => 'GTM-XXXXXXX',
			'description' => 'Google Tag Manager container ID. Only fires for logged-out visitors.',
		),
		'google_site_verification'  => array(
			'label'       => 'Google Site Verification',
			'type'        => 'text',
			'section'     => 'lc_mdsx2026_tracking',
			'description' => 'Content value of the google-site-verification meta tag.',
		),
		'bing_site_verification'    => array(
			'label'       => 'Bing Site Verification',
			'type'        => 'text',
			'section'     => 'lc_mdsx2026_tracking',
			'description' => 'Content value of the msvalidate.01 meta tag.',
		),
		'custom_head'               => array(
			'label'       => 'Head',
			'type'        => 'code',
			'section'     => 'lc_mdsx2026_scripts',
			'description' => 'Printed in <head> after the managed GA/GTM tags. Paste vendor snippets verbatim, <script> tags and all — nothing is escaped or filtered.',
		),
		'custom_body_open'          => array(
			'label'       => 'Body Open',
			'type'        => 'code',
			'section'     => 'lc_mdsx2026_scripts',
			'description' => 'Printed immediately after <body> opens, after the GTM noscript fallback. Where <noscript> tracking pixels belong.',
		),
		'custom_body_close'         => array(
			'label'       => 'Body Close',
			'type'        => 'code',
			'section'     => 'lc_mdsx2026_scripts',
			'description' => 'Printed just before </body>. Use for anything that must not block rendering — chat widgets, late-loading embeds.',
		),
		'custom_scripts_logged_out_only' => array(
			'label'       => 'Logged-Out Visitors Only',
			'type'        => 'checkbox',
			'section'     => 'lc_mdsx2026_scripts',
			'default'     => '1',
			'description' => 'On by default, matching GA/GTM — keeps the team\'s own traffic out of whatever these scripts measure. Untick if a slot holds something every visitor should see, e.g. a chat widget.',
		),
	);

	foreach ( $fields as $key => $field ) {
		add_settings_field(
			$key,
			$field['label'],
			'lc_mdsx2026_render_settings_field',
			'theme-general-settings',
			$field['section'],
			array_merge( $field, array( 'key' => $key ) )
		);
	}
}
add_action( 'admin_menu', 'lc_mdsx2026_register_settings_page' );

/**
 * Enqueue the settings-page admin script, settings page only.
 *
 * @param string $hook_suffix Current admin page hook.
 * @return void
 */
function lc_mdsx2026_settings_page_assets( $hook_suffix ) {
	if ( 'toplevel_page_theme-general-settings' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_script(
		'lc-mdsx2026-tabs',
		get_stylesheet_directory_uri() . '/js/tabs.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'lc_mdsx2026_settings_page_assets' );

/**
 * Render a single settings field — text/email/url input, a textarea or a
 * checkbox.
 *
 * @param array $args Field args: key, type, placeholder, description.
 * @return void
 */
function lc_mdsx2026_render_settings_field( $args ) {
	if ( 'textarea' === $args['type'] || 'code' === $args['type'] ) {
		lc_mdsx2026_render_textarea_field( $args );
		return;
	}

	if ( 'checkbox' === $args['type'] ) {
		lc_mdsx2026_render_checkbox_field( $args );
		return;
	}

	$value = lc_mdsx2026_get_setting( $args['key'] );
	?>
	<input
		type="<?php echo esc_attr( $args['type'] ); ?>"
		id="<?php echo esc_attr( $args['key'] ); ?>"
		name="<?php echo esc_attr( LC_MDSX2026_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $args['key'] ); ?>]"
		value="<?php echo esc_attr( $value ); ?>"
		placeholder="<?php echo esc_attr( $args['placeholder'] ?? '' ); ?>"
		class="regular-text"
	>
	<?php
	if ( ! empty( $args['description'] ) ) {
		?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Render a `textarea`- or `code`-type field — for multi-line values like a
 * postal address or a pasted vendor script. `<input type="textarea">` is not
 * a real input type (browsers silently degrade it to a single-line
 * `type="text"`), so this needs its own branch rather than falling through to
 * the generic input above.
 *
 * `code` differs only in presentation — monospace, no spellcheck/autocorrect,
 * and taller by default. The stored value is a plain string either way;
 * nothing here decides whether it's escaped on output, that's the caller's
 * job (see inc/head-tags.php, which prints the script slots verbatim).
 *
 * @param array $args Field args: key, type, placeholder, rows, description.
 * @return void
 */
function lc_mdsx2026_render_textarea_field( $args ) {
	$value   = lc_mdsx2026_get_setting( $args['key'] );
	$is_code = 'code' === $args['type'];
	?>
	<textarea
		id="<?php echo esc_attr( $args['key'] ); ?>"
		name="<?php echo esc_attr( LC_MDSX2026_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $args['key'] ); ?>]"
		placeholder="<?php echo esc_attr( $args['placeholder'] ?? '' ); ?>"
		rows="<?php echo (int) ( $args['rows'] ?? ( $is_code ? 10 : 4 ) ); ?>"
		class="large-text"
		<?php if ( $is_code ) : ?>
		spellcheck="false"
		autocapitalize="off"
		autocomplete="off"
		style="font-family: Consolas, Monaco, monospace; font-size: 12px; white-space: pre; overflow-wrap: normal; overflow-x: auto;"
		<?php endif; ?>
	><?php echo esc_textarea( $value ); ?></textarea>
	<?php
	if ( ! empty( $args['description'] ) ) {
		?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Render a `checkbox`-type field.
 *
 * An unticked checkbox posts nothing at all, which is indistinguishable from
 * "never saved" — so a paired hidden input submits '0' first and the checkbox
 * overwrites it with '1' when ticked. That's what makes a default-on checkbox
 * possible: absent means genuinely-never-saved (fall back to `default`),
 * '0' means deliberately unticked.
 *
 * Note this only works because lc_mdsx2026_get_setting() treats '' as
 * unset but returns '0' as-is — and '0' is falsey in PHP, so callers can just
 * test the returned value.
 *
 * @param array $args Field args: key, label, default, description.
 * @return void
 */
function lc_mdsx2026_render_checkbox_field( $args ) {
	$value = lc_mdsx2026_get_setting( $args['key'], $args['default'] ?? '0' );
	$name  = sprintf( '%s[%s]', LC_MDSX2026_SETTINGS_OPTION, $args['key'] );
	?>
	<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0">
	<label>
		<input
			type="checkbox"
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			value="1"
			<?php checked( '1', $value ); ?>
		>
		<?php echo esc_html( $args['checkbox_label'] ?? 'Enabled' ); ?>
	</label>
	<?php
	if ( ! empty( $args['description'] ) ) {
		?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Settings page HTML — one tab per registered section, using
 * js/tabs.js's generic [data-tabs] contract (see that file's docblock)
 * rather than anything Settings-API-specific, so the same markup pattern
 * can be reused wherever tabs are next needed, including a future
 * block-editor equivalent.
 *
 * Replaces do_settings_sections() with a manual per-section loop — that
 * function always renders every section for a page in one continuous flow,
 * with no way to render one section at a time into its own tab panel.
 *
 * @return void
 */
function lc_mdsx2026_render_settings_page() {
	global $wp_settings_sections;

	$sections = $wp_settings_sections['theme-general-settings'] ?? array();
	?>
	<div class="wrap">
		<h1>Site-Wide Settings</h1>
		<form action="options.php" method="post">
			<?php settings_fields( 'lc_mdsx2026_settings' ); ?>
			<div class="lc-mdsx2026-tabs" data-tabs>
				<h2 class="nav-tab-wrapper" data-tabs-nav>
					<?php
					$is_first = true;
					foreach ( $sections as $section_id => $section ) {
						$class = 'nav-tab' . ( $is_first ? ' nav-tab-active' : '' );
						?>
					<a href="#" class="<?php echo esc_attr( $class ); ?>" data-tabs-target="<?php echo esc_attr( $section_id ); ?>"><?php echo esc_html( $section['title'] ); ?></a>
						<?php
						$is_first = false;
					}
					?>
				</h2>
				<?php
				$is_first = true;
				foreach ( $sections as $section_id => $section ) {
					?>
				<div class="lc-mdsx2026-tabs__panel" data-tabs-panel="<?php echo esc_attr( $section_id ); ?>" style="padding-top: 20px;" <?php echo $is_first ? '' : 'hidden'; ?>>
					<?php
					if ( is_callable( $section['callback'] ) ) {
						call_user_func( $section['callback'], $section );
					}
					?>
					<table class="form-table" role="presentation">
						<?php do_settings_fields( 'theme-general-settings', $section_id ); ?>
					</table>
				</div>
					<?php
					$is_first = false;
				}
				?>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
