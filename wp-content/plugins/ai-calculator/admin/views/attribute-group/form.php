<?php
/**
 * Attribute group form.
 *
 * @var object|null $group
 * @var array       $descriptions
 * @var array       $languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id       = $group ? (int) $group->attribute_group_id : 0;
$form_id  = 'ai-calculator-form-attribute-group';
$save_url = AI_Calculator_Router::url( 'attribute_group', 'save' );
?>
<form id="<?php echo esc_attr( $form_id ); ?>" method="post" action="<?php echo esc_url( $save_url ); ?>" class="form-horizontal ai-calculator-form">
	<?php wp_nonce_field( 'ai_calculator_attribute_group_save' ); ?>
	<input type="hidden" name="attribute_group_id" value="<?php echo esc_attr( (string) $id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'General', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<?php
			$panel_prefix = 'attr-group-lang';
			$show_meta    = false;
			$name_only    = true;
			include AI_CALCULATOR_PATH . 'admin/views/partials/language-tabs.php';
			?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Data', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label" for="attr-group-sort"><?php esc_html_e( 'Sort order', 'ai-calculator' ); ?></label>
				<input type="number" class="form-control" id="attr-group-sort" name="sort_order" value="<?php echo $group ? (int) $group->sort_order : 0; ?>">
			</div>
		</div>
	</div>
</form>
