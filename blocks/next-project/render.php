<?php
/**
 * Block template for Next Project.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$id = (int) ( $attributes['project'] ?? 0 );
if ( $id && lc_mdsx2026_is_unlisted( $id ) ) {
	$id = 0;
}
if ( ! $id ) {
	// The next newer listed project, or the next older one at the end of the line.
	$listed = get_posts(
		lc_mdsx2026_listing_args(
			array(
				'post_type'      => 'portfolio',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		)
	);
	$here   = array_search( get_the_ID(), $listed, true );
	if ( false === $here ) {
		$id = $listed ? $listed[0] : 0;
	} else {
		$id = $listed[ $here - 1 ] ?? ( $listed[ $here + 1 ] ?? 0 );
	}
}
if ( ! $id ) {
	return;
}
?>
<section <?php echo lc_mdsx2026_wrapper( 'next-project', 'nero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<a class="next-project__link" href="<?php echo esc_url( get_permalink( $id ) ); ?>">
		<?php echo get_the_post_thumbnail( $id, 'full', array( 'loading' => 'lazy', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped markup. ?>
		<span class="container next-project__copy">
			<small><?php esc_html_e( 'Next project', 'lc-mdsx2026' ); ?></small>
			<strong class="t-h2"><?php echo esc_html( get_the_title( $id ) ); ?><i class="arrow" aria-hidden="true"></i></strong>
		</span>
	</a>
</section>
