<?php
/**
 * Attribute groups list.
 *
 * @var array $groups
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ai-calculator-list-fullwidth">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Список групп атрибутов', 'ai-calculator' ); ?></h3>
		</div>
		<div class="panel-body ai-calculator-list-table-wrap">
			<table class="table table-bordered table-hover ai-calculator-table">
				<thead>
					<tr>
						<td class="text-left"><?php esc_html_e( 'Name', 'ai-calculator' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Sort', 'ai-calculator' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Action', 'ai-calculator' ); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $groups ) ) : ?>
						<tr><td colspan="3" class="text-center"><?php esc_html_e( 'Групп атрибутов пока нет.', 'ai-calculator' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $groups as $item ) : ?>
							<tr>
								<td><?php echo esc_html( $item->name ? $item->name : '#' . $item->attribute_group_id ); ?></td>
								<td class="text-right"><?php echo (int) $item->sort_order; ?></td>
								<td class="text-right">
									<a href="<?php echo esc_url( AI_Calculator_Router::url( 'attribute_group', 'form', (int) $item->attribute_group_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
									<a href="<?php echo esc_url( wp_nonce_url( AI_Calculator_Router::url( 'attribute_group', 'delete', (int) $item->attribute_group_id ), 'ai_calculator_attribute_group_delete_' . (int) $item->attribute_group_id ) ); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo esc_js( __( 'Удалить эту группу?', 'ai-calculator' ) ); ?>');"><i class="fa fa-trash-o"></i></a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
