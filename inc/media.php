<?php
/**
 * Media: JPEG uploads are stored as WebP.
 *
 * WordPress can write its resized copies as WebP on its own (the `image_editor_output_format` filter),
 * but the file you upload stays a JPEG. Converting at upload time as well means the library holds a
 * single, smaller format. PNGs are left alone (transparency), and a JPEG that is already smaller
 * than its WebP is kept as is.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Quality for WebP output.
 *
 * @param int    $quality   Default quality.
 * @param string $mime_type Output MIME type.
 * @return int
 */
function lc_mdsx2026_webp_quality( $quality, $mime_type ) {
	return 'image/webp' === $mime_type ? 82 : $quality;
}
add_filter( 'wp_editor_set_quality', 'lc_mdsx2026_webp_quality', 10, 2 );

/**
 * Write WordPress's resized copies of a JPEG as WebP.
 *
 * @param string[] $formats Map of source MIME type => output MIME type.
 * @return string[]
 */
function lc_mdsx2026_webp_output_formats( $formats ) {
	$formats['image/jpeg'] = 'image/webp';

	return $formats;
}
add_filter( 'image_editor_output_format', 'lc_mdsx2026_webp_output_formats' );

/**
 * Convert an uploaded JPEG to WebP before WordPress creates the attachment.
 *
 * @param array $upload { file, url, type } of the stored upload.
 * @return array
 */
function lc_mdsx2026_convert_upload_to_webp( $upload ) {
	if ( empty( $upload['file'] ) || 'image/jpeg' !== ( $upload['type'] ?? '' ) || ! wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		return $upload;
	}

	$editor = wp_get_image_editor( $upload['file'] );
	if ( is_wp_error( $editor ) ) {
		return $upload;
	}

	$webp   = preg_replace( '/\.jpe?g$/i', '.webp', $upload['file'] );
	$unique = wp_unique_filename( dirname( $webp ), basename( $webp ) );
	$saved  = $editor->save( dirname( $webp ) . '/' . $unique, 'image/webp' );

	if ( is_wp_error( $saved ) || ! file_exists( $saved['path'] ) ) {
		return $upload;
	}
	if ( filesize( $saved['path'] ) >= filesize( $upload['file'] ) ) {
		wp_delete_file( $saved['path'] );
		return $upload;
	}

	wp_delete_file( $upload['file'] );

	return array(
		'file' => $saved['path'],
		'url'  => trailingslashit( dirname( $upload['url'] ) ) . basename( $saved['path'] ),
		'type' => 'image/webp',
	);
}
add_filter( 'wp_handle_upload', 'lc_mdsx2026_convert_upload_to_webp' );
add_filter( 'wp_handle_sideload', 'lc_mdsx2026_convert_upload_to_webp' );
