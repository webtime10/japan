<?php
/**
 * @var array $items
 * @var array<int, int> $marker_counts
 */
?>
<div class="map-plum-list-fullwidth">
	<form id="map-plum-list-category" method="post" action="<?php echo esc_url( Map_Plum_Router::url( 'category', 'delete' ) ); ?>">
		<?php wp_nonce_field( 'map_plum_bulk_action' ); ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Category List', 'map-plum' ); ?></h3>
			</div>
			<div class="panel-body map-plum-list-table-wrap">
				<table class="table table-bordered table-hover map-plum-table">
					<thead>
						<tr>
							<td width="1"><input type="checkbox" onclick="jQuery('input[name*=\'selected\']').prop('checked', this.checked);"></td>
							<td><?php esc_html_e( 'Category Name', 'map-plum' ); ?></td>
							<td class="text-center">Маркеры на карте</td>
							<td class="text-right"><?php esc_html_e( 'Sort Order', 'map-plum' ); ?></td>
							<td class="text-center"><?php esc_html_e( 'Status', 'map-plum' ); ?></td>
							<td class="text-right"><?php esc_html_e( 'Action', 'map-plum' ); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $items ) ) : ?>
							<tr><td colspan="6" class="text-center"><?php esc_html_e( 'No results!', 'map-plum' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $items as $item ) :
								$cat_id       = (int) $item->category_id;
								$marker_count = isset( $marker_counts[ $cat_id ] ) ? (int) $marker_counts[ $cat_id ] : 0;
								?>
								<tr>
									<td><input type="checkbox" name="selected[]" value="<?php echo esc_attr( (string) $cat_id ); ?>"></td>
									<td><?php echo esc_html( ( $item->parent_id ? '— ' : '' ) . ( $item->name ? $item->name : '#' . $cat_id ) ); ?></td>
									<td class="text-center">
										<?php if ( $marker_count > 0 && (int) $item->status === 1 ) : ?>
											<span class="label label-success"><?php echo esc_html( (string) $marker_count ); ?></span>
										<?php else : ?>
											<span class="text-muted">—</span>
										<?php endif; ?>
									</td>
									<td class="text-right"><?php echo esc_html( (string) $item->sort_order ); ?></td>
									<td class="text-center"><?php echo (int) $item->status ? esc_html__( 'Enabled', 'map-plum' ) : esc_html__( 'Disabled', 'map-plum' ); ?></td>
									<td class="text-right">
										<a href="<?php echo esc_url( Map_Plum_Router::url( 'category', 'edit', (int) $item->category_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
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
