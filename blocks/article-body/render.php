<?php
/**
 * Block template for Article Body.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$has_img = ! empty( $attributes['imageUrl'] ) || ! empty( $attributes['imageId'] );
?>
<article <?php echo lc_mdsx2026_wrapper( 'article-body section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( $has_img ) { ?>
			<figure class="article-body__image reveal">
				<?php lc_mdsx2026_image( (int) ( $attributes['imageId'] ?? 0 ), $attributes['imageUrl'] ?? '', $attributes['imageAlt'] ?? '', 'large', 'eager' ); ?>
				<?php if ( ! empty( $attributes['caption'] ) ) { ?>
					<figcaption><?php echo esc_html( $attributes['caption'] ); ?></figcaption>
				<?php } ?>
			</figure>
		<?php } ?>
		<?php if ( $attributes['showDate'] ?? true ) { ?>
			<p class="article-body__date t-meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		<?php } ?>
		<div class="article-body__text reveal"><?php echo wp_kses_post( $attributes['body'] ?? '' ); ?></div>
	</div>
</article>
