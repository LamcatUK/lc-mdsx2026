<?php
/**
 * Block template for Project Intro.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['surface'] ?? 'nero' );
$meta    = array_filter( (array) ( $attributes['meta'] ?? array() ), static fn( $r ) => ! lc_mdsx2026_row_empty( $r ) );
$tags    = lc_mdsx2026_lines( $attributes['tags'] ?? '' );
?>
<section <?php echo lc_mdsx2026_wrapper( 'project-intro section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container project-intro__grid">
		<div class="reveal">
			<?php if ( ! empty( $attributes['statement'] ) ) { ?>
				<p class="t-statement project-intro__statement"><?php echo esc_html( $attributes['statement'] ); ?></p>
			<?php } ?>
			<?php if ( $meta ) { ?>
				<dl class="project-intro__meta">
					<?php foreach ( $meta as $row ) { ?>
						<dt><?php echo esc_html( $row['label'] ?? '' ); ?></dt>
						<dd><?php echo esc_html( $row['value'] ?? '' ); ?></dd>
					<?php } ?>
				</dl>
			<?php } ?>
			<?php if ( $tags ) { ?>
				<ul class="project-intro__tags">
					<?php foreach ( $tags as $tag ) { ?>
						<li class="tag"><?php echo esc_html( $tag ); ?></li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>
		<?php if ( ! empty( $attributes['body'] ) ) { ?>
			<div class="project-intro__body t-lede reveal" style="--i:1"><?php echo wp_kses_post( $attributes['body'] ); ?></div>
		<?php } ?>
	</div>
</section>
