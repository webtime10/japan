<?php
/**
 * Dashboard home.
 *
 * @var array $groups
 * @var array $counts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-th-large"></i> <?php esc_html_e( 'Категории калькулятора', 'family-comfort-calc' ); ?></h3>
	</div>
	<div class="panel-body">
		<table class="table table-bordered table-hover ai-calculator-table">
			<thead>
				<tr>
					<td class="text-left"><?php esc_html_e( 'Группа', 'family-comfort-calc' ); ?></td>
					<td class="text-right"><?php esc_html_e( 'Записей', 'family-comfort-calc' ); ?></td>
					<td class="text-right"><?php esc_html_e( 'Действие', 'family-comfort-calc' ); ?></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $groups as $group => $label ) : ?>
					<tr>
						<td><?php echo esc_html( $label ); ?></td>
						<td class="text-right"><?php echo isset( $counts[ $group ] ) ? (int) $counts[ $group ] : 0; ?></td>
						<td class="text-right">
							<a href="<?php echo esc_url( FCC_Router::url( $group . '_category', 'index' ) ); ?>" class="btn btn-primary btn-sm">
								<i class="fa fa-list"></i> <?php esc_html_e( 'Список', 'family-comfort-calc' ); ?>
							</a>
							<a href="<?php echo esc_url( FCC_Router::url( $group . '_category', 'form' ) ); ?>" class="btn btn-primary btn-sm">
								<i class="fa fa-plus"></i> <?php esc_html_e( 'Добавить', 'family-comfort-calc' ); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
