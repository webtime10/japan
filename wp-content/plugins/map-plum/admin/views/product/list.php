<?php
/**
 * @var array<int, object>     $items
 * @var array<int, string>     $manufacturer_options
 * @var array<int, string>     $category_options
 * @var int                    $filter_manufacturer_id
 * @var int                    $filter_category_id
 * @var bool                   $filters_active
 */
$list_url = Map_Plum_Router::url( 'product' );
?>
<div class="map-plum-list-toolbar map-plum-list-toolbar-wp">
	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" class="map-plum-filter-form">
		<input type="hidden" name="page" value="<?php echo esc_attr( Map_Plum_Router::page_slug_for_route( 'product' ) ); ?>">
		<label for="map-plum-product-filter-region" class="screen-reader-text">Регион</label>
		<select id="map-plum-product-filter-region" name="manufacturer_id">
			<?php foreach ( $manufacturer_options as $manufacturer_id => $label ) : ?>
				<option value="<?php echo esc_attr( (string) $manufacturer_id ); ?>" <?php selected( $filter_manufacturer_id, (int) $manufacturer_id ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<label for="map-plum-product-filter-category" class="screen-reader-text">Категория</label>
		<select id="map-plum-product-filter-category" name="category_id">
			<?php foreach ( $category_options as $category_id => $label ) : ?>
				<option value="<?php echo esc_attr( (string) $category_id ); ?>" <?php selected( $filter_category_id, (int) $category_id ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<button type="submit" name="filter_action" value="1" class="button">Фильтр</button>
		<?php if ( $filters_active && ( $filter_manufacturer_id > 0 || $filter_category_id > 0 ) ) : ?>
			<a href="<?php echo esc_url( $list_url ); ?>" class="button map-plum-filter-reset">Сбросить</a>
		<?php endif; ?>
	</form>
</div>

<div class="container-fluid">
	<form id="map-plum-list-product" method="post" action="<?php echo esc_url( Map_Plum_Router::url( 'product', 'delete' ) ); ?>">
		<?php wp_nonce_field( 'map_plum_bulk_action' ); ?>
		<?php if ( $filters_active ) : ?>
			<input type="hidden" name="return_manufacturer_id" value="<?php echo esc_attr( (string) $filter_manufacturer_id ); ?>">
			<input type="hidden" name="return_category_id" value="<?php echo esc_attr( (string) $filter_category_id ); ?>">
		<?php endif; ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list"></i> Список округов</h3>
			</div>
			<div class="panel-body">
				<table class="table table-bordered table-hover map-plum-table">
					<thead>
						<tr>
							<td width="1"><input type="checkbox" onclick="jQuery('input[name*=\'selected\']').prop('checked', this.checked);"></td>
							<td>Название округа</td>
							<td>Категории</td>
							<td>Регион</td>
							<td class="text-center"><?php esc_html_e( 'Status', 'map-plum' ); ?></td>
							<td class="text-right"><?php esc_html_e( 'Action', 'map-plum' ); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $items ) ) : ?>
							<tr><td colspan="6" class="text-center"><?php esc_html_e( 'No results!', 'map-plum' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $items as $item ) : ?>
								<tr>
									<td><input type="checkbox" name="selected[]" value="<?php echo esc_attr( (string) $item->product_id ); ?>"></td>
									<td><?php echo esc_html( $item->name ? $item->name : '—' ); ?></td>
									<td><?php echo esc_html( ! empty( $item->category_names ) ? $item->category_names : '—' ); ?></td>
									<td>
										<?php
										if ( ! empty( $item->manufacturer_name ) ) {
											echo esc_html( $item->manufacturer_name );
										} elseif ( ! empty( $item->manufacturer_id ) ) {
											echo esc_html( 'Регион #' . (int) $item->manufacturer_id );
										} else {
											echo '—';
										}
										?>
									</td>
									<td class="text-center"><?php echo (int) $item->status ? esc_html__( 'Enabled', 'map-plum' ) : esc_html__( 'Disabled', 'map-plum' ); ?></td>
									<td class="text-right">
										<a href="<?php echo esc_url( Map_Plum_Router::url( 'product', 'edit', (int) $item->product_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>
