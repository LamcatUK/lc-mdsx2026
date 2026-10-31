<?php
/**
 * Block template for News List: the newest posts (News articles).
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$count   = max( 1, min( 24, (int) ( $attributes['count'] ?? 12 ) ) );
$news    = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $count, 'orderby' => 'date', 'order' => 'DESC' ) );
?>
<section <?php echo lc_mdsx2026_wrapper( 'news-list section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<ul class="news-list__list">
			<?php foreach ( $news as $i => $article ) { ?>
				<?php lc_mdsx2026_news_card( $article, $i ); ?>
			<?php } ?>
		</ul>
	</div>
</section>
