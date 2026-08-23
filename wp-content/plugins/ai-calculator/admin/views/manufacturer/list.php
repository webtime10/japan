<?php
/**
 * Manufacturers list.
 *
 * @var array $manufacturers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ai-calculator-list-fullwidth">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Список калькуляторов', 'ai-calculator' ); ?></h3>
		</div>
		<div class="panel-body ai-calculator-list-table-wrap">
			<table class="table table-bordered table-hover ai-calculator-table">
				<thead>
					<tr>
						<td class="text-left"><?php esc_html_e( 'Name', 'ai-calculator' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Sort', 'ai-calculator' ); ?></td>
						<td class="text-center"><?php esc_html_e( 'Status', 'ai-calculator' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Action', 'ai-calculator' ); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $manufacturers ) ) : ?>
						<tr><td colspan="4" class="text-center"><?php esc_html_e( 'Калькуляторов пока нет.', 'ai-calculator' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $manufacturers as $item ) : ?>
							<tr>
								<td><?php echo esc_html( $item->name ? $item->name : '#' . $item->manufacturer_id ); ?></td>
								<td class="text-right"><?php echo (int) $item->sort_order; ?></td>
								<td class="text-center">
									<?php echo (int) $item->status ? esc_html__( 'Enabled', 'ai-calculator' ) : esc_html__( 'Disabled', 'ai-calculator' ); ?>
								</td>
								<td class="text-right">
									<a href="<?php echo esc_url( AI_Calculator_Router::url( 'manufacturer', 'form', (int) $item->manufacturer_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
									<a href="<?php echo esc_url( wp_nonce_url( AI_Calculator_Router::url( 'manufacturer', 'delete', (int) $item->manufacturer_id ), 'ai_calculator_manufacturer_delete_' . (int) $item->manufacturer_id ) ); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo esc_js( __( 'Удалить этот калькулятор?', 'ai-calculator' ) ); ?>');"><i class="fa fa-trash-o"></i></a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
