<?php
/**
 * Posts page template (Settings > Reading > Posts page = News).
 *
 * Title: the page's title. Intro: whatever is in the page's editor, shown under
 * the title where a page hero's intro would sit. Below that, the article list
 * (the main query, paginated) and a closing call to action.
 *
 * @package lc-mdsx2026
 */

get_header();

$posts_page = get_post( (int) get_option( 'page_for_posts' ) );
$title      = $posts_page ? get_the_title( $posts_page ) : __( 'News', 'lc-mdsx2026' );
$intro      = $posts_page ? trim( apply_filters( 'the_content', $posts_page->post_content ) ) : '';
?>
<section class="page-hero page-hero--title s-nero">
	<div class="page-hero__inner container">
		<div class="page-hero__copy">
			<h1 class="baseline reveal"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $intro ) { ?>
				<div class="page-hero__intro t-lede reveal" style="--i:1"><?php echo $intro; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core content filter. ?></div>
			<?php } ?>
		</div>
	</div>
</section>

<section class="news-list section s-nero">
	<div class="container">
		<?php if ( have_posts() ) { ?>
			<ul class="news-list__list">
				<?php
				$i = 0;
				while ( have_posts() ) {
					the_post();
					lc_mdsx2026_news_card( get_post(), $i++ );
				}
				?>
			</ul>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Newer', 'lc-mdsx2026' ),
					'next_text' => __( 'Older', 'lc-mdsx2026' ),
				)
			);
			?>
		<?php } else { ?>
			<p><?php esc_html_e( 'No news yet.', 'lc-mdsx2026' ); ?></p>
		<?php } ?>
	</div>
</section>

<?php
echo do_blocks( '<!-- wp:lc-mdsx2026/cta-band {"heading":"Planning a museum or attraction project?","ctaText":"Start a project","ctaUrl":"/contact-us/"} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block render.

get_footer();
