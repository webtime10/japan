<?php
/**
 * short_editor block
 * ACF layout: s_flexibol_short_editor
 * Field: s_flexibol_short_editor (textarea)
 */

if ( get_row_layout() !== 's_flexibol_short_editor' ) {
	return;
}

$code_html = get_sub_field( 's_flexibol_short_editor' );
if ( ! empty( $code_html ) ) {
	// Output only the user-provided code (sanitized for safety).
	echo wp_kses_post( $code_html );
}
