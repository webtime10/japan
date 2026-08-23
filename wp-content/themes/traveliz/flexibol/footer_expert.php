<?php
/**
 * Flexible Constructor: Footer expert (section.web-expert)
 * Layout: s_flexibol_footer_expert
 */
if ( get_row_layout() !== 's_flexibol_footer_expert' ) {
	return;
}

get_template_part(
	'template-parts/web_expert',
	null,
	array(
		'image' => get_sub_field( 's_flexibol_footer_expert_image' ),
		'title' => (string) get_sub_field( 's_flexibol_footer_expert_title' ),
		'text'  => (string) get_sub_field( 's_flexibol_footer_expert_text' ),
	)
);
