<?php
/**
 * Block template for Credits List.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$items   = array_filter( (array) ( $attributes['items'] ?? array() ), static fn( $r ) => ! lc_mdsx2026_row_empty( $r ) );
if ( ! $items ) {
	return;
}

// Optionally link each name to the matching Team member's profile (exact name match).
$bios = array();
if ( $attributes['linkPeople'] ?? true ) {
	foreach ( get_posts( array( 'post_type' => 'team', 'post_status' => 'publish', 'posts_per_page' => -1 ) ) as $member ) {
		$bios[ strtolower( trim( $member->post_title ) ) ] = get_permalink( $member );
	}
}
?>
<section <?php echo lc_mdsx2026_wrapper( 'credits-list section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="t-h3 reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<ul class="credits-list__list">
			<?php foreach ( $items as $item ) { ?>
				<?php
				$person = trim( $item['name'] ?? '' );
				$bio    = $bios[ strtolower( $person ) ] ?? '';
				?>
				<li>
					<b><?php echo esc_html( $item['role'] ?? '' ); ?></b>
					<?php if ( $bio ) { ?>
						<a href="<?php echo esc_url( $bio ); ?>"><?php echo esc_html( $person ); ?></a>
					<?php } else { ?>
						<?php echo esc_html( $person ); ?>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
