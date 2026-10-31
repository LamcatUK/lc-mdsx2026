<?php
/**
 * Block template for FAQ.
 *
 * @package lc-mdsx2026
 */

defined( 'ABSPATH' ) || exit;

$surface = lc_mdsx2026_surface( $attributes['backgroundColor'] ?? 'nero' );
$items   = array_values( array_filter( (array) ( $attributes['items'] ?? array() ), static fn( $r ) => ! empty( $r['question'] ) ) );
if ( ! $items ) {
	return;
}
$group = 'faq-' . wp_unique_id();
$ld    = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array_map(
		static fn( $r ) => array(
			'@type'          => 'Question',
			'name'           => $r['question'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $r['answer'] ?? '' ),
			),
		),
		$items
	),
);
?>
<section <?php echo lc_mdsx2026_wrapper( 'faq section', $surface ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped attributes. ?>>
	<div class="container faq__grid">
		<?php if ( ! empty( $attributes['heading'] ) ) { ?>
			<h2 class="reveal"><?php echo esc_html( $attributes['heading'] ); ?></h2>
		<?php } ?>
		<div class="faq__items">
			<?php foreach ( $items as $item ) { ?>
				<details name="<?php echo esc_attr( $group ); ?>">
					<summary><?php echo esc_html( $item['question'] ); ?><i class="faq__icon" aria-hidden="true"></i></summary>
					<div class="faq__answer"><?php echo wp_kses_post( wpautop( $item['answer'] ?? '' ) ); ?></div>
				</details>
			<?php } ?>
		</div>
	</div>
	<script type="application/ld+json"><?php echo wp_json_encode( $ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD. ?></script>
</section>
