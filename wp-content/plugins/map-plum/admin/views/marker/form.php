<?php
/**
 * @var object|null $item
 * @var array $descriptions
 * @var array $languages
 * @var int $marker_id
 * @var array $manufacturer_options
 * @var array $category_options
 */
$save_url = Map_Plum_Router::url( 'marker', 'save' );
?>
<form id="map-plum-form-marker" action="<?php echo esc_url( $save_url ); ?>" method="post" enctype="multipart/form-data" class="form-horizontal map-plum-form">
	<?php wp_nonce_field( 'map_plum_marker_save' ); ?>
	<input type="hidden" name="marker_id" value="<?php echo esc_attr( (string) $marker_id ); ?>">

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
			<div class="form-group">
				<label class="col-sm-2 control-label">Категория</label>
				<div class="col-sm-10">
					<select name="category_id" class="form-control">
						<?php foreach ( $category_options as $cid => $label ) : ?>
							<option value="<?php echo esc_attr( (string) $cid ); ?>" <?php selected( $item && isset( $item->category_id ) ? (int) $item->category_id : 0, (int) $cid ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="help-block">Категория появится на карте только если она выбрана у маркера и маркер включён.</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Координаты</label>
				<div class="col-sm-10">
					<input type="text" name="coordinates" value="<?php echo esc_attr( $item ? $item->coordinates : '' ); ?>" class="form-control" placeholder="46.8182, 8.2275">
					<p class="help-block">Формат: широта, долгота</p>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Название и описание</h3></div>
		<div class="panel-body">
			<ul class="nav nav-tabs map-plum-lang-tabs" role="tablist">
				<?php foreach ( $languages as $i => $lang ) : ?>
					<li class="<?php echo 0 === $i ? 'active' : ''; ?>">
						<a href="#marker-lang-<?php echo esc_attr( (string) (int) $lang->language_id ); ?>" data-toggle="tab">
							<?php echo esc_html( $lang->name ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content map-plum-tab-content">
				<?php foreach ( $languages as $i => $lang ) :
					$lid = (int) $lang->language_id;
					$d   = isset( $descriptions[ $lid ] ) ? $descriptions[ $lid ] : null;
					?>
					<div class="tab-pane <?php echo 0 === $i ? 'active' : ''; ?>" id="marker-lang-<?php echo esc_attr( (string) $lid ); ?>">
						<div class="form-group">
							<label class="control-label">Название</label>
							<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][name]" value="<?php echo esc_attr( $d && isset( $d->name ) ? $d->name : '' ); ?>" class="form-control large-field">
						</div>
						<div class="form-group">
							<label class="control-label">Название на арабском</label>
							<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][arabic_name]" value="<?php echo esc_attr( $d && isset( $d->arabic_name ) ? $d->arabic_name : '' ); ?>" class="form-control large-field">
						</div>
						<div class="form-group">
							<label class="control-label">Описание</label>
							<textarea name="description[<?php echo esc_attr( (string) $lid ); ?>][description]" rows="5" class="form-control"><?php echo esc_textarea( $d && isset( $d->description ) ? $d->description : '' ); ?></textarea>
						</div>
					</div>
				<?php endforeach; ?>
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
		<div class="panel-heading"><h3 class="panel-title">Фотография</h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">Изображение</label>
				<div class="col-sm-10">
					<?php include MAP_PLUM_PATH . 'admin/views/partials/marker-image-upload.php'; ?>
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
					<label class="checkbox-inline">
						<input type="checkbox" name="status" value="1" <?php checked( ! $item || (int) $item->status === 1 ); ?>>
						Включено
					</label>
				</div>
			</div>
		</div>
	</div>

</form>
