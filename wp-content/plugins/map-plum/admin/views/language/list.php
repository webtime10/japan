<?php
/**
 * Languages list.
 *
 * @var array<int, object> $languages
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="map-plum-list-fullwidth">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Language List', 'map-plum' ); ?></h3>
		</div>
		<div class="panel-body map-plum-list-table-wrap">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<td class="text-left"><?php esc_html_e( 'Name', 'map-plum' ); ?></td>
						<td class="text-left"><?php esc_html_e( 'Code', 'map-plum' ); ?></td>
						<td class="text-left"><?php esc_html_e( 'Locale', 'map-plum' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Sort', 'map-plum' ); ?></td>
						<td class="text-center"><?php esc_html_e( 'Status', 'map-plum' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Action', 'map-plum' ); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $languages ) ) : ?>
						<tr><td colspan="6" class="text-center"><?php esc_html_e( 'No languages yet.', 'map-plum' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $languages as $lang ) : ?>
							<tr>
								<td><?php echo esc_html( $lang->name ); ?></td>
								<td><code><?php echo esc_html( $lang->code ); ?></code></td>
								<td><?php echo esc_html( $lang->locale ); ?></td>
								<td class="text-right"><?php echo (int) $lang->sort_order; ?></td>
								<td class="text-center">
									<?php echo (int) $lang->status ? esc_html__( 'Enabled', 'map-plum' ) : esc_html__( 'Disabled', 'map-plum' ); ?>
								</td>
								<td class="text-right">
									<a href="<?php echo esc_url( Map_Plum_Router::url( 'language', 'form', (int) $lang->language_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
									<?php if ( count( $languages ) > 1 ) : ?>
										<a href="<?php echo esc_url( wp_nonce_url( Map_Plum_Router::url( 'language', 'delete', (int) $lang->language_id ), 'map_plum_language_delete_' . (int) $lang->language_id ) ); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo esc_js( __( 'Delete this language?', 'map-plum' ) ); ?>');"><i class="fa fa-trash-o"></i></a>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
