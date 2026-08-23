<?php
/**
 * @var object|null $item
 * @var array $descriptions
 * @var array $languages
 * @var int $manufacturer_id
 */
$save_url = Map_Plum_Router::url( 'manufacturer', 'save' );
?>
<form id="map-plum-form-manufacturer" action="<?php echo esc_url( $save_url ); ?>" method="post" class="form-horizontal map-plum-form">
	<?php wp_nonce_field( 'map_plum_manufacturer_save' ); ?>
	<input type="hidden" name="manufacturer_id" value="<?php echo esc_attr( (string) $manufacturer_id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-language"></i> Название</h3>
		</div>
		<div class="panel-body">
			<?php
			$panel_prefix = 'mfr-lang';
			$show_meta    = false;
			include MAP_PLUM_PATH . 'admin/views/partials/language-tabs.php';
			?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-cog"></i> Дополнительно</h3>
		</div>
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
