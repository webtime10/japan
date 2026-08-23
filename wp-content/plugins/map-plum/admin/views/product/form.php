<?php
/**
 * @var object|null $item
 * @var array $descriptions
 * @var array $languages
 * @var int $product_id
 * @var array $manufacturer_options
 * @var array $category_list
 * @var array $product_categories
 * @var array $all_markers
 * @var array $selected_markers
 */
$save_url = Map_Plum_Router::url( 'product', 'save' );
?>
<form id="map-plum-form-product" action="<?php echo esc_url( $save_url ); ?>" method="post" enctype="multipart/form-data" class="form-horizontal map-plum-form">
	<?php wp_nonce_field( 'map_plum_product_save' ); ?>
	<input type="hidden" name="product_id" value="<?php echo esc_attr( (string) $product_id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Регион</h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">Регион</label>
				<div class="col-sm-10">
					<select name="manufacturer_id" class="form-control">
						<?php foreach ( $manufacturer_options as $mid => $label ) : ?>
							<option value="<?php echo esc_attr( (string) $mid ); ?>" <?php selected( $item ? (int) $item->manufacturer_id : 0, (int) $mid ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Категория</h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">Категория</label>
				<div class="col-sm-10 map-plum-category-checkboxes">
					<?php foreach ( $category_list as $cat ) : ?>
						<label class="checkbox-inline" style="display:block;margin-bottom:6px;">
							<input type="checkbox" name="product_category[]" value="<?php echo esc_attr( (string) $cat->category_id ); ?>" <?php checked( in_array( (int) $cat->category_id, $product_categories, true ) ); ?>>
							<?php echo esc_html( $cat->name ? $cat->name : '#' . $cat->category_id ); ?>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<?php
	$marker_panel_title = 'Маркер';
	include MAP_PLUM_PATH . 'admin/views/partials/marker-picker.php';
	?>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Название и название на арабском</h3></div>
		<div class="panel-body">
			<?php
			$panel_prefix      = 'prd-lang';
			$name_only         = false;
			$show_meta         = false;
			$show_meta_keyword = false;
			include MAP_PLUM_PATH . 'admin/views/partials/language-tabs.php';
			?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Фотография</h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">Изображение</label>
				<div class="col-sm-10">
					<?php include MAP_PLUM_PATH . 'admin/views/partials/product-image-upload.php'; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Ссылка</h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">Ссылка</label>
				<div class="col-sm-10">
					<input type="text" name="polylink" value="<?php echo esc_attr( $item && isset( $item->polylink ) ? $item->polylink : '' ); ?>" class="form-control" placeholder="https:// (необязательно)">
					<p class="help-block">Необязательное поле. Можно оставить пустым.</p>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Настройки</h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">Порядок сортировки</label>
				<div class="col-sm-10">
					<input type="number" name="sort_order" value="<?php echo esc_attr( $item ? (string) $item->sort_order : '0' ); ?>" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Статус</label>
				<div class="col-sm-10">
					<label><input type="checkbox" name="status" value="1" <?php checked( ! $item || (int) $item->status === 1 ); ?>> Включено</label>
				</div>
			</div>
		</div>
	</div>

</form>
