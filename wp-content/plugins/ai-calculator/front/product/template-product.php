<?php
/**
 * Шаблон страницы товара.
 *
 * @var object      $product
 * @var string      $product_name
 * @var string      $product_description
 * @var array       $product_blocks
 * @var array       $related_products
 * @var int         $product_id
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product_id = isset( $product ) ? (int) $product->product_id : 0;
?>
<article class="ai-calc-product" data-ai-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
	<header class="ai-calc-product__header">
		<h1 class="ai-calc-product__title"><?php echo esc_html( $product_name ); ?></h1>
	</header>

	<?php if ( '' !== trim( $product_description ) ) : ?>
		<div class="ai-calc-product__description">
			<?php echo wp_kses_post( wpautop( $product_description ) ); ?>
		</div>
	<?php endif; ?>

	<?php
	$block_labels = array(
		'block1' => __( 'Блок1', 'ai-calculator' ),
		'block2' => __( 'Блок2', 'ai-calculator' ),
		'block3' => __( 'Блок3', 'ai-calculator' ),
		'block4' => __( 'Блок4', 'ai-calculator' ),
		'block5' => __( 'Блок5', 'ai-calculator' ),
		'block6' => __( 'Блок6', 'ai-calculator' ),
	);
	foreach ( $block_labels as $block_key => $block_label ) :
		$block_value = isset( $product_blocks[ $block_key ] ) ? trim( (string) $product_blocks[ $block_key ] ) : '';
		if ( '' === $block_value ) {
			continue;
		}
		?>
		<div class="ai-calc-product__block ai-calc-product__block--<?php echo esc_attr( $block_key ); ?>">
			<h2 class="ai-calc-product__block-title"><?php echo esc_html( $block_label ); ?></h2>
			<p class="ai-calc-product__block-text"><?php echo esc_html( $block_value ); ?></p>
		</div>
	<?php endforeach; ?>

	<?php
	if ( ! empty( $related_products ) ) {
		include AI_CALCULATOR_PATH . 'front/product/template-related.php';
	}
	?>
</article>
