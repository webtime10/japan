<?php
/**
 * @var object|null $item
 * @var array $descriptions
 * @var array $languages
 * @var int $category_id
 * @var array $parent_options
 */
$save_url = Map_Plum_Router::url( 'category', 'save' );
?>
<form id="map-plum-form-category" action="<?php echo esc_url( $save_url ); ?>" method="post" class="form-horizontal map-plum-form">
	<?php wp_nonce_field( 'map_plum_category_save' ); ?>
	<input type="hidden" name="category_id" value="<?php echo esc_attr( (string) $category_id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Название</h3></div>
		<div class="panel-body">
			<?php
			$panel_prefix = 'cat-lang';
			$show_meta    = false;
			include MAP_PLUM_PATH . 'admin/views/partials/language-tabs.php';
			?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Дополнительно</h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">Родительская категория</label>
				<div class="col-sm-10">
					<select name="parent_id" class="form-control">
						<?php foreach ( $parent_options as $pid => $label ) : ?>
							<option value="<?php echo esc_attr( (string) $pid ); ?>" <?php selected( $item ? (int) $item->parent_id : 0, (int) $pid ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
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
			<div class="form-group">
				<label class="col-sm-2 control-label">На карте</label>
				<div class="col-sm-10">
					<p class="help-block" style="margin-top: 7px;">
						Категория отображается на карте только если она включена и хотя бы у одного активного маркера выбрана эта категория.
					</p>
				</div>
			</div>
		</div>
	</div>

</form>
