<?php
/**
 * Shortcode [web_expert] — block template-parts/web_expert.php
 */

add_shortcode(
	'web_expert',
	function( $atts ) {
		ob_start();
		get_template_part( 'template-parts/web_expert' );
		return ob_get_clean();
	}
);
