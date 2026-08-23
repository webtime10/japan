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
 * @var string $description_label
 */

if ( ! defined( 'ABSPATH' ) || empty( $languages ) ) {
	return;
}

$panel_prefix        = ! empty( $panel_prefix ) ? $panel_prefix : 'lang';
$show_meta           = ! empty( $show_meta );
$name_only           = ! empty( $name_only );
$show_product_blocks = ! empty( $show_product_blocks );
$description_label   = ! empty( $description_label ) ? (string) $description_label : __( 'Description', 'ai-calculator' );
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
			<div class="form-group">
				<label class="control-label"><?php esc_html_e( 'Name', 'ai-calculator' ); ?></label>
				<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][name]" value="<?php echo esc_attr( $d && isset( $d->name ) ? $d->name : '' ); ?>" class="form-control large-field">
			</div>
			<?php if ( ! $name_only ) : ?>
			<div class="form-group">
				<label class="control-label"><?php echo esc_html( $description_label ); ?></label>
				<textarea name="description[<?php echo esc_attr( (string) $lid ); ?>][description]" rows="5" class="form-control"><?php echo esc_textarea( $d && isset( $d->description ) ? $d->description : '' ); ?></textarea>
			</div>
			<?php endif; ?>
			<?php if ( $show_product_blocks ) : ?>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Блок1', 'ai-calculator' ); ?></label>
					<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][block1]" value="<?php echo esc_attr( $d && isset( $d->block1 ) ? $d->block1 : '' ); ?>" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Блок2', 'ai-calculator' ); ?></label>
					<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][block2]" value="<?php echo esc_attr( $d && isset( $d->block2 ) ? $d->block2 : '' ); ?>" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Блок3', 'ai-calculator' ); ?></label>
					<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][block3]" value="<?php echo esc_attr( $d && isset( $d->block3 ) ? $d->block3 : '' ); ?>" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Блок4', 'ai-calculator' ); ?></label>
					<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][block4]" value="<?php echo esc_attr( $d && isset( $d->block4 ) ? $d->block4 : '' ); ?>" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Блок5', 'ai-calculator' ); ?></label>
					<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][block5]" value="<?php echo esc_attr( $d && isset( $d->block5 ) ? $d->block5 : '' ); ?>" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Блок6', 'ai-calculator' ); ?></label>
					<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][block6]" value="<?php echo esc_attr( $d && isset( $d->block6 ) ? $d->block6 : '' ); ?>" class="form-control">
				</div>
			<?php endif; ?>
			<?php if ( $show_meta ) : ?>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Meta title', 'ai-calculator' ); ?></label>
					<input type="text" name="description[<?php echo esc_attr( (string) $lid ); ?>][meta_title]" value="<?php echo esc_attr( $d && isset( $d->meta_title ) ? $d->meta_title : '' ); ?>" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label"><?php esc_html_e( 'Meta description', 'ai-calculator' ); ?></label>
					<textarea name="description[<?php echo esc_attr( (string) $lid ); ?>][meta_description]" rows="3" class="form-control"><?php echo esc_textarea( $d && isset( $d->meta_description ) ? $d->meta_description : '' ); ?></textarea>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
