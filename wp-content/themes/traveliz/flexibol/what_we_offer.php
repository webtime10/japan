<?php
/**
 * Flexible layout: s_flexibol_what_we_offer (из общих)
 * → template-parts/what_we_offer.php (Options)
 */

if ( get_row_layout() !== 's_flexibol_what_we_offer' ) {
	return;
}

$show = get_sub_field( 'what_we_offer_show' );
if ( null === $show ) {
	$show = true;
}
if ( ! $show ) {
	return;
}

get_template_part( 'template-parts/what_we_offer' );
