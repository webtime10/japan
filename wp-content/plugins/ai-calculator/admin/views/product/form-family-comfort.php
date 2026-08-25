<?php
/**
 * Family Comfort calculator card fields on product form.
 *
 * @var array       $languages
 * @var array       $descriptions
 * @var object|null $product
 * @var int         $family_comfort_manufacturer_id
 * @var bool        $is_family_comfort
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image         = $product && isset( $product->image ) ? (string) $product->image : '';
$panel_visible = ! empty( $is_family_comfort );
?>
<div
	id="ai-calculator-family-comfort-panel"
		class="panel panel-default ai-calculator-family-comfort-panel"
		data-manufacturer-id="<?php echo (int) $family_comfort_manufacturer_id; ?>"
	>
	<div class="panel-heading">
		<h3 class="panel-title"><?php esc_html_e( 'Калькулятор семейного отдыха', 'ai-calculator' ); ?></h3>
	</div>
	<div class="panel-body">
		<p
			id="ai-calculator-family-comfort-panel-hint"
			class="help-block ai-calculator-family-comfort-panel__hint"
			<?php echo $panel_visible ? 'style="display:none;"' : ''; ?>
		>
			<?php esc_html_e( 'Выберите калькулятор или категорию семейного отдыха в блоке Data — поля карточки появятся ниже. Название заполняется в General.', 'ai-calculator' ); ?>
		</p>
		<p class="help-block ai-calculator-family-comfort-panel__active-hint" <?php echo $panel_visible ? '' : 'style="display:none;"'; ?>>
			<?php esc_html_e( 'Карточка направления на калькуляторе: фото, текст, ссылка и метки.', 'ai-calculator' ); ?>
		</p>
		<div class="ai-calculator-family-comfort-card" <?php echo $panel_visible ? '' : 'style="display:none;"'; ?>>
			<div class="ai-calculator-family-comfort-card__media">
				<div class="form-group ai-calculator-media-field">
					<label class="control-label" for="prod-fc-image"><?php esc_html_e( 'Фото', 'ai-calculator' ); ?></label>
					<div class="ai-calculator-media-field__controls">
						<input type="text" class="form-control ai-calculator-media-input" id="prod-fc-image" name="image" value="<?php echo esc_attr( $image ); ?>">
						<button type="button" class="button ai-calculator-media-select"><?php esc_html_e( 'Выбрать', 'ai-calculator' ); ?></button>
						<button type="button" class="button ai-calculator-media-clear" <?php disabled( '' === $image ); ?> aria-label="<?php esc_attr_e( 'Удалить фото', 'ai-calculator' ); ?>">&times;</button>
					</div>
					<div class="ai-calculator-media-preview">
						<?php if ( '' !== $image ) : ?>
							<img src="<?php echo esc_url( $image ); ?>" alt="">
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="ai-calculator-family-comfort-card__content">
				<?php
				$panel_prefix             = 'fc-lang';
				$description_post_key     = 'fc_description';
				$hide_name                = true;
				$show_russian_name        = false;
				$show_family_comfort_card = true;
				$name_only                = true;
				$show_product_blocks      = false;
				$show_meta                = false;
				include AI_CALCULATOR_PATH . 'admin/views/partials/language-tabs.php';
				?>
			</div>
		</div>
	</div>
</div>
