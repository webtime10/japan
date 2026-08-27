<?php
/**
 * Flexible: s_flexibol_what_you_will_get — «Что вы получите / מה תקבלו» (из общих)
 * → template-parts/what_you_will_get.php
 */

if ( get_row_layout() !== 's_flexibol_what_you_will_get' ) {
	return;
}

$show = get_sub_field( 'what_you_will_get_show' );
if ( null === $show ) {
	$show = true;
}
if ( ! $show ) {
	return;
}

get_template_part(
	'template-parts/what_you_will_get',
	null,
	array( 'omit_buttons' => true )
);
