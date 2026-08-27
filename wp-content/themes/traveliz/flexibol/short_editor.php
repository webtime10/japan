<?php
/**
 * short_editor block
 * ACF layout: s_flexibol_short_editor
 *
 * Без wp_kses_post — он вырезает <form> у Contact Form 7.
 */

if ( get_row_layout() !== 's_flexibol_short_editor' ) {
	return;
}

$removed = array();
foreach ( array( 'wpautop', 'shortcode_unautop', 'wp_filter_content_tags' ) as $cb ) {
	if ( has_filter( 'acf_the_content', $cb ) ) {
		remove_filter( 'acf_the_content', $cb );
		$removed[] = $cb;
	}
}

$code_html = get_sub_field( 's_flexibol_short_editor' );

foreach ( $removed as $cb ) {
	if ( 'wp_filter_content_tags' === $cb ) {
		add_filter( 'acf_the_content', 'wp_filter_content_tags', 12 );
	} else {
		add_filter( 'acf_the_content', $cb );
	}
}

if ( empty( $code_html ) || ! is_string( $code_html ) ) {
	return;
}

$code_html = do_shortcode( $code_html );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML/shortcodes from admin (incl. CF7).
echo $code_html;
