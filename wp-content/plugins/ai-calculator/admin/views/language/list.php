<?php
/**
 * Languages list.
 *
 * @var array<int, object> $languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ai-calculator-list-fullwidth">
	<div class="ai-calculator-list-toolbar">
		<a href="<?php echo esc_url( wp_nonce_url( AI_Calculator_Router::url( 'language', 'sync_polylang' ), 'ai_calculator_sync_polylang' ) ); ?>" class="btn btn-default">
			<i class="fa fa-download"></i> <?php esc_html_e( 'Import from Polylang', 'ai-calculator' ); ?>
		</a>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-list"></i> <?php esc_html_e( 'Language List', 'ai-calculator' ); ?></h3>
		</div>
		<div class="panel-body ai-calculator-list-table-wrap">
			<table class="table table-bordered table-hover ai-calculator-table">
				<thead>
					<tr>
						<td class="text-left"><?php esc_html_e( 'Name', 'ai-calculator' ); ?></td>
						<td class="text-left"><?php esc_html_e( 'Code', 'ai-calculator' ); ?></td>
						<td class="text-left"><?php esc_html_e( 'Locale', 'ai-calculator' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Sort', 'ai-calculator' ); ?></td>
						<td class="text-center"><?php esc_html_e( 'Status', 'ai-calculator' ); ?></td>
						<td class="text-right"><?php esc_html_e( 'Action', 'ai-calculator' ); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $languages ) ) : ?>
						<tr><td colspan="6" class="text-center"><?php esc_html_e( 'No languages yet.', 'ai-calculator' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $languages as $lang ) : ?>
							<tr>
								<td><?php echo esc_html( $lang->name ); ?></td>
								<td><code><?php echo esc_html( $lang->code ); ?></code></td>
								<td><?php echo esc_html( $lang->locale ); ?></td>
								<td class="text-right"><?php echo (int) $lang->sort_order; ?></td>
								<td class="text-center">
									<?php echo (int) $lang->status ? esc_html__( 'Enabled', 'ai-calculator' ) : esc_html__( 'Disabled', 'ai-calculator' ); ?>
								</td>
								<td class="text-right">
									<a href="<?php echo esc_url( AI_Calculator_Router::url( 'language', 'form', (int) $lang->language_id ) ); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
									<?php if ( count( $languages ) > 1 ) : ?>
										<a href="<?php echo esc_url( wp_nonce_url( AI_Calculator_Router::url( 'language', 'delete', (int) $lang->language_id ), 'ai_calculator_language_delete_' . (int) $lang->language_id ) ); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo esc_js( __( 'Delete this language?', 'ai-calculator' ) ); ?>');"><i class="fa fa-trash-o"></i></a>
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
