<?php
/**
 * ACF fields for Landing Page
 *
 * Template: page-lending.php
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

add_action(
	'acf/init',
	function() {
		// Группа полей для страницы лендинга.
		acf_add_local_field_group(
			array(
				'key'    => 'group_landing_page',
				'title'  => 'Landing Page Settings',
				'fields' => array(
					// Hero section.
					array(
						'key'           => 'header_background',
						'label'         => 'Header Background',
						'name'          => 'header_background',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'   => 'field_landing_hero_title',
						'label' => 'Title (H1)',
						'name'  => 'hero_title',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_landing_hero_subtitle',
						'label' => 'Subtitle (H2)',
						'name'  => 'hero_subtitle',
						'type'  => 'text',
					),

					// Button 1.
					array(
						'key'   => 'field_landing_button_1_text',
						'label' => 'Button 1 - Text',
						'name'  => 'button_1_text',
						'type'  => 'text',
					),
					array(
						'key'       => 'field_landing_button_1_type',
						'label'     => 'Button 1 - Type',
						'name'      => 'button_1_type',
						'type'      => 'radio',
						'choices'   => array(
							'link'  => 'Link',
							'popup' => 'Popup',
						),
						'default_value' => 'link',
						'layout'        => 'horizontal',
					),
					array(
						'key'   => 'field_landing_button_1_link',
						'label' => 'Button 1 - Link',
						'name'  => 'button_1_link',
						'type'  => 'text',
					),

					// Button 2.
					array(
						'key'   => 'field_landing_button_2_text',
						'label' => 'Button 2 - Text',
						'name'  => 'button_2_text',
						'type'  => 'text',
					),
					array(
						'key'       => 'field_landing_button_2_type',
						'label'     => 'Button 2 - Type',
						'name'      => 'button_2_type',
						'type'      => 'radio',
						'choices'   => array(
							'link'  => 'Link',
							'popup' => 'Popup',
						),
						'default_value' => 'link',
						'layout'        => 'horizontal',
					),
					array(
						'key'   => 'field_landing_button_2_link',
						'label' => 'Button 2 - Link',
						'name'  => 'button_2_link',
						'type'  => 'text',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-lending.php',
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


