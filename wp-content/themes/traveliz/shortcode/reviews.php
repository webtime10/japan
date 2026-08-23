<?php
/**
 * Shortcode callback for [short_reviews]
 *
 * Renders the same block as template-parts/reviews.php.
 */

add_shortcode( 'short_reviews', function( $atts ) {
	ob_start();
	get_template_part( 'template-parts/reviews' );
	return ob_get_clean();
} );

