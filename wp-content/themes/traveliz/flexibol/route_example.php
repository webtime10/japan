<?php
/**
 * Flexible: s_flexibol_route_example — «пример маршрута (общее)»
 * → template-parts/route_slider_3d.php (Options)
 */

if ( get_row_layout() !== 's_flexibol_route_example' ) {
	return;
}

$show = get_sub_field( 'route_example_show' );
if ( null === $show ) {
	$show = true;
}
if ( ! $show ) {
	return;
}

get_template_part( 'template-parts/route_slider_3d' );
