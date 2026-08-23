<?php
/**
 * Language form.
 *
 * @var object|null $language
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id       = $language ? (int) $language->language_id : 0;
$form_id  = 'ai-calculator-form-language';
$save_url = AI_Calculator_Router::url( 'language', 'save' );
?>
<form id="<?php echo esc_attr( $form_id ); ?>" method="post" action="<?php echo esc_url( $save_url ); ?>" class="form-horizontal ai-calculator-form">
	<?php wp_nonce_field( 'ai_calculator_language_save' ); ?>
	<input type="hidden" name="language_id" value="<?php echo esc_attr( (string) $id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Language', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label" for="lang-name"><?php esc_html_e( 'Name', 'ai-calculator' ); ?></label>
				<input type="text" class="form-control" id="lang-name" name="name" value="<?php echo $language ? esc_attr( $language->name ) : ''; ?>" required>
			</div>
			<div class="form-group">
				<label class="control-label" for="lang-code"><?php esc_html_e( 'Code', 'ai-calculator' ); ?></label>
				<input type="text" class="form-control" id="lang-code" name="code" value="<?php echo $language ? esc_attr( $language->code ) : ''; ?>" maxlength="5" required>
			</div>
			<div class="form-group">
				<label class="control-label" for="lang-locale"><?php esc_html_e( 'Locale', 'ai-calculator' ); ?></label>
				<input type="text" class="form-control" id="lang-locale" name="locale" value="<?php echo $language ? esc_attr( $language->locale ) : ''; ?>">
			</div>
			<div class="form-group">
				<label class="control-label" for="lang-sort"><?php esc_html_e( 'Sort order', 'ai-calculator' ); ?></label>
				<input type="number" class="form-control" id="lang-sort" name="sort_order" value="<?php echo $language ? (int) $language->sort_order : 0; ?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php esc_html_e( 'Status', 'ai-calculator' ); ?></label>
				<label><input type="checkbox" name="status" value="1" <?php checked( ! $language || (int) $language->status ); ?>> <?php esc_html_e( 'Enabled', 'ai-calculator' ); ?></label>
			</div>
		</div>
	</div>
</form>
