<?php
/**
 * Flexible layout: s_flexibol_how_it_works — «Как мы работаем» (из общих)
 * → template-parts/how_it_works.php (Options + CF7)
 */

if ( get_row_layout() !== 's_flexibol_how_it_works' ) {
	return;
}

$show = get_sub_field( 'how_it_works_show' );
if ( null === $show ) {
	$show = true;
}
if ( ! $show ) {
	return;
}

get_template_part( 'template-parts/how_it_works' );
