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

$family_comfort_manufacturer_id = function_exists( 'ai_calculator_get_family_comfort_manufacturer_id' )
	? ai_calculator_get_family_comfort_manufacturer_id()
	: 0;
$is_family_comfort              = false;
if ( $mfr_id > 0 && ! empty( $manufacturer_options ) ) {
	foreach ( $manufacturer_options as $mid => $label ) {
		if ( (int) $mid !== $mfr_id ) {
			continue;
		}
		$is_family_comfort = (int) $mid === $family_comfort_manufacturer_id
			|| ( function_exists( 'ai_calculator_family_comfort_name_matches' ) && ai_calculator_family_comfort_name_matches( $label ) );
		break;
	}
}
if ( ! $is_family_comfort && $category_id > 0 && function_exists( 'ai_calculator_is_family_comfort_category' ) ) {
	$is_family_comfort = ai_calculator_is_family_comfort_category( $category_id );
}
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
			$name_only           = $is_family_comfort;
			$show_product_blocks = ! $is_family_comfort;
			$show_product_image  = ! $is_family_comfort;
			$product_images      = array(
				$product && isset( $product->image ) ? (string) $product->image : '',
				$product && isset( $product->image2 ) ? (string) $product->image2 : '',
				$product && isset( $product->image3 ) ? (string) $product->image3 : '',
				$product && isset( $product->image4 ) ? (string) $product->image4 : '',
				$product && isset( $product->image5 ) ? (string) $product->image5 : '',
				$product && isset( $product->image6 ) ? (string) $product->image6 : '',
			);
			$show_russian_name   = true;
			$description_label   = __( 'Описание', 'ai-calculator' );
			$name_label          = __( 'Название', 'ai-calculator' );
			$russian_name_label  = __( 'Название на русском', 'ai-calculator' );
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
						<?php
						$is_fc_option = (int) $mid === $family_comfort_manufacturer_id
							|| ( function_exists( 'ai_calculator_family_comfort_name_matches' ) && ai_calculator_family_comfort_name_matches( $label ) );
						?>
						<option
							value="<?php echo (int) $mid; ?>"
							data-family-comfort="<?php echo $is_fc_option ? '1' : '0'; ?>"
							<?php selected( $mfr_id, (int) $mid ); ?>
						>
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
						<?php
						if ( function_exists( 'ai_calculator_is_family_comfort_root_category' ) && ai_calculator_is_family_comfort_root_category( (int) $cat->category_id ) ) {
							continue;
						}
						$is_fc_category = (int) $cat->manufacturer_id === $family_comfort_manufacturer_id
							|| ( function_exists( 'ai_calculator_is_family_comfort_category' ) && ai_calculator_is_family_comfort_category( (int) $cat->category_id ) );
						$category_label = $is_fc_category && ! empty( $cat->name )
							? (string) $cat->name
							: wp_strip_all_tags( $cat->path_name );
						?>
						<option
							value="<?php echo (int) $cat->category_id; ?>"
							data-manufacturer-id="<?php echo (int) $cat->manufacturer_id; ?>"
							data-family-comfort="<?php echo $is_fc_category ? '1' : '0'; ?>"
							<?php selected( $category_id, (int) $cat->category_id ); ?>
						>
							<?php echo esc_html( $category_label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p id="prod-category-empty" class="help-block" style="display:none;"><?php esc_html_e( 'Для выбранного калькулятора нет категорий.', 'ai-calculator' ); ?></p>
				<p class="help-block"><?php esc_html_e( 'Категория — интерес на калькуляторе (например: Природа). Товар в этой категории — город или направление.', 'ai-calculator' ); ?></p>
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

	<?php include AI_CALCULATOR_PATH . 'admin/views/product/form-family-comfort.php'; ?>

	<?php include AI_CALCULATOR_PATH . 'admin/views/product/form-related.php'; ?>
	<?php include AI_CALCULATOR_PATH . 'admin/views/product/form-attributes.php'; ?>
</form>
