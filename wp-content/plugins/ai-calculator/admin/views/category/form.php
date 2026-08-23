<?php
/**
 * Category form.
 *
 * @var object|null $category
 * @var array       $descriptions
 * @var array       $languages
 * @var array       $parent_categories
 * @var array       $manufacturer_options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id       = $category ? (int) $category->category_id : 0;
$form_id  = 'ai-calculator-form-category';
$save_url = AI_Calculator_Router::url( 'category', 'save' );
$mfr_id   = $category ? (int) $category->manufacturer_id : 0;
if ( ! $mfr_id && isset( $_GET['filter_manufacturer'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$mfr_id = (int) $_GET['filter_manufacturer'];
}
$parent_id = $category ? (int) $category->parent_id : 0;
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
					<?php foreach ( $manufacturer_options as $mid => $label ) : ?>
						<?php if ( (int) $mid <= 0 ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<option value="<?php echo (int) $mid; ?>" <?php selected( $mfr_id, (int) $mid ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group">
				<label class="control-label" for="cat-parent"><?php esc_html_e( 'Parent', 'ai-calculator' ); ?></label>
				<select name="parent_id" id="cat-parent" class="form-control">
					<option value="0" data-manufacturer-id="0"><?php esc_html_e( '— None —', 'ai-calculator' ); ?></option>
					<?php foreach ( $parent_categories as $parent_cat ) : ?>
						<?php if ( (int) $parent_cat->category_id === $id ) : ?>
							<?php continue; ?>
						<?php endif; ?>
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
