<?php
/**
 * Flexible Constructor: Editor block
 *
 * Renders layout "s_flexibol_editor" from ACF.
 * - field name: s_flexibol_editor (wysiwyg)
 */
if ( get_row_layout() === 's_flexibol_editor' ) :
	$editor_html = get_sub_field( 's_flexibol_editor' );
	if ( ! empty( $editor_html ) ) :
		?>
		<section class="editor">
			<div class="container-4">
				<div class="into-editor">
					<div class="flexibol-editor">
						<?php echo wp_kses_post( $editor_html ); ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	endif;
endif;
?>