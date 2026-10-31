<?php
/**
 * Block template for Process Steps.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'solitude', 'solitude' );
$steps   = array_values( array_filter( (array) ( $attributes['steps'] ?? array() ), static fn( $r ) => ! lc_mdsx2026_row_empty( $r ) ) );
?>
<section <?php echo lc_mdsx2026_wrapper( 'process-steps section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<?php if ( ! empty( $attributes['intro'] ) ) { ?>
			<div class="t-subhead reveal"><?php echo wp_kses_post( $attributes['intro'] ); ?></div>
		<?php } ?>
		<ol class="process-steps__list">
			<?php foreach ( $steps as $i => $step ) { ?>
				<li class="reveal" style="--i:<?php echo (int) $i; ?>">
					<span class="process-steps__chevron" aria-hidden="true"></span>
					<h3><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
					<?php if ( ! empty( $step['text'] ) ) { ?>
						<p><?php echo esc_html( $step['text'] ); ?></p>
					<?php } ?>
				</li>
			<?php } ?>
		</ol>
	</div>
</section>
