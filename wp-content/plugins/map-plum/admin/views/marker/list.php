<?php
/**
 * @var array<int, object>     $items
 * @var array<int, string>     $manufacturer_options
 * @var int                    $filter_manufacturer_id
 * @var int                    $paged
 * @var int                    $per_page
 * @var int                    $total
 * @var int                    $total_pages
 */
$list_url = Map_Plum_Router::url( 'marker' );

/**
 * @param int $page
 * @return string
 */
$marker_list_url = static function ( $page ) use ( $list_url, $filter_manufacturer_id ) {
	$args = array();
	if ( $filter_manufacturer_id > 0 ) {
		$args['manufacturer_id'] = $filter_manufacturer_id;
	}
	if ( $page > 1 ) {
		$args['paged'] = $page;
	}
	if ( empty( $args ) ) {
		return $list_url;
	}
	return add_query_arg( $args, $list_url );
};

$from = 0;
$to   = 0;
if ( $total > 0 ) {
	$from = ( ( $paged - 1 ) * $per_page ) + 1;
	$to   = min( $paged * $per_page, $total );
}
?>
<div class="map-plum-list-toolbar">
	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" class="map-plum-filter-form">
		<input type="hidden" name="page" value="<?php echo esc_attr( Map_Plum_Router::page_slug_for_route( 'marker' ) ); ?>">
		<label for="map-plum-marker-filter-region">Регион</label>
		<select id="map-plum-marker-filter-region" name="manufacturer_id" onchange="this.form.submit()">
			<?php foreach ( $manufacturer_options as $manufacturer_id => $label ) : ?>
				<option value="<?php echo esc_attr( (string) $manufacturer_id ); ?>" <?php selected( $filter_manufacturer_id, (int) $manufacturer_id ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</form>
	<?php if ( $total > 0 ) : ?>
		<p class="map-plum-list-summary">
			Показано <?php echo esc_html( (string) $from ); ?>–<?php echo esc_html( (string) $to ); ?> из <?php echo esc_html( (string) $total ); ?>
		</p>
	<?php endif; ?>
</div>

<form id="map-plum-list-marker" method="post" action="<?php echo esc_url( Map_Plum_Router::url( 'marker', 'delete' ) ); ?>">
	<?php wp_nonce_field( 'map_plum_bulk_action' ); ?>
	<?php if ( $filter_manufacturer_id > 0 ) : ?>
		<input type="hidden" name="return_manufacturer_id" value="<?php echo esc_attr( (string) $filter_manufacturer_id ); ?>">
	<?php endif; ?>
	<?php if ( $paged > 1 ) : ?>
		<input type="hidden" name="return_paged" value="<?php echo esc_attr( (string) $paged ); ?>">
	<?php endif; ?>
	<div class="panel panel-default">
		<div class="table-responsive">
			<table class="table table-hover map-plum-table">
				<thead>
					<tr>
						<td width="1"><input type="checkbox" onclick="jQuery('input[name*=\'selected\']').prop('checked', this.checked);"></td>
						<td>Название</td>
						<td>Регион</td>
						<td>Категория</td>
						<td>Координаты</td>
						<td class="text-right">Порядок</td>
						<td class="text-center">Статус</td>
						<td class="text-right">Действие</td>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $items ) ) : ?>
						<tr>
							<td class="text-center" colspan="8">Нет маркеров</td>
						</tr>
					<?php else : ?>
						<?php foreach ( $items as $row ) : ?>
							<tr>
								<td><input type="checkbox" name="selected[]" value="<?php echo esc_attr( (string) $row->marker_id ); ?>"></td>
								<td><?php echo esc_html( $row->name ? $row->name : '—' ); ?></td>
								<td><?php echo esc_html( ! empty( $row->manufacturer_name ) ? $row->manufacturer_name : '—' ); ?></td>
								<td><?php echo esc_html( ! empty( $row->category_name ) ? $row->category_name : '—' ); ?></td>
								<td><?php echo esc_html( $row->coordinates ); ?></td>
								<td class="text-right"><?php echo esc_html( (string) $row->sort_order ); ?></td>
								<td class="text-center"><?php echo (int) $row->status === 1 ? 'Вкл' : 'Выкл'; ?></td>
								<td class="text-right">
									<a href="<?php echo esc_url( Map_Plum_Router::url( 'marker', 'edit', (int) $row->marker_id ) ); ?>" class="btn btn-default btn-sm">Изменить</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</form>

<?php if ( $total_pages > 1 ) : ?>
	<nav class="map-plum-pagination" aria-label="Навигация по страницам">
		<ul>
			<?php if ( $paged > 1 ) : ?>
				<li>
					<a href="<?php echo esc_url( $marker_list_url( $paged - 1 ) ); ?>" class="map-plum-page-link">&laquo; Назад</a>
				</li>
			<?php endif; ?>
			<?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
				<li>
					<?php if ( $i === $paged ) : ?>
						<span class="map-plum-page-current"><?php echo esc_html( (string) $i ); ?></span>
					<?php else : ?>
						<a href="<?php echo esc_url( $marker_list_url( $i ) ); ?>" class="map-plum-page-link"><?php echo esc_html( (string) $i ); ?></a>
					<?php endif; ?>
				</li>
			<?php endfor; ?>
			<?php if ( $paged < $total_pages ) : ?>
				<li>
					<a href="<?php echo esc_url( $marker_list_url( $paged + 1 ) ); ?>" class="map-plum-page-link">Вперёд &raquo;</a>
				</li>
			<?php endif; ?>
		</ul>
	</nav>
<?php endif; ?>
