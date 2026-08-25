<?php
/**
 * Language tabs for forms (Map Plum style).
 *
 * @var array  $languages
 * @var array  $descriptions
 * @var string $panel_prefix
 * @var bool   $show_meta
 * @var bool   $name_only
 * @var bool   $show_product_blocks
 * @var bool   $show_attribute_blocks
 * @var bool   $show_family_comfort_card
 * @var bool   $hide_name
 * @var bool   $show_russian_name
 * @var string $description_label
 * @var string $name_label
 * @var string $russian_name_label
 * @var string $description_post_key POST array key (description | fc_description).
 * @var bool   $show_product_image
 * @var array  $product_images
 */

if ( ! defined( 'ABSPATH' ) || empty( $languages ) ) {
	return;
}

$panel_prefix         = ! empty( $panel_prefix ) ? $panel_prefix : 'lang';
$show_meta            = ! empty( $show_meta );
$name_only            = ! empty( $name_only );
$show_product_blocks  = ! empty( $show_product_blocks );
$show_attribute_blocks = ! empty( $show_attribute_blocks );
$show_family_comfort_card = ! empty( $show_family_comfort_card );
$hide_name            = ! empty( $hide_name );
$show_russian_name    = ! empty( $show_russian_name );
$description_label    = ! empty( $description_label ) ? (string) $description_label : __( 'Description', 'ai-calculator' );
$name_label           = ! empty( $name_label ) ? (string) $name_label : __( 'Name', 'ai-calculator' );
$russian_name_label   = ! empty( $russian_name_label ) ? (string) $russian_name_label : __( 'Название на русском', 'ai-calculator' );
$description_post_key = ! empty( $description_post_key ) ? (string) $description_post_key : 'description';
$show_product_image   = ! empty( $show_product_image );
$product_images       = isset( $product_images ) && is_array( $product_images ) ? array_values( $product_images ) : array( '', '', '', '', '', '' );
while ( count( $product_images ) < 6 ) {
	$product_images[] = '';
}
$product_images = array_slice( $product_images, 0, 6 );
?>
<ul class="nav nav-tabs ai-calculator-lang-tabs" role="tablist">
	<?php foreach ( $languages as $i => $lang ) : ?>
		<li class="<?php echo 0 === $i ? 'active' : ''; ?>">
			<a href="#<?php echo esc_attr( $panel_prefix . '-' . (int) $lang->language_id ); ?>" data-toggle="tab">
				<?php echo esc_html( $lang->name ); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
