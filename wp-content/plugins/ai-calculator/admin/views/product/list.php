<?php
/**
 * Products list.
 *
 * @var array $products
 * @var array $category_list
 * @var int   $category_id
 * @var int   $admin_language_id
 * @var int   $total
 * @var int   $page
 * @var int   $pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$admin_language_id = isset( $admin_language_id ) ? (int) $admin_language_id : 0;
?>
<div class="ai-calculator-list-fullwidth">
	<div class="ai-calculator-list-toolbar">
		<form method="get" class="ai-calculator-filter-form">
			<input type="hidden" name="page" value="ai_calculator_products">
			<label for="filter_category"><?php esc_html_e( 'Category', 'ai-calculator' ); ?></label>
			<select name="filter_category" id="filter_category" class="form-control">
				<option value="0"><?php esc_html_e( '— All —', 'ai-calculator' ); ?></option>
				<?php foreach ( $category_list as $cat ) : ?>
					<option value="<?php echo (int) $cat->category_id; ?>" <?php selected( $category_id, (int) $cat->category_id ); ?>>
						<?php echo esc_html( wp_strip_all_tags( $cat->path_name ) ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<label for="filter_manufacturer"><?php esc_html_e( 'Калькулятор', 'ai-calculator' ); ?></label>
			<select name="filter_manufacturer" id="filter_manufacturer" class="form-control">
				<?php foreach ( $manufacturer_list as $mid => $label ) : ?>
					<option value="<?php echo (int) $mid; ?>" <?php selected( $manufacturer_id, (int) $mid ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Filter', 'ai-calculator' ); ?></button>
		</form>
		<p class="ai-calculator-list-summary">
			<?php
			printf(
				/* translators: %d: total items */
				esc_html__( 'Total: %d', 'ai-calculator' ),
				(int) $total
			);
			?>
		</p>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Product List', 'ai-calculator' ); ?></h3>
		</div>
		<div class="panel-body ai-calculator-list-table-wrap">
			<form method="post" action="<?php echo esc_url( AI_Calculator_Router::url( 'product', 'bulk_delete' ) ); ?>">
				<?php wp_nonce_field( 'ai_calculator_product_bulk_delete' ); ?>
				<?php wp_nonce_field( 'ai_calculator_product_sort_order', 'ai_calculator_sort_nonce', false ); ?>
				<input type="hidden" name="filter_category" value="<?php echo (int) $category_id; ?>">
				<input type="hidden" name="filter_manufacturer" value="<?php echo (int) $manufacturer_id; ?>">
				<input type="hidden" name="paged" value="<?php echo (int) $page; ?>">

				<div class="ai-calculator-bulk-actions">
					<button type="submit" class="btn btn-danger" onclick="return confirm('<?php echo esc_js( __( 'Delete selected products?', 'ai-calculator' ) ); ?>');">
						<i class="fa fa-trash-o"></i> <?php esc_html_e( 'Удалить выбранные', 'ai-calculator' ); ?>
					</button>
				</div>

				<table class="table table-bordered table-hover ai-calculator-table">
					<thead>
						<tr>
							<td class="text-center" style="width: 42px;">
								<input type="checkbox" aria-label="<?php esc_attr_e( 'Select all products', 'ai-calculator' ); ?>" onclick="var checked = this.checked; document.querySelectorAll('.ai-calculator-product-check').forEach(function (checkbox) { checkbox.checked = checked; });">
							</td>
							<td class="text-right" style="width: 60px;"><?php esc_html_e( 'ID', 'ai-calculator' ); ?></td>
							<td class="text-left"><?php esc_html_e( 'Name', 'ai-calculator' ); ?></td>
							<td class="text-left"><?php esc_html_e( 'Наз. на русск.', 'ai-calculator' ); ?></td>
							<td class="text-left"><?php esc_html_e( 'Калькулятор', 'ai-calculator' ); ?></td>
							<td class="text-left"><?php esc_html_e( 'Категория', 'ai-calculator' ); ?></td>
							<td class="text-right ai-calculator-col-sort-order"><?php esc_html_e( 'Sort order', 'ai-calculator' ); ?></td>
							<td class="text-center"><?php esc_html_e( 'Status', 'ai-calculator' ); ?></td>
							<td class="text-right"><?php esc_html_e( 'Action', 'ai-calculator' ); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $products ) ) : ?>
							<tr><td colspan="9" class="text-center"><?php esc_html_e( 'No products yet.', 'ai-calculator' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $products as $product ) : ?>
								<tr>
									<td class="text-center">
										<input type="checkbox" class="ai-calculator-product-check" name="product_ids[]" value="<?php echo (int) $product->product_id; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Select product %s', 'ai-calculator' ), $product->name ? $product->name : '#' . $product->product_id ) ); ?>">
									</td>
									<td class="text-right"><?php echo (int) $product->product_id; ?></td>
									<td>
										<input type="text" class="form-control input-sm ai-calculator-inline-input" data-field="name" value="<?php echo esc_attr( $product->name ? $product->name : '' ); ?>" style="display:inline-block;width:180px;">
										<button
											type="button"
											class="btn btn-success btn-sm ai-calculator-inline-save"
											data-product-id="<?php echo (int) $product->product_id; ?>"
											data-language-id="<?php echo (int) $admin_language_id; ?>"
											data-field="name"
											title="<?php esc_attr_e( 'Save name', 'ai-calculator' ); ?>"
										>
											<i class="fa fa-save"></i>
										</button>
										<span class="ai-calculator-inline-status small" aria-live="polite"></span>
									</td>
									<td>
										<input type="text" class="form-control input-sm ai-calculator-inline-input" data-field="block6" value="<?php echo esc_attr( ! empty( $product->russian_name ) ? wp_strip_all_tags( $product->russian_name ) : '' ); ?>" style="display:inline-block;width:180px;">
										<button
											type="button"
											class="btn btn-success btn-sm ai-calculator-inline-save"
											data-product-id="<?php echo (int) $product->product_id; ?>"
											data-language-id="<?php echo (int) $admin_language_id; ?>"
											data-field="block6"
											title="<?php esc_attr_e( 'Save Russian name', 'ai-calculator' ); ?>"
										>
											<i class="fa fa-save"></i>
										</button>
										<span class="ai-calculator-inline-status small" aria-live="polite"></span>
									</td>
									<td><?php echo ! empty( $product->manufacturer_name ) ? esc_html( $product->manufacturer_name ) : '—'; ?></td>
									<td><?php echo ! empty( $product->category_name ) ? esc_html( $product->category_name ) : '—'; ?></td>
									<td class="text-right ai-calculator-col-sort-order">
										<input type="number" class="form-control input-sm ai-calculator-sort-input" name="sort_order[<?php echo (int) $product->product_id; ?>]" value="<?php echo (int) ( $product->sort_order ?? 0 ); ?>" style="display:inline-block;width:70px;">
										<button
											type="button"
											class="btn btn-success btn-sm ai-calculator-sort-save"
											data-product-id="<?php echo (int) $product->product_id; ?>"
											title="<?php esc_attr_e( 'Save sort order', 'ai-calculator' ); ?>"
										>
											<i class="fa fa-save"></i>
										</button>
										<span class="ai-calculator-sort-status small" aria-live="polite"></span>
									</td>
									<td class="text-center">
										<?php echo (int) $product->status ? esc_html__( 'Enabled', 'ai-calculator' ) : esc_html__( 'Disabled', 'ai-calculator' ); ?>
									</td>
									<td class="text-right">
										<a href="<?php echo esc_url( AI_Calculator_Router::url( 'product', 'form', (int) $product->product_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
										<a href="<?php echo esc_url( wp_nonce_url( AI_Calculator_Router::url( 'product', 'delete', (int) $product->product_id ), 'ai_calculator_product_delete_' . (int) $product->product_id ) ); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo esc_js( __( 'Delete this product?', 'ai-calculator' ) ); ?>');"><i class="fa fa-trash-o"></i></a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</form>

			<?php if ( $pages > 1 ) : ?>
				<nav class="ai-calculator-pagination">
					<ul>
						<?php
						$base = add_query_arg(
							array(
								'page'                => 'ai_calculator_products',
								'filter_category'     => $category_id,
								'filter_manufacturer' => $manufacturer_id,
								'paged'               => '%#%',
							),
							admin_url( 'admin.php' )
						);
						$links = paginate_links(
							array(
								'base'      => $base,
								'format'    => '',
								'current'   => $page,
								'total'     => $pages,
								'prev_text' => '&laquo;',
								'next_text' => '&raquo;',
								'type'      => 'array',
							)
						);
						if ( is_array( $links ) ) :
							foreach ( $links as $link ) :
								if ( strpos( $link, 'current' ) !== false ) {
									echo '<li><span class="ai-calculator-page-current">' . wp_kses_post( strip_tags( $link ) ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									echo '<li>' . str_replace( 'page-numbers', 'ai-calculator-page-link', $link ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
							endforeach;
						endif;
						?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</div>
