<?php
/**
 * Categories list.
 *
 * @var array $categories
 * @var array $manufacturer_list
 * @var int   $manufacturer_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ai-calculator-list-fullwidth">
	<div class="ai-calculator-list-toolbar">
		<form method="get" class="ai-calculator-filter-form">
			<input type="hidden" name="page" value="ai_calculator_categories">
			<label for="filter_manufacturer"><?php esc_html_e( 'Калькулятор', 'ai-calculator' ); ?></label>
			<select name="filter_manufacturer" id="filter_manufacturer" class="form-control" onchange="this.form.submit()">
				<option value="0"><?php esc_html_e( '— Все —', 'ai-calculator' ); ?></option>
				<?php foreach ( $manufacturer_list as $mid => $label ) : ?>
					<?php if ( (int) $mid <= 0 ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<option value="<?php echo (int) $mid; ?>" <?php selected( $manufacturer_id, (int) $mid ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</form>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Category List', 'ai-calculator' ); ?></h3>
		</div>
		<div class="panel-body ai-calculator-list-table-wrap">
			<form method="post" action="<?php echo esc_url( AI_Calculator_Router::url( 'category', 'bulk_delete' ) ); ?>">
				<?php wp_nonce_field( 'ai_calculator_category_bulk_delete' ); ?>
				<input type="hidden" name="filter_manufacturer" value="<?php echo (int) $manufacturer_id; ?>">

				<div class="ai-calculator-bulk-actions">
					<button type="submit" class="btn btn-danger" onclick="return confirm('<?php echo esc_js( __( 'Delete selected categories?', 'ai-calculator' ) ); ?>');">
						<i class="fa fa-trash-o"></i> <?php esc_html_e( 'Удалить выбранные', 'ai-calculator' ); ?>
					</button>
				</div>

				<table class="table table-bordered table-hover ai-calculator-table">
					<thead>
						<tr>
							<td class="text-center" style="width: 42px;">
								<input type="checkbox" aria-label="<?php esc_attr_e( 'Select all categories', 'ai-calculator' ); ?>" onclick="var checked = this.checked; document.querySelectorAll('.ai-calculator-category-check').forEach(function (checkbox) { checkbox.checked = checked; });">
							</td>
							<td class="text-left"><?php esc_html_e( 'Name', 'ai-calculator' ); ?></td>
							<td class="text-left"><?php esc_html_e( 'Калькулятор', 'ai-calculator' ); ?></td>
							<td class="text-right"><?php esc_html_e( 'Sort', 'ai-calculator' ); ?></td>
							<td class="text-center"><?php esc_html_e( 'Status', 'ai-calculator' ); ?></td>
							<td class="text-right"><?php esc_html_e( 'Action', 'ai-calculator' ); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $categories ) ) : ?>
							<tr><td colspan="6" class="text-center"><?php esc_html_e( 'No categories yet.', 'ai-calculator' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $categories as $cat ) : ?>
								<tr>
									<td class="text-center">
										<input type="checkbox" class="ai-calculator-category-check" name="category_ids[]" value="<?php echo (int) $cat->category_id; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Select category %s', 'ai-calculator' ), wp_strip_all_tags( $cat->path_name ) ) ); ?>">
									</td>
									<td><?php echo wp_kses_post( $cat->path_name ); ?></td>
									<td>
										<?php
										if ( ! empty( $cat->manufacturer_name ) ) {
											echo esc_html( $cat->manufacturer_name );
										} elseif ( ! empty( $cat->manufacturer_id ) ) {
											echo esc_html( '#' . (int) $cat->manufacturer_id );
										} else {
											echo '<span class="text-muted">&mdash;</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
										?>
									</td>
									<td class="text-right"><?php echo (int) $cat->sort_order; ?></td>
									<td class="text-center">
										<?php echo (int) $cat->status ? esc_html__( 'Enabled', 'ai-calculator' ) : esc_html__( 'Disabled', 'ai-calculator' ); ?>
									</td>
									<td class="text-right">
										<a href="<?php echo esc_url( AI_Calculator_Router::url( 'category', 'form', (int) $cat->category_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
										<a href="<?php echo esc_url( wp_nonce_url( AI_Calculator_Router::url( 'category', 'delete', (int) $cat->category_id ), 'ai_calculator_category_delete_' . (int) $cat->category_id ) ); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo esc_js( __( 'Delete this category?', 'ai-calculator' ) ); ?>');"><i class="fa fa-trash-o"></i></a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
