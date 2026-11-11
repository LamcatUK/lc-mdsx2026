<?php
/**
 * Portfolio project page ("journey" template) support: image sizes, the stage list that drives the
 * Journey Nav and the numbered stage heads, video markup shared by the hero, film and reels, and the
 * global lightbox.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Responsive image sizes for project galleries (long edge 640, 1024, 1600, 2400). Existing uploads
 * need `wp media regenerate --yes` once to gain them.
 *
 * @return void
 */
function lc_mdsx2026_portfolio_image_sizes() {
	foreach ( array( 640, 1024, 1600, 2400 ) as $width ) {
		add_image_size( 'portfolio-' . $width, $width, $width );
	}
}
add_action( 'after_setup_theme', 'lc_mdsx2026_portfolio_image_sizes' );

/**
 * Block names that are journey stages, with their default labels.
 *
 * @return string[]
 */
function lc_mdsx2026_stage_defaults() {
	return array(
		'lc-mdsx2026/stage-sketchbook' => __( 'Concept', 'lc-mdsx2026' ),
		'lc-mdsx2026/stage-drawings'   => __( 'Design', 'lc-mdsx2026' ),
		'lc-mdsx2026/stage-build'      => __( 'Build', 'lc-mdsx2026' ),
		'lc-mdsx2026/stage-community'  => __( 'Community', 'lc-mdsx2026' ),
		'lc-mdsx2026/stage-opening'    => __( 'Opening day', 'lc-mdsx2026' ),
	);
}

/**
 * The stages on the current post, in page order: [ { id, label, upcoming, date } ].
 * Read from the post content so the Journey Nav (which renders above the stages) knows them all.
 *
 * @return array[]
 */
