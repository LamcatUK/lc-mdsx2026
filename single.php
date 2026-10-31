<?php
/**
 * Single post template (News articles).
 *
 * Header: breadcrumb, title, standfirst (the excerpt) and a meta line (date, author when set, reading time). Then the featured image as a
 * lead image (only if it is landscape; square and portrait images stay as the card image),
 * then the post content exactly as written in the editor, so core blocks such as Quote,
 * Image, Heading and List work normally. Closes with a call to action.
 *
 * @package lc-mdsx2026
 */

get_header();

while ( have_posts() ) {
	the_post();

	$posts_page = (int) get_option( 'page_for_posts' );
	$thumb_id   = get_post_thumbnail_id();
	$thumb      = $thumb_id ? wp_get_attachment_image_src( $thumb_id, 'large' ) : null;
	$landscape  = $thumb && $thumb[2] && ( $thumb[1] / $thumb[2] ) >= 1.3;
	$caption    = $thumb_id ? wp_get_attachment_caption( $thumb_id ) : '';
	$minutes    = estimate_reading_time_in_minutes( get_the_content() );
	?>
	<article <?php post_class( 'news-article' ); ?>>
		<header class="page-hero page-hero--title s-nero">
			<div class="page-hero__inner container">
				<div class="page-hero__copy">
					<?php lc_mdsx2026_breadcrumb( $posts_page ? get_the_title( $posts_page ) : __( 'News', 'lc-mdsx2026' ), $posts_page ? wp_make_link_relative( get_permalink( $posts_page ) ) : '' ); ?>
					<h1 class="baseline reveal"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) { ?>
						<p class="page-hero__intro t-lede reveal" style="--i:1"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php } ?>
					<p class="news-article__meta t-meta reveal" style="--i:2">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<?php if ( get_the_author() ) { ?>
							<span aria-hidden="true">&middot;</span>
							<span><?php /* translators: %s: author name. */ printf( esc_html__( 'By %s', 'lc-mdsx2026' ), esc_html( get_the_author() ) ); ?></span>
						<?php } ?>
						<?php if ( $minutes ) { ?>
							<span aria-hidden="true">&middot;</span>
							<span><?php /* translators: %d: minutes. */ printf( esc_html__( '%d min read', 'lc-mdsx2026' ), (int) $minutes ); ?></span>
						<?php } ?>
					</p>
				</div>
			</div>
		</header>

		<div class="news-article__main section s-nero">
			<div class="container">
				<?php if ( $landscape ) { ?>
					<figure class="news-article__lead">
						<?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
						<?php if ( $caption ) { ?>
							<figcaption><?php echo esc_html( $caption ); ?></figcaption>
						<?php } ?>
					</figure>
				<?php } ?>
				<div class="news-article__content">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
	</article>
	<?php
	echo do_blocks( '<!-- wp:lc-mdsx2026/cta-band {"heading":"Planning a museum or attraction project?","ctaText":"Start a project","ctaUrl":"/contact-us/"} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block render.
}

get_footer();
