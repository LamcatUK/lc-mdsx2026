<?php
/**
 * Block template for Journey Nav: numbered stage markers built from the stage blocks on the page.
 * Active / completed state and the progress bar are set by src/js/journey-nav.js.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$stages = lc_mdsx2026_page_stages();
if ( ! $stages ) {
	return;
}
?>
<nav <?php echo get_block_wrapper_attributes( array( 'class' => 'journey-nav' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?> aria-label="<?php esc_attr_e( 'Project journey', 'lc-mdsx2026' ); ?>" data-journey-nav>
	<div class="container">
		<ol class="journey-nav__list">
			<?php foreach ( $stages as $i => $stage ) { ?>
				<li>
					<?php if ( $stage['upcoming'] ) { ?>
						<a class="journey-nav__link is-upcoming" href="#<?php echo esc_attr( $stage['id'] ); ?>" data-journey-link>
							<span class="journey-nav__n" aria-hidden="true"><?php echo (int) ( $i + 1 ); ?></span>
							<span><?php echo esc_html( $stage['label'] ); ?><?php if ( $stage['date'] ) { ?><small><?php echo esc_html( $stage['date'] ); ?></small><?php } ?></span>
						</a>
					<?php } else { ?>
						<a class="journey-nav__link" href="#<?php echo esc_attr( $stage['id'] ); ?>" data-journey-link>
							<span class="journey-nav__n" aria-hidden="true"><?php echo (int) ( $i + 1 ); ?></span><?php echo esc_html( $stage['label'] ); ?>
						</a>
					<?php } ?>
				</li>
			<?php } ?>
		</ol>
	</div>
	<div class="journey-nav__bar" aria-hidden="true"><i data-journey-bar></i></div>
</nav>
