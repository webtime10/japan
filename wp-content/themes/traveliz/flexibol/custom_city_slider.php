<?php
/**
 * Flexible layout: s_flexibol_custom_city_slider
 * Renders shared block from template-parts/slider_city.
 */

if ( get_row_layout() !== 's_flexibol_custom_city_slider' ) {
	return;
}

$show_city_slider = get_sub_field( 'custom_city_slider' );

if ( $show_city_slider === null ) {
	$show_city_slider = true;
}

if ( ! $show_city_slider ) {
	return;
}

get_template_part( 'template-parts/slider_city' );
