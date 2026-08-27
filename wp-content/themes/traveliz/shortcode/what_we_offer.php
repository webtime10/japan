<?php
/**
 * Shortcode [what_we_offer]
 * Renders template-parts/what_we_offer.php
 */

add_shortcode(
	'what_we_offer',
	function () {
		ob_start();
		get_template_part( 'template-parts/what_we_offer' );
		return ob_get_clean();
	}
);