<div class="tab-content ai-calculator-tab-content">
	<?php foreach ( $languages as $i => $lang ) :
		$lid = (int) $lang->language_id;
		$d   = isset( $descriptions[ $lid ] ) ? $descriptions[ $lid ] : null;
		?>
		<div class="tab-pane <?php echo 0 === $i ? 'active' : ''; ?>" id="<?php echo esc_attr( $panel_prefix . '-' . $lid ); ?>">
			<?php if ( ! $hide_name ) : ?>
			<div class="form-group">
				<label class="control-label"><?php echo esc_html( $name_label ); ?></label>
				<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][name]" value="<?php echo esc_attr( $d && isset( $d->name ) ? $d->name : '' ); ?>" class="form-control large-field">
			</div>
			<?php endif; ?>
			<?php if ( $show_russian_name ) : ?>
			<div class="form-group prod-russian-name">
				<label class="control-label"><?php echo esc_html( $russian_name_label ); ?></label>
				<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][name_ru]" value="<?php echo esc_attr( $d && isset( $d->block6 ) ? $d->block6 : '' ); ?>" class="form-control large-field ai-product-name-ru">
			</div>
			<?php endif; ?>
			<?php if ( ! $name_only ) : ?>
			<div class="prod-general-extra">
				<div class="form-group">
					<label class="control-label"><?php echo esc_html( $description_label ); ?></label>
					<textarea name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][description]" rows="5" class="form-control"><?php echo esc_textarea( $d && isset( $d->description ) ? $d->description : '' ); ?></textarea>
				</div>
				<?php if ( $show_attribute_blocks ) : ?>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Поле 1', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block1]" value="<?php echo esc_attr( $d && isset( $d->block1 ) ? $d->block1 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Поле 2', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block2]" value="<?php echo esc_attr( $d && isset( $d->block2 ) ? $d->block2 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Поле 3', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block3]" value="<?php echo esc_attr( $d && isset( $d->block3 ) ? $d->block3 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Поле 4', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block4]" value="<?php echo esc_attr( $d && isset( $d->block4 ) ? $d->block4 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Поле 5', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block5]" value="<?php echo esc_attr( $d && isset( $d->block5 ) ? $d->block5 : '' ); ?>" class="form-control">
					</div>
				<?php endif; ?>
				<?php if ( $show_product_blocks ) : ?>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Блок1', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block1]" value="<?php echo esc_attr( $d && isset( $d->block1 ) ? $d->block1 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Блок2', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block2]" value="<?php echo esc_attr( $d && isset( $d->block2 ) ? $d->block2 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Блок3', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block3]" value="<?php echo esc_attr( $d && isset( $d->block3 ) ? $d->block3 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Блок4', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block4]" value="<?php echo esc_attr( $d && isset( $d->block4 ) ? $d->block4 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Блок5', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block5]" value="<?php echo esc_attr( $d && isset( $d->block5 ) ? $d->block5 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Блок 6_1', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block6_1]" value="<?php echo esc_attr( $d && isset( $d->block7 ) ? $d->block7 : '' ); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'dop1', 'ai-calculator' ); ?></label>
						<input
							type="text"
							name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][dop1]"
							value="<?php echo esc_attr( $d && isset( $d->dop1 ) ? $d->dop1 : '' ); ?>"
							class="form-control"
							placeholder="<?php esc_attr_e( 'Один вариант ответа / До двух вариантов ответа / До трёх вариантов ответа', 'ai-calculator' ); ?>"
						>
						<p class="description"><?php esc_html_e( 'Подсказка слева у вопроса на фронте Ideal Region.', 'ai-calculator' ); ?></p>
					</div>
					<?php if ( $show_product_image && 0 === $i ) : ?>
					<div class="prod-general-images">
						<?php for ( $photo_index = 0; $photo_index < 6; $photo_index++ ) :
							$photo_num   = $photo_index + 1;
							$field_name  = 1 === $photo_num ? 'image' : 'image' . $photo_num;
							$field_id    = 'prod-image-' . $photo_num;
							$photo_value = (string) $product_images[ $photo_index ];
							?>
						<div class="form-group ai-calculator-media-field prod-general-image">
							<label class="control-label" for="<?php echo esc_attr( $field_id ); ?>">
								<?php
								printf(
									/* translators: %d: photo number */
									esc_html__( 'Фото %d', 'ai-calculator' ),
									$photo_num
								);
								?>
							</label>
							<div class="ai-calculator-media-field__controls">
								<input type="text" class="form-control ai-calculator-media-input" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $photo_value ); ?>">
								<button type="button" class="button ai-calculator-media-select"><?php esc_html_e( 'Выбрать', 'ai-calculator' ); ?></button>
								<button type="button" class="button ai-calculator-media-clear" <?php disabled( '' === $photo_value ); ?> aria-label="<?php esc_attr_e( 'Удалить фото', 'ai-calculator' ); ?>">&times;</button>
							</div>
							<div class="ai-calculator-media-preview">
								<?php if ( '' !== $photo_value ) : ?>
									<img src="<?php echo esc_url( $photo_value ); ?>" alt="">
								<?php endif; ?>
							</div>
						</div>
						<?php endfor; ?>
					</div>
					<?php endif; ?>
					<?php if ( ! $show_russian_name ) : ?>
					<div class="form-group">
						<label class="control-label"><?php esc_html_e( 'Блок6', 'ai-calculator' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block6]" value="<?php echo esc_attr( $d && isset( $d->block6 ) ? $d->block6 : '' ); ?>" class="form-control">
					</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ( $show_family_comfort_card ) : ?>
				<div class="ai-calculator-family-comfort-card__inner">
					<div class="ai-calculator-family-comfort-card__side">
						<div class="form-group">
							<label class="control-label"><?php esc_html_e( 'URL', 'ai-calculator' ); ?></label>
							<input type="url" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][block1]" value="<?php echo esc_attr( $d && isset( $d->block1 ) ? $d->block1 : '' ); ?>" class="form-control">
						</div>
						<?php for ( $block = 2; $block <= 8; $block++ ) : ?>
							<?php
							if ( 6 === $block ) {
								continue;
							}
							$field      = 'block' . $block;
							$metka_num  = $block <= 6 ? $block - 1 : $block - 2;
							?>
							<div class="form-group">
								<label class="control-label">
									<?php
									printf(
										/* translators: %d: tag number */
										esc_html__( 'Метка %d', 'ai-calculator' ),
										$metka_num
									);
									?>
								</label>
								<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][<?php echo esc_attr( $field ); ?>]" value="<?php echo esc_attr( $d && isset( $d->$field ) ? $d->$field : '' ); ?>" class="form-control">
							</div>
						<?php endfor; ?>
					</div>
					<div class="ai-calculator-family-comfort-card__main">
						<div class="form-group">
							<label class="control-label"><?php esc_html_e( 'Текст', 'ai-calculator' ); ?></label>
							<textarea name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][description]" rows="12" class="form-control"><?php echo esc_textarea( $d && isset( $d->description ) ? $d->description : '' ); ?></textarea>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( $show_meta ) : ?>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Meta title', 'ai-calculator' ); ?></label>
					<input type="text" name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][meta_title]" value="<?php echo esc_attr( $d && isset( $d->meta_title ) ? $d->meta_title : '' ); ?>" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Meta description', 'ai-calculator' ); ?></label>
					<textarea name="<?php echo esc_attr( $description_post_key ); ?>[<?php echo esc_attr( (string) $lid ); ?>][meta_description]" rows="3" class="form-control"><?php echo esc_textarea( $d && isset( $d->meta_description ) ? $d->meta_description : '' ); ?></textarea>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
