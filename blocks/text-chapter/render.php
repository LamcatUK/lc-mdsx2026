<?php
/**
 * Block template for Text Chapter.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
?>
<section <?php echo lc_mdsx2026_wrapper( 'text-chapter section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container text-chapter__grid">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<div class="text-chapter__body reveal" style="--i:1">
			<?php
			// The copy is inner blocks (paragraphs, headings, lists). Chapters saved before that carry it in `body`.
			echo '' !== trim( $content ) ? $content : wp_kses_post( $attributes['body'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks, or kses-filtered legacy HTML.
			?>
		</div>
	</div>
</section>
