<?php
/**
 * Category form.
 *
 * @var object|null $category
 * @var string      $name
 * @var string      $description
 * @var string      $route
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id       = $category ? (int) $category->category_id : 0;
$form_id  = 'fcc-form-category';
$save_url = FCC_Router::url( $route, 'save' );
?>
<form id="<?php echo esc_attr( $form_id ); ?>" method="post" action="<?php echo esc_url( $save_url ); ?>" class="form-horizontal ai-calculator-form">
	<?php wp_nonce_field( 'fcc_category_save' ); ?>
	<input type="hidden" name="category_id" value="<?php echo esc_attr( (string) $id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Название и описание', 'family-comfort-calc' ); ?></h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label" for="fcc-cat-name"><?php esc_html_e( 'Название', 'family-comfort-calc' ); ?></label>
				<input type="text" class="form-control" id="fcc-cat-name" name="name" value="<?php echo esc_attr( $name ); ?>" required>
			</div>
			<div class="form-group">
				<label class="control-label" for="fcc-cat-desc"><?php esc_html_e( 'Описание', 'family-comfort-calc' ); ?></label>
				<textarea class="form-control" rows="4" id="fcc-cat-desc" name="description"><?php echo esc_textarea( $description ); ?></textarea>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Параметры', 'family-comfort-calc' ); ?></h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label" for="fcc-cat-sort"><?php esc_html_e( 'Сортировка', 'family-comfort-calc' ); ?></label>
				<input type="number" class="form-control" id="fcc-cat-sort" name="sort_order" value="<?php echo $category ? (int) $category->sort_order : 0; ?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php esc_html_e( 'Статус', 'family-comfort-calc' ); ?></label>
				<label><input type="checkbox" name="status" value="1" <?php checked( ! $category || (int) $category->status ); ?>> <?php esc_html_e( 'Включено', 'family-comfort-calc' ); ?></label>
			</div>
		</div>
	</div>
</form>
