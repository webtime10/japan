<?php
/**
 * Posts list.
 *
 * @var array  $posts
 * @var int    $total
 * @var int    $page
 * @var int    $pages
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
			<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Список постов', 'family-comfort-calc' ); ?></h3>
		</div>
		<div class="panel-body ai-calculator-list-table-wrap">
			<form method="post" action="<?php echo esc_url( FCC_Router::url( $route, 'bulk_delete' ) ); ?>">
				<?php wp_nonce_field( 'fcc_post_bulk_delete' ); ?>
				<input type="hidden" name="paged" value="<?php echo (int) $page; ?>">

				<div class="ai-calculator-bulk-actions">
					<button type="submit" class="btn btn-danger" onclick="return confirm('<?php echo esc_js( __( 'Удалить выбранные посты?', 'family-comfort-calc' ) ); ?>');">
						<i class="fa fa-trash-o"></i> <?php esc_html_e( 'Удалить выбранные', 'family-comfort-calc' ); ?>
					</button>
				</div>

				<table class="table table-bordered table-hover ai-calculator-table">
					<thead>
						<tr>
							<td class="text-center" style="width: 42px;">
								<input type="checkbox" aria-label="<?php esc_attr_e( 'Выбрать все', 'family-comfort-calc' ); ?>" onclick="var checked = this.checked; document.querySelectorAll('.fcc-post-check').forEach(function (checkbox) { checkbox.checked = checked; });">
							</td>
							<td class="text-left"><?php esc_html_e( 'Название', 'family-comfort-calc' ); ?></td>
							<td class="text-left"><?php esc_html_e( 'Направление', 'family-comfort-calc' ); ?></td>
							<td class="text-right"><?php esc_html_e( 'Сортировка', 'family-comfort-calc' ); ?></td>
							<td class="text-center"><?php esc_html_e( 'Статус', 'family-comfort-calc' ); ?></td>
							<td class="text-right"><?php esc_html_e( 'Действие', 'family-comfort-calc' ); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $posts ) ) : ?>
							<tr><td colspan="6" class="text-center"><?php esc_html_e( 'Постов ещё нет. Нажмите «Добавить».', 'family-comfort-calc' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $posts as $item ) : ?>
								<tr>
									<td class="text-center">
										<input type="checkbox" class="fcc-post-check" name="post_ids[]" value="<?php echo (int) $item->post_id; ?>">
									</td>
									<td><?php echo esc_html( $item->name ? $item->name : '#' . (int) $item->post_id ); ?></td>
									<td><?php echo esc_html( ! empty( $item->direction_name ) ? $item->direction_name : '—' ); ?></td>
									<td class="text-right"><?php echo (int) $item->sort_order; ?></td>
									<td class="text-center">
										<?php echo (int) $item->status ? esc_html__( 'Включено', 'family-comfort-calc' ) : esc_html__( 'Отключено', 'family-comfort-calc' ); ?>
									</td>
									<td class="text-right">
										<a href="<?php echo esc_url( FCC_Router::url( $route, 'form', (int) $item->post_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
										<a href="<?php echo esc_url( wp_nonce_url( FCC_Router::url( $route, 'delete', (int) $item->post_id ), 'fcc_post_delete_' . (int) $item->post_id ) ); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo esc_js( __( 'Удалить этот пост?', 'family-comfort-calc' ) ); ?>');"><i class="fa fa-trash-o"></i></a>
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
