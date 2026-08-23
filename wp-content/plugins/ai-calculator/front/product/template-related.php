<?php
/**
 * Блок «Рекомендуемые товары».
 *
 * @var array<int, object> $related_products
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $related_products ) ) {
	return;
}
?>
<section class="ai-calc-product-related">
	<h2 class="ai-calc-product-related__title"><?php esc_html_e( 'Рекомендуемые товары', 'ai-calculator' ); ?></h2>
	<ul class="ai-calc-product-related__list">
		<?php foreach ( $related_products as $related ) : ?>
			<?php
			$related_id   = (int) $related->product_id;
			$related_name = ! empty( $related->name ) ? (string) $related->name : '#' . $related_id;
			$related_url  = add_query_arg( 'ai_product', $related_id, get_permalink() );
			?>
			<li class="ai-calc-product-related__item">
				<a class="ai-calc-product-related__link" href="<?php echo esc_url( $related_url ); ?>">
					<?php echo esc_html( $related_name ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
