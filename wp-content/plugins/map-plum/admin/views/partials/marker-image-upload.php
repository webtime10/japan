<?php
/**
 * Загрузка изображения маркера.
 *
 * @var object|null $item
 */
$current_image = ( $item && ! empty( $item->image ) ) ? (string) $item->image : '';
$current_id    = ( $item && ! empty( $item->image_id ) ) ? (int) $item->image_id : 0;
?>
<div class="map-plum-image-upload">
	<input type="hidden" name="image" id="map-plum-marker-image-url" value="<?php echo esc_attr( $current_image ); ?>">
	<input type="hidden" name="image_attachment_id" id="map-plum-marker-image-id" value="<?php echo esc_attr( (string) $current_id ); ?>">

	<div class="map-plum-image-preview-wrap">
		<?php if ( $current_image ) : ?>
			<img src="<?php echo esc_url( $current_image ); ?>" alt="" class="map-plum-image-preview" id="map-plum-marker-image-preview">
		<?php else : ?>
			<div class="map-plum-image-preview map-plum-image-preview-empty" id="map-plum-marker-image-preview">Нет изображения</div>
		<?php endif; ?>
	</div>

	<p class="map-plum-image-upload-actions">
		<button type="button" class="button" id="map-plum-marker-image-select">Выбрать из медиатеки</button>
		<span class="map-plum-image-upload-or">или</span>
		<label class="button map-plum-image-upload-file-label">
			Загрузить файл
			<input type="file" name="marker_image_file" id="map-plum-marker-image-file" accept="image/jpeg,image/png,image/webp,image/gif" class="map-plum-image-upload-file-input">
		</label>
		<button type="button" class="button" id="map-plum-marker-image-remove">Убрать</button>
	</p>
	<p class="description">ID изображения: <span id="map-plum-marker-image-id-view"><?php echo esc_html( (string) $current_id ); ?></span></p>
</div>
