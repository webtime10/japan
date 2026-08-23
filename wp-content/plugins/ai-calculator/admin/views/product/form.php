<?php
/**
 * Product form.
 *
 * @var object|null $product
 * @var array       $descriptions
 * @var int         $category_id
 * @var array       $category_list
 * @var array       $languages
 * @var array       $related_items
 * @var int         $admin_language_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id       = $product ? (int) $product->product_id : 0;
$form_id  = 'ai-calculator-form-product';
$save_url = AI_Calculator_Router::url( 'product', 'save' );
$mfr_id   = $product ? (int) $product->manufacturer_id : 0;
?>
<form id="<?php echo esc_attr( $form_id ); ?>" method="post" action="<?php echo esc_url( $save_url ); ?>" class="form-horizontal ai-calculator-form">
	<?php wp_nonce_field( 'ai_calculator_product_save' ); ?>
	<input type="hidden" name="product_id" value="<?php echo esc_attr( (string) $id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'General', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<?php
			$panel_prefix        = 'prod-lang';
			$show_meta           = false;
			$show_product_blocks = true;
			$description_label   = __( 'Название на русском', 'ai-calculator' );
			include AI_CALCULATOR_PATH . 'admin/views/partials/language-tabs.php';
			?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Data', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label" for="prod-manufacturer"><?php esc_html_e( 'Калькулятор', 'ai-calculator' ); ?></label>
				<select name="manufacturer_id" id="prod-manufacturer" class="form-control">
					<?php foreach ( $manufacturer_options as $mid => $label ) : ?>
						<option value="<?php echo (int) $mid; ?>" <?php selected( $mfr_id, (int) $mid ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group">
				<label class="control-label" for="prod-category"><?php esc_html_e( 'Категория', 'ai-calculator' ); ?></label>
				<select name="category_id" id="prod-category" class="form-control">
					<option value="0" data-manufacturer-id="0"><?php esc_html_e( '— None —', 'ai-calculator' ); ?></option>
					<?php foreach ( $category_list as $cat ) : ?>
						<option
							value="<?php echo (int) $cat->category_id; ?>"
							data-manufacturer-id="<?php echo (int) $cat->manufacturer_id; ?>"
							<?php selected( $category_id, (int) $cat->category_id ); ?>
						>
							<?php echo esc_html( wp_strip_all_tags( $cat->path_name ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p id="prod-category-empty" class="help-block" style="display:none;"><?php esc_html_e( 'Для выбранного калькулятора нет категорий.', 'ai-calculator' ); ?></p>
			</div>
			<div class="form-group">
				<label class="control-label" for="prod-sort"><?php esc_html_e( 'Sort order', 'ai-calculator' ); ?></label>
				<input type="number" class="form-control" id="prod-sort" name="sort_order" value="<?php echo $product ? (int) $product->sort_order : 0; ?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php esc_html_e( 'Status', 'ai-calculator' ); ?></label>
				<label><input type="checkbox" name="status" value="1" <?php checked( ! $product || (int) $product->status ); ?>> <?php esc_html_e( 'Enabled', 'ai-calculator' ); ?></label>
			</div>
		</div>
	</div>

	<?php include AI_CALCULATOR_PATH . 'admin/views/product/form-related.php'; ?>
