<?php
/**
 * @var array $items
 */
?>
<div class="container-fluid">
	<form id="map-plum-list-manufacturer" method="post" action="<?php echo esc_url( Map_Plum_Router::url( 'manufacturer', 'delete' ) ); ?>">
		<?php wp_nonce_field( 'map_plum_bulk_action' ); ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list"></i> Список регионов</h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover map-plum-table">
						<thead>
							<tr>
								<td width="1" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"></td>
								<td class="text-left"><?php esc_html_e( 'Name', 'map-plum' ); ?></td>
								<td class="text-right"><?php esc_html_e( 'Sort Order', 'map-plum' ); ?></td>
								<td class="text-center"><?php esc_html_e( 'Status', 'map-plum' ); ?></td>
								<td class="text-right"><?php esc_html_e( 'Action', 'map-plum' ); ?></td>
							</tr>
						</thead>
						<tbody>
							<?php if ( empty( $items ) ) : ?>
								<tr><td class="text-center" colspan="5"><?php esc_html_e( 'No results!', 'map-plum' ); ?></td></tr>
							<?php else : ?>
								<?php foreach ( $items as $item ) : ?>
									<tr>
										<td class="text-center"><input type="checkbox" name="selected[]" value="<?php echo esc_attr( (string) $item->manufacturer_id ); ?>"></td>
										<td class="text-left"><?php echo esc_html( $item->name ? $item->name : '—' ); ?></td>
										<td class="text-right"><?php echo esc_html( (string) $item->sort_order ); ?></td>
										<td class="text-center"><?php echo (int) $item->status ? esc_html__( 'Enabled', 'map-plum' ) : esc_html__( 'Disabled', 'map-plum' ); ?></td>
										<td class="text-right">
											<a href="<?php echo esc_url( Map_Plum_Router::url( 'manufacturer', 'edit', (int) $item->manufacturer_id ) ); ?>" class="btn btn-primary btn-sm" title="<?php esc_attr_e( 'Edit', 'map-plum' ); ?>"><i class="fa fa-pencil"></i></a>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</form>
</div>
