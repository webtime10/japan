<?php
/**
 * Flexible layout: s_flexibol_custom_regions
 * Renders shared block from template-parts/slider_our_experience.
 */

if ( get_row_layout() !== 's_flexibol_custom_regions' ) {
	return;
}

$show_regions = get_sub_field( 'custom_regions' );

if ( $show_regions === null ) {
	$show_regions = true;
}

if ( ! $show_regions ) {
	return;
}

get_template_part( 'template-parts/slider_our_experience' );
