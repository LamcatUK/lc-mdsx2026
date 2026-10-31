<?php
/**
 * Block template for People Strip. (Outlet names live in the Press Names block.)
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$people  = array_values( array_filter( (array) ( $attributes['people'] ?? array() ), static fn( $r ) => ! lc_mdsx2026_row_empty( $r ) ) );
?>
<section <?php echo lc_mdsx2026_wrapper( 'people-strip section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="baseline reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<div class="people-strip__list">
			<?php foreach ( $people as $i => $p ) { ?>
				<article class="people-strip__person reveal" style="--i:<?php echo (int) $i; ?>">
					<div class="people-strip__photo"><?php lc_mdsx2026_image( (int) ( $p['photo'] ?? 0 ), $p['photoUrl'] ?? '', '', 'medium' ); ?></div>
					<div class="people-strip__body">
						<h3><?php echo esc_html( $p['name'] ?? '' ); ?></h3>
						<?php if ( ! empty( $p['role'] ) ) { ?>
							<p class="t-label"><?php echo esc_html( $p['role'] ); ?></p>
						<?php } ?>
						<?php if ( ! empty( $p['bio'] ) ) { ?>
							<p class="people-strip__bio"><?php echo esc_html( $p['bio'] ); ?></p>
						<?php } ?>
						<?php
						// Each link names its person ("Read Dan's bio") so a list of them isn't a row of identical "Read profile" links.
						$link_text = $p['linkText'] ?? '';
						if ( '' === $link_text || 'Read profile' === $link_text ) {
							$first     = strtok( trim( $p['name'] ?? '' ), ' ' );
							$link_text = $first ? sprintf( /* translators: %s: first name */ __( 'Read %s’s bio', 'lc-mdsx2026' ), $first ) : __( 'Read bio', 'lc-mdsx2026' );
						}
						lc_mdsx2026_link( $link_text, $p['link'] ?? '', 'textlink' );
						?>
					</div>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
