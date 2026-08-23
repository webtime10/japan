<?php
/**
 * DB dump template — print all plugin tables on page.
 *
 * @var array $ai_db_dump_summary
 * @var array $ai_db_dump_data
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$summary = isset( $ai_db_dump_summary ) ? $ai_db_dump_summary : array( 'prefix' => '', 'tables' => array() );
$dump    = isset( $ai_db_dump_data ) ? $ai_db_dump_data : array();
?>
<section class="ai-db-dump">
	<div class="ai-db-dump__head">
		<h2 class="ai-db-dump__title"><?php esc_html_e( 'AI Calculator — дамп базы', 'ai-calculator' ); ?></h2>
		<p class="ai-db-dump__meta">
			<?php
			printf(
				/* translators: 1: table prefix, 2: number of tables */
				esc_html__( 'Префикс: %1$s · Таблиц: %2$d', 'ai-calculator' ),
				esc_html( $summary['prefix'] ?? '' ),
				count( $summary['tables'] ?? array() )
			);
			?>
		</p>
	</div>

	<?php if ( empty( $summary['tables'] ) ) : ?>
		<p class="ai-db-dump__empty"><?php esc_html_e( 'Таблицы плагина не найдены. Обновите админку или переактивируйте плагин.', 'ai-calculator' ); ?></p>
	<?php else : ?>
		<table class="ai-db-dump__summary">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Таблица', 'ai-calculator' ); ?></th>
					<th><?php esc_html_e( 'Строк', 'ai-calculator' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $summary['tables'] as $tbl ) : ?>
					<tr>
						<td><code><?php echo esc_html( $tbl['short'] ); ?></code></td>
						<td><?php echo (int) $tbl['count']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php foreach ( $dump as $table_name => $rows ) : ?>
			<?php
			$short = str_replace( $summary['prefix'] ?? '', '', $table_name );
			$cols  = array();
			if ( ! empty( $rows ) ) {
				$cols = array_keys( $rows[0] );
			}
			?>
			<div class="ai-db-dump__block" id="<?php echo esc_attr( 'ai-db-' . sanitize_title( $short ) ); ?>">
				<h3 class="ai-db-dump__block-title">
					<?php echo esc_html( $short ); ?>
					<span class="ai-db-dump__count">(<?php echo count( $rows ); ?>)</span>
				</h3>

				<?php if ( empty( $rows ) ) : ?>
					<p class="ai-db-dump__empty-row"><?php esc_html_e( 'Пусто', 'ai-calculator' ); ?></p>
				<?php else : ?>
					<div class="ai-db-dump__table-wrap">
						<table class="ai-db-dump__data">
							<thead>
								<tr>
									<?php foreach ( $cols as $col ) : ?>
										<th><?php echo esc_html( $col ); ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $rows as $row ) : ?>
									<tr>
										<?php foreach ( $cols as $col ) : ?>
											<td>
												<?php
												$val = isset( $row[ $col ] ) ? $row[ $col ] : '';
												if ( is_array( $val ) || is_object( $val ) ) {
													echo '<pre class="ai-db-dump__pre">' . esc_html( print_r( $val, true ) ) . '</pre>';
												} else {
													$text = (string) $val;
													if ( strlen( $text ) > 500 ) {
														$text = substr( $text, 0, 500 ) . '…';
													}
													echo esc_html( $text );
												}
												?>
											</td>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

					<details class="ai-db-dump__raw">
						<summary><?php esc_html_e( 'print_r (сырой дамп)', 'ai-calculator' ); ?></summary>
						<pre class="ai-db-dump__pre"><?php echo esc_html( print_r( $rows, true ) ); ?></pre>
					</details>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</section>
