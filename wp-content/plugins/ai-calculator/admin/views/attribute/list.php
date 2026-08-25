<?php
/**
 * Attributes list.
 *
 * @var array $attributes
 * @var array $group_list
 * @var int   $group_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ai-calculator-list-fullwidth">
	<div class="ai-calculator-list-toolbar">
		<form method="get" class="ai-calculator-filter-form">
			<input type="hidden" name="page" value="ai_calculator_attributes">
			<label for="filter_group"><?php esc_html_e( 'Группа', 'ai-calculator' ); ?></label>
			<select name="filter_group" id="filter_group" class="form-control" onchange="this.form.submit()">
				<option value="0"><?php esc_html_e( '— Все —', 'ai-calculator' ); ?></option>
				<?php foreach ( $group_list as $gid => $label ) : ?>
					<?php if ( (int) $gid <= 0 ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<option value="<?php echo (int) $gid; ?>" <?php selected( $group_id, (int) $gid ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</form>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Список атрибутов', 'ai-calculator' ); ?></h3>
		</div>
		<div class="panel-body ai-calculator-list-table-wrap">
			<table class="table table-bordered table-hover ai-calculator-table">
				<thead>
					<tr>
						<td class="text-left"><?php esc_html_e( 'Name', 'ai-calculator' ); ?></td>
						<td class="text-left"><?php esc_html_e( 'Группа', 'ai-calculator' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Sort', 'ai-calculator' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Action', 'ai-calculator' ); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $attributes ) ) : ?>
						<tr><td colspan="4" class="text-center"><?php esc_html_e( 'Атрибутов пока нет.', 'ai-calculator' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $attributes as $item ) : ?>
							<tr>
								<td><?php echo esc_html( $item->name ? $item->name : '#' . $item->attribute_id ); ?></td>
								<td><?php echo esc_html( $item->group_name ? $item->group_name : ( $item->attribute_group_id ? '#' . (int) $item->attribute_group_id : '—' ) ); ?></td>
								<td class="text-right"><?php echo (int) $item->sort_order; ?></td>
								<td class="text-right">
									<a href="<?php echo esc_url( AI_Calculator_Router::url( 'attribute', 'form', (int) $item->attribute_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
									<a href="<?php echo esc_url( wp_nonce_url( AI_Calculator_Router::url( 'attribute', 'delete', (int) $item->attribute_id ), 'ai_calculator_attribute_delete_' . (int) $item->attribute_id ) ); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo esc_js( __( 'Удалить этот атрибут?', 'ai-calculator' ) ); ?>');"><i class="fa fa-trash-o"></i></a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
