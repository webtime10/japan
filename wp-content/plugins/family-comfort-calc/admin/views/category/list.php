<?php
/**
 * Categories list.
 *
 * @var array $categories
 * @var int   $total
 * @var int   $page
 * @var int   $pages
 * @var string $route
 * @var string $page_slug
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ai-calculator-list-fullwidth">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Список категорий', 'family-comfort-calc' ); ?></h3>
		</div>
		<div class="panel-body ai-calculator-list-table-wrap">
			<form method="post" action="<?php echo esc_url( FCC_Router::url( $route, 'bulk_delete' ) ); ?>">
				<?php wp_nonce_field( 'fcc_category_bulk_delete' ); ?>
				<input type="hidden" name="paged" value="<?php echo (int) $page; ?>">

				<div class="ai-calculator-bulk-actions">
					<button type="submit" class="btn btn-danger" onclick="return confirm('<?php echo esc_js( __( 'Удалить выбранные категории?', 'family-comfort-calc' ) ); ?>');">
						<i class="fa fa-trash-o"></i> <?php esc_html_e( 'Удалить выбранные', 'family-comfort-calc' ); ?>
					</button>
				</div>

				<table class="table table-bordered table-hover ai-calculator-table">
					<thead>
						<tr>
							<td class="text-center" style="width: 42px;">
								<input type="checkbox" aria-label="<?php esc_attr_e( 'Выбрать все', 'family-comfort-calc' ); ?>" onclick="var checked = this.checked; document.querySelectorAll('.fcc-category-check').forEach(function (checkbox) { checkbox.checked = checked; });">
							</td>
							<td class="text-left"><?php esc_html_e( 'Название', 'family-comfort-calc' ); ?></td>
							<td class="text-right"><?php esc_html_e( 'Сортировка', 'family-comfort-calc' ); ?></td>
							<td class="text-center"><?php esc_html_e( 'Статус', 'family-comfort-calc' ); ?></td>
							<td class="text-right"><?php esc_html_e( 'Действие', 'family-comfort-calc' ); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $categories ) ) : ?>
							<tr><td colspan="5" class="text-center"><?php esc_html_e( 'Категорий ещё нет.', 'family-comfort-calc' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $categories as $cat ) : ?>
								<tr>
									<td class="text-center">
										<input type="checkbox" class="fcc-category-check" name="category_ids[]" value="<?php echo (int) $cat->category_id; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Выбрать %s', 'family-comfort-calc' ), $cat->name ? $cat->name : '#' . (int) $cat->category_id ) ); ?>">
									</td>
									<td><?php echo esc_html( $cat->name ? $cat->name : '#' . (int) $cat->category_id ); ?></td>
									<td class="text-right"><?php echo (int) $cat->sort_order; ?></td>
									<td class="text-center">
										<?php echo (int) $cat->status ? esc_html__( 'Включено', 'family-comfort-calc' ) : esc_html__( 'Отключено', 'family-comfort-calc' ); ?>
									</td>
									<td class="text-right">
										<a href="<?php echo esc_url( FCC_Router::url( $route, 'form', (int) $cat->category_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
										<a href="<?php echo esc_url( wp_nonce_url( FCC_Router::url( $route, 'delete', (int) $cat->category_id ), 'fcc_category_delete_' . (int) $cat->category_id ) ); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo esc_js( __( 'Удалить эту категорию?', 'family-comfort-calc' ) ); ?>');"><i class="fa fa-trash-o"></i></a>
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
								'page'  => $page_slug,
								'paged' => '%#%',
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
