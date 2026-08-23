<?php
/**
 * Manufacturer form.
 *
 * @var object|null $manufacturer
 * @var array       $descriptions
 * @var array       $languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id       = $manufacturer ? (int) $manufacturer->manufacturer_id : 0;
$form_id  = 'ai-calculator-form-manufacturer';
$save_url = AI_Calculator_Router::url( 'manufacturer', 'save' );
?>
<form id="<?php echo esc_attr( $form_id ); ?>" method="post" action="<?php echo esc_url( $save_url ); ?>" class="form-horizontal ai-calculator-form">
	<?php wp_nonce_field( 'ai_calculator_manufacturer_save' ); ?>
	<input type="hidden" name="manufacturer_id" value="<?php echo esc_attr( (string) $id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'General', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<?php
			$panel_prefix = 'mfr-lang';
			$show_meta    = false;
			include AI_CALCULATOR_PATH . 'admin/views/partials/language-tabs.php';
			?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Data', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label" for="mfr-sort"><?php esc_html_e( 'Sort order', 'ai-calculator' ); ?></label>
				<input type="number" class="form-control" id="mfr-sort" name="sort_order" value="<?php echo $manufacturer ? (int) $manufacturer->sort_order : 0; ?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php esc_html_e( 'Status', 'ai-calculator' ); ?></label>
				<label><input type="checkbox" name="status" value="1" <?php checked( ! $manufacturer || (int) $manufacturer->status ); ?>> <?php esc_html_e( 'Enabled', 'ai-calculator' ); ?></label>
			</div>
		</div>
	</div>
</form>