function lc_mdsx2026_page_stages() {
	static $cache = array();
	$post = get_post();
	if ( ! $post ) {
		return array();
	}
	if ( isset( $cache[ $post->ID ] ) ) {
		return $cache[ $post->ID ];
	}

	$defaults = lc_mdsx2026_stage_defaults();
	$stages   = array();
	$used     = array();
	$walk     = static function ( $blocks ) use ( &$walk, &$stages, &$used, $defaults ) {
		foreach ( $blocks as $block ) {
			if ( isset( $defaults[ $block['blockName'] ] ) ) {
				$attrs = $block['attrs'];
				$label = ! empty( $attrs['label'] ) ? $attrs['label'] : $defaults[ $block['blockName'] ];
				$slug  = sanitize_title( $label );
				$slug  = $slug ? $slug : 'stage';
				$id    = $slug;
				for ( $n = 2; isset( $used[ $id ] ); $n++ ) {
					$id = $slug . '-' . $n;
				}
				$used[ $id ] = true;
				$stages[]    = array(
					'id'       => $id,
					'label'    => $label,
					'upcoming' => 'upcoming' === ( $attrs['state'] ?? '' ),
					'date'     => $attrs['openingDate'] ?? '',
				);
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};
	$walk( parse_blocks( $post->post_content ) );

	$cache[ $post->ID ] = $stages;
	return $stages;
}

/**
 * The next stage in render order (stage blocks call this once each, so it lines up with
 * lc_mdsx2026_page_stages()).
 *
 * @param string $default_label Label to use if the block is not found in the post content.
 * @return array { id, label, number }
 */
function lc_mdsx2026_next_stage( $default_label ) {
	static $counter = array();
	$post_id = get_the_ID();
	$i       = $counter[ $post_id ] ?? 0;
	$stages  = lc_mdsx2026_page_stages();
	$counter[ $post_id ] = $i + 1;

	$stage           = $stages[ $i ] ?? array( 'id' => sanitize_title( $default_label ) . '-' . ( $i + 1 ), 'label' => $default_label );
	$stage['number'] = $i + 1;
	return $stage;
}

/**
 * Wrapper attributes for a stage block: id, data-stage and the surface class.
 *
 * @param array  $stage   From lc_mdsx2026_next_stage().
 * @param string $name    Block slug.
 * @param string $surface Surface slug.
 * @param string $extra   Extra classes.
 * @return string
 */
function lc_mdsx2026_stage_attrs( $stage, $name, $surface = '', $extra = '' ) {
	return get_block_wrapper_attributes(
		array(
			'class'      => trim( $name . ' stage ' . ( $surface ? 's-' . lc_mdsx2026_surface( $surface ) : '' ) . ' ' . $extra ),
			'id'         => $stage['id'],
			'data-stage' => $stage['label'],
		)
	);
}

/**
 * Print a stage head: numbered parallelogram marker, heading and caption.
 *
 * @param array  $stage   From lc_mdsx2026_next_stage().
 * @param string $caption Caption (may be empty).
 * @return void
 */
function lc_mdsx2026_stage_head( $stage, $caption = '' ) {
	?>
	<div class="stage-head reveal">
		<span class="stage-head__num" aria-hidden="true"><?php echo (int) $stage['number']; ?></span>
		<h2 class="stage-head__title"><?php echo esc_html( $stage['label'] ); ?></h2>
		<?php if ( $caption ) { ?>
			<p class="stage-head__caption"><?php echo esc_html( $caption ); ?></p>
		<?php } ?>
	</div>
	<?php
}

/**
 * URL of an attachment, or a stored URL when the attachment is gone.
 *
 * @param int    $id       Attachment ID.
 * @param string $fallback Fallback URL.
 * @return string
 */
function lc_mdsx2026_media_url( $id, $fallback = '' ) {
	$url = $id ? wp_get_attachment_url( (int) $id ) : '';
	return $url ? $url : (string) $fallback;
}

/**
 * Lightbox attributes for a gallery image: `data-lb` plus the stage label.
 *
 * @param string $stage Stage label.
 * @return array
 */
function lc_mdsx2026_lb_attrs( $stage ) {
	$GLOBALS['lc_mdsx2026_lightbox'] = true;
	return array(
		'data-lb'    => '',
		'data-lb-stage' => $stage,
	);
}

/**
 * Print a gallery image that opens in the lightbox.
 *
 * @param int    $id    Attachment ID.
 * @param string $url   URL fallback.
 * @param string $alt   Alt text.
 * @param string $stage Stage label shown in the lightbox.
 * @param string $sizes The `sizes` attribute for this slot.
 * @param string $size  Registered image size.
 * @return void
 */
function lc_mdsx2026_lb_image( $id, $url, $alt, $stage, $sizes = '(min-width: 1320px) 1320px, 100vw', $size = 'portfolio-1600' ) {
	$attrs = lc_mdsx2026_lb_attrs( $stage );
	if ( $id ) {
		$alt = '' !== $alt ? $alt : (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		echo wp_get_attachment_image( $id, $size, false, array_merge( array( 'alt' => $alt, 'loading' => 'lazy', 'sizes' => $sizes ), $attrs ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup.
	} elseif ( $url ) {
		printf( '<img src="%s" alt="%s" loading="lazy" decoding="async" data-lb data-lb-stage="%s">', esc_url( $url ), esc_attr( $alt ), esc_attr( $stage ) );
	}
}

/**
 * Width / height of a clip, from the media library's video metadata (0 if unknown).
 *
 * @param array $v Video fields (webm / mp4 attachment IDs).
 * @return float
 */
function lc_mdsx2026_video_ratio( $v ) {
	foreach ( array( 'mp4', 'webm' ) as $key ) {
		$meta = ! empty( $v[ $key ] ) ? wp_get_attachment_metadata( (int) $v[ $key ] ) : array();
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			return $meta['width'] / $meta['height'];
		}
	}
	return 0;
}

/**
 * Print a <video> (or, for a Vimeo-only film, nothing: the iframe is added by JS after the play click).
 *
 * `$v` keys: webm, mp4 (attachment IDs), webmUrl, mp4Url (fallbacks), poster / posterUrl, captions (VTT attachment ID),
 * label (aria-label). `$mode`: 'loop' (muted, autoplaying, paused offscreen) or 'film' (click to play, sound, controls).
 *
 * @param array  $v    Video fields.
 * @param string $mode 'loop' or 'film'.
 * @return bool True if a video element was printed.
 */
function lc_mdsx2026_video( $v, $mode = 'film' ) {
	$webm = lc_mdsx2026_media_url( $v['webm'] ?? 0, $v['webmUrl'] ?? '' );
	$mp4  = lc_mdsx2026_media_url( $v['mp4'] ?? 0, $v['mp4Url'] ?? '' );
	if ( ! $webm && ! $mp4 ) {
		return false;
	}
	$poster = lc_mdsx2026_media_url( $v['poster'] ?? 0, $v['posterUrl'] ?? '' );
	$vtt    = lc_mdsx2026_media_url( $v['captions'] ?? 0 );
	$label  = $v['label'] ?? '';

	if ( 'loop' === $mode ) {
		$attrs = 'data-video="loop" muted loop playsinline autoplay preload="metadata"';
	} else {
		$attrs = 'data-video="film" playsinline preload="none"';
	}
	$ratio = lc_mdsx2026_video_ratio( $v );
	// A clip in a slider or grid can open in the lightbox (on phones; src/js/lightbox.js).
	$lb = '';
	if ( 'loop' === $mode && ! empty( $v['lbStage'] ) ) {
		$GLOBALS['lc_mdsx2026_lightbox'] = true;
		$lb = ' data-lb-video data-lb-stage="' . esc_attr( $v['lbStage'] ) . '"';
	}
	printf(
		'<video %s%s%s%s%s>',
		$attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static string.
		$lb, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		$poster ? ' poster="' . esc_url( $poster ) . '"' : '',
		$label ? ' aria-label="' . esc_attr( $label ) . '"' : '',
		$ratio ? ' style="--ar:' . esc_attr( round( $ratio, 4 ) ) . '"' : ''
	);
	if ( $webm ) {
		printf( '<source src="%s" type="video/webm">', esc_url( $webm ) );
	}
	if ( $mp4 ) {
		printf( '<source src="%s" type="video/mp4">', esc_url( $mp4 ) );
	}
	if ( $vtt ) {
		printf( '<track kind="captions" src="%s" srclang="en" label="English">', esc_url( $vtt ) );
	}
	echo '</video>';

	// WCAG 2.2.2: a visible pause control on any loop (JS only shows it when the clip runs over 5 seconds).
	if ( 'loop' === $mode ) {
		printf(
			'<button class="video-pause" type="button" data-video-pause aria-pressed="false" hidden><span class="visually-hidden">%s</span><i aria-hidden="true"></i></button>',
			esc_html__( 'Pause video', 'lc-mdsx2026' )
		);
	}
	return true;
}

/**
 * The global lightbox (one per page), printed in the footer when any block on the page asked for it.
 *
 * @return void
 */
function lc_mdsx2026_print_lightbox() {
	if ( empty( $GLOBALS['lc_mdsx2026_lightbox'] ) ) {
		return;
	}
	?>
	<div class="lb" id="lb" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Image viewer', 'lc-mdsx2026' ); ?>" hidden data-lenis-prevent>
		<button class="lb__close" type="button" data-lb-close><?php esc_html_e( 'Close', 'lc-mdsx2026' ); ?></button>
		<button class="lb__nav lb__nav--prev" type="button" data-lb-prev aria-label="<?php esc_attr_e( 'Previous image', 'lc-mdsx2026' ); ?>"><i aria-hidden="true"></i></button>
		<img class="lb__img" src="" alt="" data-lb-img>
		<video class="lb__video" data-lb-video-el controls playsinline hidden></video>
		<button class="lb__nav lb__nav--next" type="button" data-lb-next aria-label="<?php esc_attr_e( 'Next image', 'lc-mdsx2026' ); ?>"><i aria-hidden="true"></i></button>
		<div class="lb__bar"><span data-lb-stage></span><em data-lb-cap></em><b data-lb-count></b></div>
	</div>
	<?php
}
add_action( 'wp_footer', 'lc_mdsx2026_print_lightbox' );

/**
 * Primary sector of a project, for the hero breadcrumb: [ name, url ] or empty.
 *
 * @param int $post_id Project ID.
 * @return array
 */
function lc_mdsx2026_primary_sector( $post_id ) {
	$terms = get_the_terms( $post_id, 'sector' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}
	return array( $terms[0]->name, $terms[0]->slug );
}
