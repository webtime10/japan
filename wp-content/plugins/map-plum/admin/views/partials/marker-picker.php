<?php
/**
 * Выбор маркеров для товара.
 *
 * @var array  $all_markers
 * @var array  $selected_markers
 * @var string $marker_panel_title Заголовок панели (по умолчанию «Маркер»).
 */
$marker_panel_title = isset( $marker_panel_title ) ? $marker_panel_title : 'Маркер';

/**
 * @param object $marker
 * @return string
 */
$map_plum_marker_label = static function ( $marker ) {
	$name = ! empty( $marker->name ) ? (string) $marker->name : 'Маркер #' . (int) $marker->marker_id;
	if ( ! empty( $marker->manufacturer_name ) ) {
		return $name . ' (' . $marker->manufacturer_name . ')';
	}
	return $name;
};
?>
<div class="panel panel-default map-plum-marker-panel">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo esc_html( $marker_panel_title ); ?></h3>
	</div>
	<div class="panel-body">
		<div class="form-group map-plum-marker-picker">
			<label class="control-label">Выберите маркер</label>
			<div class="map-plum-marker-picker-row">
				<select id="map-plum-marker-select" class="form-control">
					<option value="">— выберите маркер —</option>
					<?php foreach ( $all_markers as $m ) : ?>
						<?php
						$marker_name   = $m->name ? $m->name : 'Маркер #' . $m->marker_id;
						$marker_region = ! empty( $m->manufacturer_name ) ? (string) $m->manufacturer_name : '';
						?>
						<option
							value="<?php echo esc_attr( (string) $m->marker_id ); ?>"
							data-name="<?php echo esc_attr( $marker_name ); ?>"
							data-region="<?php echo esc_attr( $marker_region ); ?>"
							data-coordinates="<?php echo esc_attr( $m->coordinates ); ?>"
						>
							<?php echo esc_html( $map_plum_marker_label( $m ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<button type="button" class="button" id="map-plum-marker-add">Добавить</button>
			</div>
			<p class="help-block">Выберите маркер в списке и нажмите «Добавить». Можно добавить несколько маркеров.</p>
		</div>

		<div class="map-plum-marker-selected-wrap">
			<label class="control-label">Выбранные маркеры</label>
			<ul id="map-plum-marker-selected" class="map-plum-marker-selected">
				<?php foreach ( $selected_markers as $m ) : ?>
					<li class="map-plum-marker-chip" data-id="<?php echo esc_attr( (string) $m->marker_id ); ?>">
						<span class="map-plum-marker-chip-label">
							<strong><?php echo esc_html( $map_plum_marker_label( $m ) ); ?></strong>
						</span>
						<button type="button" class="map-plum-marker-remove" title="Убрать">&times;</button>
						<input type="hidden" name="product_marker[]" value="<?php echo esc_attr( (string) $m->marker_id ); ?>">
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="map-plum-marker-empty <?php echo empty( $selected_markers ) ? '' : 'hidden'; ?>">Маркеры не выбраны</p>
		</div>
	</div>
</div>
