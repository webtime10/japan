<?php
/**
 * Language tabs for forms.
 *
 * @var array $languages
 * @var string $panel_prefix e.g. 'lang'
 */

if ( empty( $languages ) ) {
	return;
}
?>
<ul class="nav nav-tabs map-plum-lang-tabs" role="tablist">
	<?php foreach ( $languages as $i => $lang ) : ?>
		<li class="<?php echo 0 === $i ? 'active' : ''; ?>">
			<a href="#<?php echo esc_attr( $panel_prefix . '-' . (int) $lang->language_id ); ?>" data-toggle="tab">
				<?php echo esc_html( $lang->name ); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
<div class="tab-content map-plum-tab-content">
	<?php foreach ( $languages as $i => $lang ) :
		$lid = (int) $lang->language_id;
		$d   = isset( $descriptions[ $lid ] ) ? $descriptions[ $lid ] : null;
		?>
		<div class="tab-pane <?php echo 0 === $i ? 'active' : ''; ?>" id="<?php echo esc_attr( $panel_prefix . '-' . $lid ); ?>">
			<div class="form-group">
				<label class="control-label">Название</label>
				<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][name]" value="<?php echo esc_attr( $d && isset( $d->name ) ? $d->name : '' ); ?>" class="form-control large-field">
			</div>
			<?php if ( empty( $name_only ) ) : ?>
			<div class="form-group">
				<label class="control-label">Название на арабском</label>
				<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][description]" value="<?php echo esc_attr( $d && isset( $d->description ) ? $d->description : '' ); ?>" class="form-control large-field">
			</div>
			<?php endif; ?>
			<?php if ( ! empty( $show_meta ) ) : ?>
				<div class="form-group">
					<label class="control-label">Meta Title</label>
					<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][meta_title]" value="<?php echo esc_attr( $d && isset( $d->meta_title ) ? $d->meta_title : '' ); ?>" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label">Meta Description</label>
					<textarea name="description[<?php echo esc_attr( (string) $lid ); ?>][meta_description]" rows="3" class="form-control"><?php echo esc_textarea( $d && isset( $d->meta_description ) ? $d->meta_description : '' ); ?></textarea>
				</div>
				<?php if ( ! empty( $show_meta_keyword ) ) : ?>
					<div class="form-group">
						<label class="control-label">Meta Keyword</label>
						<textarea name="description[<?php echo esc_attr( (string) $lid ); ?>][meta_keyword]" rows="2" class="form-control"><?php echo esc_textarea( $d && isset( $d->meta_keyword ) ? $d->meta_keyword : '' ); ?></textarea>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
