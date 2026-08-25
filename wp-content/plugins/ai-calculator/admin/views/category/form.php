<?php
/**
 * Category form.
 *
 * @var object|null $category
 * @var array       $descriptions
 * @var array       $languages
 * @var array       $parent_categories
 * @var array       $manufacturer_options
 * @var int         $family_comfort_manufacturer_id
 * @var int         $family_comfort_root_category_id
 * @var bool        $is_family_comfort_root
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id       = $category ? (int) $category->category_id : 0;
$form_id  = 'ai-calculator-form-category';
$save_url = AI_Calculator_Router::url( 'category', 'save' );
$mfr_id   = $category ? (int) $category->manufacturer_id : 0;
$parent_id = $category ? (int) $category->parent_id : 0;
$family_comfort_manufacturer_id  = isset( $family_comfort_manufacturer_id ) ? (int) $family_comfort_manufacturer_id : 0;
$family_comfort_root_category_id = isset( $family_comfort_root_category_id ) ? (int) $family_comfort_root_category_id : 0;
$is_family_comfort_root          = ! empty( $is_family_comfort_root );
$is_family_comfort               = $mfr_id > 0 && $family_comfort_manufacturer_id > 0 && $mfr_id === $family_comfort_manufacturer_id;
if ( ! $is_family_comfort && $id > 0 && $family_comfort_root_category_id > 0 ) {
	$is_family_comfort = true;
}
if ( $is_family_comfort && ! $is_family_comfort_root && $family_comfort_root_category_id > 0 && $parent_id <= 0 ) {
	$parent_id = $family_comfort_root_category_id;
}
?>
<form id="<?php echo esc_attr( $form_id ); ?>" method="post" action="<?php echo esc_url( $save_url ); ?>" class="form-horizontal ai-calculator-form">
	<?php wp_nonce_field( 'ai_calculator_category_save' ); ?>
	<input type="hidden" name="category_id" value="<?php echo esc_attr( (string) $id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'General', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<?php
			$panel_prefix = 'cat-lang';
			$show_meta    = true;
			include AI_CALCULATOR_PATH . 'admin/views/partials/language-tabs.php';
			?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Data', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label" for="cat-manufacturer"><?php esc_html_e( 'Калькулятор', 'ai-calculator' ); ?></label>
				<select name="manufacturer_id" id="cat-manufacturer" class="form-control" required>
					<option value="" <?php selected( $mfr_id, 0 ); ?>><?php esc_html_e( '— Выберите калькулятор —', 'ai-calculator' ); ?></option>
					<?php foreach ( $manufacturer_options as $mid => $label ) : ?>
						<?php
						$is_fc_option = $family_comfort_manufacturer_id > 0 && (int) $mid === $family_comfort_manufacturer_id;
						if ( ! $is_fc_option && function_exists( 'ai_calculator_family_comfort_name_matches' ) ) {
							$is_fc_option = ai_calculator_family_comfort_name_matches( $label );
						}
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
			<div
				class="form-group"
				id="cat-parent-group"
				data-family-comfort-manufacturer-id="<?php echo (int) $family_comfort_manufacturer_id; ?>"
				data-family-comfort-root-category-id="<?php echo (int) $family_comfort_root_category_id; ?>"
				data-is-family-comfort-root="<?php echo $is_family_comfort_root ? '1' : '0'; ?>"
			>
				<label class="control-label" for="cat-parent"><?php esc_html_e( 'Parent', 'ai-calculator' ); ?></label>
				<select name="parent_id" id="cat-parent" class="form-control">
					<option value="0" data-manufacturer-id="0"><?php esc_html_e( '— None —', 'ai-calculator' ); ?></option>
					<?php foreach ( $parent_categories as $parent_cat ) : ?>
						<option
							value="<?php echo (int) $parent_cat->category_id; ?>"
							data-manufacturer-id="<?php echo (int) $parent_cat->manufacturer_id; ?>"
							<?php selected( $parent_id, (int) $parent_cat->category_id ); ?>
						><?php echo esc_html( wp_strip_all_tags( $parent_cat->path_name ) ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="help-block" id="cat-parent-empty" style="display:none;">
					<?php esc_html_e( 'Нет родительских категорий для этого калькулятора.', 'ai-calculator' ); ?>
				</p>
				<p class="help-block" id="cat-parent-family-comfort-root" <?php echo $is_family_comfort_root ? '' : 'style="display:none;"'; ?>>
					<?php esc_html_e( 'Это корневая категория калькулятора семейного комфорта. Родитель не задаётся.', 'ai-calculator' ); ?>
				</p>
				<p class="help-block" id="cat-parent-family-comfort-child" <?php echo ( $is_family_comfort && ! $is_family_comfort_root ) ? '' : 'style="display:none;"'; ?>>
					<?php esc_html_e( 'Для калькулятора семейного комфорта родителем может быть только корневая категория калькулятора.', 'ai-calculator' ); ?>
				</p>
			</div>
			<div class="form-group">
				<label class="control-label" for="cat-image"><?php esc_html_e( 'Image URL', 'ai-calculator' ); ?></label>
				<input type="text" class="form-control" id="cat-image" name="image" value="<?php echo $category ? esc_attr( $category->image ) : ''; ?>" placeholder="https://">
			</div>
			<div class="form-group">
				<label class="control-label" for="cat-sort"><?php esc_html_e( 'Sort order', 'ai-calculator' ); ?></label>
				<input type="number" class="form-control" id="cat-sort" name="sort_order" value="<?php echo $category ? (int) $category->sort_order : 0; ?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php esc_html_e( 'Status', 'ai-calculator' ); ?></label>
				<label><input type="checkbox" name="status" value="1" <?php checked( ! $category || (int) $category->status ); ?>> <?php esc_html_e( 'Enabled', 'ai-calculator' ); ?></label>
			</div>
		</div>
	</div>
</form>
