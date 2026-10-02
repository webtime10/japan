<?php
/**
 * Post form.
 *
 * @var object|null $post
 * @var string      $name
 * @var string      $description
 * @var string      $route
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id           = $post ? (int) $post->post_id : 0;
$form_id      = 'fcc-form-post';
$save_url     = FCC_Router::url( $route, 'save' );
$image        = $post ? (string) $post->image : '';
$url          = $post ? (string) $post->url : '';
$direction_id = $post ? (int) $post->direction_id : 0;

if ( $direction_id <= 0 && '' !== $name ) {
	$direction_id = fcc_find_direction_id_by_name( $name );
}

$auto_tags = $direction_id > 0 ? fcc_get_wp_page_tags_for_direction( $direction_id ) : array();
?>
<form id="<?php echo esc_attr( $form_id ); ?>" method="post" action="<?php echo esc_url( $save_url ); ?>" class="form-horizontal ai-calculator-form fcc-page-meta-fields">
	<?php wp_nonce_field( 'fcc_post_save' ); ?>
	<input type="hidden" name="post_id" value="<?php echo esc_attr( (string) $id ); ?>">
	<input type="hidden" name="direction_id" id="fcc-post-direction" value="<?php echo esc_attr( (string) $direction_id ); ?>">

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Название и описание', 'family-comfort-calc' ); ?></h3></div>
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label" for="fcc-post-name"><?php esc_html_e( 'Название', 'family-comfort-calc' ); ?></label>
				<input type="text" class="form-control" id="fcc-post-name" name="name" value="<?php echo esc_attr( $name ); ?>" required>
				<p class="help-block"><?php esc_html_e( 'Название должно совпадать с городом (направлением) в Family Comfort — по нему подтягиваются теги со страниц.', 'family-comfort-calc' ); ?></p>
			</div>
			<div class="form-group">
				<label class="control-label" for="fcc-post-desc"><?php esc_html_e( 'Описание', 'family-comfort-calc' ); ?></label>
				<textarea class="form-control" rows="4" id="fcc-post-desc" name="description"><?php echo esc_textarea( $description ); ?></textarea>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Достопримечательности (теги)', 'family-comfort-calc' ); ?></h3></div>
		<div class="panel-body">
			<div class="fcc-auto-tags" id="fcc-auto-tags" data-direction="<?php echo esc_attr( (string) $direction_id ); ?>">
				<?php echo fcc_render_auto_tags_html( $auto_tags ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside helper ?>
			</div>
			<input type="hidden" name="fcc_page_tags_json" id="fcc-page-tags-json" value="<?php echo esc_attr( wp_json_encode( array_map( static function ( $t ) {
				return array(
					'label' => $t['label'],
					'url'   => $t['url'],
				);
			}, $auto_tags ) ) ); ?>">
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Параметры', 'family-comfort-calc' ); ?></h3></div>
		<div class="panel-body">
			<div class="form-group fcc-city-page-picker">
				<label class="control-label" for="fcc-city-page-search"><?php esc_html_e( 'Поиск страницы города', 'family-comfort-calc' ); ?></label>
				<input type="hidden" id="fcc-city-page-id" value="">
				<input type="hidden" name="image" id="fcc-page-image" value="<?php echo esc_attr( $image ); ?>">
				<input type="hidden" name="url" id="fcc-post-url" value="<?php echo esc_attr( $url ); ?>">
				<div class="fcc-city-page-picker__field">
					<input type="search" class="form-control" id="fcc-city-page-search" autocomplete="off" placeholder="<?php esc_attr_e( 'Начните вводить название (от 3 букв)…', 'family-comfort-calc' ); ?>">
					<ul class="fcc-city-page-results" id="fcc-city-page-results" hidden></ul>
				</div>
				<p class="fcc-city-page-status help-block" id="fcc-city-page-status"></p>

				<div class="fcc-city-page-preview" id="fcc-city-page-preview" <?php echo ( '' === $image && '' === $url ) ? 'hidden' : ''; ?>>
					<div class="fcc-city-page-preview__thumb" id="fcc-city-page-preview-thumb">
						<?php if ( '' !== $image ) : ?>
							<img src="<?php echo esc_url( $image ); ?>" alt="">
						<?php endif; ?>
					</div>
					<div class="fcc-city-page-preview__meta">
						<div class="fcc-city-page-preview__label"><?php esc_html_e( 'Ссылка города', 'family-comfort-calc' ); ?></div>
						<a class="fcc-city-page-preview__url" id="fcc-city-page-preview-url" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $url ); ?></a>
					</div>
				</div>

				<p class="help-block"><?php esc_html_e( 'Поиск среди страниц Default. Фото (thumbnail) и ссылка подтянутся автоматически.', 'family-comfort-calc' ); ?></p>
			</div>
			<div class="form-group">
				<label class="control-label" for="fcc-post-sort"><?php esc_html_e( 'Сортировка', 'family-comfort-calc' ); ?></label>
				<input type="number" class="form-control" id="fcc-post-sort" name="sort_order" value="<?php echo $post ? (int) $post->sort_order : 0; ?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php esc_html_e( 'Статус', 'family-comfort-calc' ); ?></label>
				<label><input type="checkbox" name="status" value="1" <?php checked( ! $post || (int) $post->status ); ?>> <?php esc_html_e( 'Включено', 'family-comfort-calc' ); ?></label>
			</div>
		</div>
	</div>
</form>
