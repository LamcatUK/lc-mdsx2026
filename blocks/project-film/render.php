<?php
/**
 * Block template for Project Film: poster plus parallelogram play button; plays inline with sound.
 * Local files, or a Vimeo ID that is only loaded after the play click (no third party before interaction).
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$vimeo     = preg_replace( '/\D/', '', $attributes['vimeoId'] ?? '' );
$has_files = ! empty( $attributes['webm'] ) || ! empty( $attributes['mp4'] );
if ( ! $has_files && ! $vimeo ) {
	return;
}
$aspect = in_array( $attributes['aspect'] ?? '16-9', array( '16-9', 'wide', 'portrait' ), true ) ? $attributes['aspect'] : '16-9';
$poster = lc_mdsx2026_media_url( $attributes['poster'] ?? 0, $attributes['posterUrl'] ?? '' );
?>
<section <?php echo lc_mdsx2026_wrapper( 'project-film', 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<div class="project-film__head reveal">
			<?php if ( ! empty( $attributes['heading'] ) ) { ?>
				<h3><?php echo esc_html( $attributes['heading'] ); ?></h3>
			<?php } ?>
			<?php if ( ! empty( $attributes['duration'] ) ) { ?>
				<span class="t-meta t-muted"><?php echo esc_html( $attributes['duration'] ); ?></span>
			<?php } ?>
		</div>
		<div class="project-film__box project-film__box--<?php echo esc_attr( $aspect ); ?>" data-film<?php echo $vimeo ? ' data-vimeo="' . esc_attr( $vimeo ) . '"' : ''; ?>>
			<div class="project-film__media" data-film-media>
				<?php
				if ( $has_files && ! $vimeo ) {
					lc_mdsx2026_video(
						array(
							'webm'      => $attributes['webm'] ?? 0,
							'mp4'       => $attributes['mp4'] ?? 0,
							'poster'    => $attributes['poster'] ?? 0,
							'posterUrl' => $attributes['posterUrl'] ?? '',
							'captions'  => $attributes['captions'] ?? 0,
							'label'     => $attributes['heading'] ?? '',
						),
						'film'
					);
				} elseif ( $poster ) {
					printf( '<img src="%s" alt="" loading="lazy" decoding="async">', esc_url( $poster ) );
				}
				?>
			</div>
			<button class="play" type="button" data-film-play>
				<span class="play__mark" aria-hidden="true"></span>
				<span><b><?php echo esc_html( ! empty( $attributes['playLabel'] ) ? $attributes['playLabel'] : __( 'Play the film', 'lc-mdsx2026' ) ); ?></b><small><?php esc_html_e( 'With sound', 'lc-mdsx2026' ); ?></small></span>
			</button>
		</div>
	</div>
</section>
