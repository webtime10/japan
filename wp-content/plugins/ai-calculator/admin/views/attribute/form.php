<?php
/**
 * Attribute form.
 *
 * @var object|null $attribute
 * @var array       $descriptions
 * @var array       $languages
 * @var array       $group_options
 * @var int         $selected_group_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id       = $attribute ? (int) $attribute->attribute_id : 0;
$form_id  = 'ai-calculator-form-attribute';
$save_url = AI_Calculator_Router::url( 'attribute', 'save' );
$group_id = $attribute ? (int) $attribute->attribute_group_id : (int) $selected_group_id;
?>
<form id="<?php echo esc_attr( $form_id ); ?>" method="post" action="<?php echo esc_url( $save_url ); ?>" class="form-horizontal ai-calculator-form">
	<?php wp_nonce_field( 'ai_calculator_attribute_save' ); ?>
	<input type="hidden" name="attribute_id" value="<?php echo esc_attr( (string) $id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'General', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<?php
			$panel_prefix = 'attr-lang';
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
				<label class="control-label" for="attr-group"><?php esc_html_e( 'Группа атрибутов', 'ai-calculator' ); ?></label>
				<select name="attribute_group_id" id="attr-group" class="form-control" required>
					<option value="0"><?php esc_html_e( '— None —', 'ai-calculator' ); ?></option>
					<?php foreach ( $group_options as $gid => $label ) : ?>
						<?php if ( (int) $gid <= 0 ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<option value="<?php echo (int) $gid; ?>" <?php selected( $group_id, (int) $gid ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group">
				<label class="control-label" for="attr-sort"><?php esc_html_e( 'Sort order', 'ai-calculator' ); ?></label>
				<input type="number" class="form-control" id="attr-sort" name="sort_order" value="<?php echo $attribute ? (int) $attribute->sort_order : 0; ?>">
			</div>
		</div>
	</div>
</form>
