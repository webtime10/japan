<?php
/**
 * ACF fields for Region Page
 *
 * Template: page-region.php
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

add_action(
	'acf/init',
	function() {
		// Группа полей для страницы Region Page.
		acf_add_local_field_group(
			array(
				'key'    => 'group_region_page',
				'title'  => 'Region Page Settings',
				'fields' => array(
					array(
						'key'           => 'field_region_header_background',
						'label'         => 'Header Background',
						'name'          => 'header_background',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-region.php',
						),
					),
				),
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
			)
		);
	}
);

