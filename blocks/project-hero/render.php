<?php
/**
 * Block template for Project Hero.
 *
 * Film mode: the best finished image, with a "Watch the film" button that plays the film in place
 * (src/js/project-film.js). Loop mode (media-led projects): a muted looping video with the still as poster.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$mode   = 'loop' === ( $attributes['mode'] ?? 'film' ) ? 'loop' : 'film';
$title  = ! empty( $attributes['title'] ) ? $attributes['title'] : get_the_title();
$lede   = $attributes['lede'] ?? '';
$vimeo  = preg_replace( '/\D/', '', $attributes['vimeoId'] ?? '' );
$image  = (int) ( $attributes['imageId'] ?? 0 );
$img_url = $attributes['imageUrl'] ?? '';
$video  = array(
	'webm'      => $attributes['webm'] ?? 0,
	'mp4'       => $attributes['mp4'] ?? 0,
	'poster'    => $attributes['poster'] ?? ( $image ),
	'posterUrl' => $attributes['posterUrl'] ?? $img_url,
	'captions'  => $attributes['captions'] ?? 0,
);
if ( empty( $video['poster'] ) && empty( $video['posterUrl'] ) ) {
	$video['poster'] = $image;
}
$has_files = ! empty( $attributes['webm'] ) || ! empty( $attributes['mp4'] );
$has_film  = 'film' === $mode && ( $has_files || $vimeo );

$crumb_text = $attributes['crumbText'] ?? '';
$crumb_url  = $attributes['crumbUrl'] ?? '';
if ( ! $crumb_text ) {
	$sector = lc_mdsx2026_primary_sector( get_the_ID() );
	if ( $sector ) {
		$crumb_text = sprintf( /* translators: %s: sector name. */ __( 'Work / %s', 'lc-mdsx2026' ), $sector[0] );
		$crumb_url  = lc_mdsx2026_page_url( 'portfolio' ) . '?sector=' . $sector[1];
	}
}
?>
<section <?php echo lc_mdsx2026_wrapper( 'project-hero', 'nero', $has_film ? 'has-film' : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?><?php echo $has_film ? ' data-film' . ( $vimeo ? ' data-vimeo="' . esc_attr( $vimeo ) . '"' : '' ) : ''; ?> aria-label="<?php echo esc_attr( $title ); ?>">
	<div class="project-hero__media" data-film-media>
		<?php if ( 'loop' === $mode && $has_files ) { ?>
			<?php lc_mdsx2026_video( $video, 'loop' ); ?>
		<?php } else { ?>
			<?php lc_mdsx2026_image( $image, $img_url, $attributes['imageAlt'] ?? '', 'portfolio-2400', 'eager', false, array( 'sizes' => '100vw', 'data-hero-img' => '' ) ); ?>
			<?php if ( $has_film && $has_files && ! $vimeo ) { ?>
				<?php lc_mdsx2026_video( $video, 'film' ); ?>
			<?php } ?>
		<?php } ?>
	</div>
	<div class="project-hero__shade"></div>
	<div class="project-hero__copy">
		<div class="container">
			<?php if ( $crumb_text ) { ?>
				<?php if ( $crumb_url ) { ?>
					<a class="project-hero__crumb" href="<?php echo esc_url( $crumb_url ); ?>"><?php echo esc_html( $crumb_text ); ?></a>
				<?php } else { ?>
					<span class="project-hero__crumb"><?php echo esc_html( $crumb_text ); ?></span>
				<?php } ?>
			<?php } ?>
			<h1 class="baseline" data-hero-title><?php echo esc_html( $title ); ?></h1>
			<div class="project-hero__row" data-hero-row>
				<?php if ( $lede ) { ?>
					<p class="t-lede project-hero__lede"><?php echo esc_html( $lede ); ?></p>
				<?php } ?>
				<?php if ( $has_film ) { ?>
					<button class="play" type="button" data-film-play>
						<span class="play__mark" aria-hidden="true"></span>
						<span><b><?php esc_html_e( 'Watch the film', 'lc-mdsx2026' ); ?></b><?php if ( ! empty( $attributes['filmDuration'] ) ) { ?><small><?php echo esc_html( $attributes['filmDuration'] ); ?></small><?php } ?></span>
					</button>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php if ( $has_film ) { ?>
		<button class="btn btn--dark btn--s project-hero__close" type="button" data-film-close><?php esc_html_e( 'Close film', 'lc-mdsx2026' ); ?></button>
	<?php } ?>
</section>
