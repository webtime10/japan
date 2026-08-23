<?php
/**
 * Flexible layout: s_flexibol_custom_reviews
 * Renders shared reviews block from template-parts/reviews.
 */

if ( get_row_layout() !== 's_flexibol_custom_reviews' ) {
	return;
}

$show_reviews = get_sub_field( 'custom_reviews' );

if ( $show_reviews === null ) {
	$show_reviews = true;
}

if ( ! $show_reviews ) {
	return;
}

get_template_part( 'template-parts/reviews' );
