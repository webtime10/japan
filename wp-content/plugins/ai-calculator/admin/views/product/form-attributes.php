<?php
/**
 * Product attributes panel — assignment checkboxes only.
 *
 * @var array $attribute_options attribute_id => label
 * @var array $selected_attribute_ids
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$selected_attribute_ids = isset( $selected_attribute_ids ) && is_array( $selected_attribute_ids )
	? array_map( 'intval', $selected_attribute_ids )
	: array();
$attribute_options = isset( $attribute_options ) && is_array( $attribute_options ) ? $attribute_options : array();
?>
<div class="panel panel-default ai-calculator-product-attributes">
	<div class="panel-heading">
		<h3 class="panel-title"><?php esc_html_e( 'Атрибуты', 'ai-calculator' ); ?></h3>
	</div>
	<div class="panel-body">
		<?php if ( empty( $attribute_options ) ) : ?>
			<p class="help-block">
				<?php
				printf(
					/* translators: 1: link to attribute groups, 2: link to attributes */
					esc_html__( 'Атрибуты не созданы. %1$s или %2$s.', 'ai-calculator' ),
					'<a href="' . esc_url( AI_Calculator_Router::url( 'attribute_group', 'index' ) ) . '">' . esc_html__( 'добавьте группы атрибутов', 'ai-calculator' ) . '</a>',
					'<a href="' . esc_url( AI_Calculator_Router::url( 'attribute', 'index' ) ) . '">' . esc_html__( 'добавьте атрибуты', 'ai-calculator' ) . '</a>'
				);
				?>
			</p>
		<?php else : ?>
			<p class="help-block">
				<?php esc_html_e( 'Отметьте, для каких параметров (например, возраста) подходит этот товар.', 'ai-calculator' ); ?>
			</p>
			<ul class="list-unstyled ai-calculator-attribute-checklist ai-calculator-attribute-checklist--simple">
				<?php foreach ( $attribute_options as $attr_id => $attr_label ) : ?>
					<?php $attr_id = (int) $attr_id; ?>
					<li class="ai-calculator-attribute-row ai-calculator-attribute-row--simple">
						<label class="checkbox-inline">
							<input
								type="checkbox"
								name="product_attribute_ids[]"
								value="<?php echo $attr_id; ?>"
								<?php checked( in_array( $attr_id, $selected_attribute_ids, true ) ); ?>
							>
							<?php echo esc_html( $attr_label ); ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
