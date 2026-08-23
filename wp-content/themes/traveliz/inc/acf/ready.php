<?php
/**
 * ACF fields for "Ready to create a route" block.
 *
 * Options page under Settings Block.
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

add_action(
	'acf/init',
	function() {
		if ( function_exists( 'acf_add_options_sub_page' ) ) {
			acf_add_options_sub_page(
				array(
					'page_title'  => 'Ready to create a route',
					'menu_title'  => 'Ready block',
					'parent_slug' => 'settings',
					'menu_slug'   => 'ready-block',
					'capability'  => 'edit_posts',
					'redirect'    => false,
				)
			);
		}

		acf_add_local_field_group(
			array(
				'key'    => 'group_ready_block',
				'title'  => 'Ready to create a route',
				'fields' => array(
					array(
						'key'   => 'field_ready_title',
						'label' => 'Block title',
						'name'  => 'ready_title',
						'type'  => 'text',
					),
					array(
						'key'           => 'field_ready_background_image',
						'label'         => 'Background image (into-ready)',
						'name'          => 'ready_background_image',
						'type'          => 'image',
						'instructions'  => 'Фон блока .into-ready на лендинге. Если пусто — фон не выводится.',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
					),
					array(
						'key'           => 'field_ready_decor_image',
						'label'         => 'Image inside block (into-ready)',
						'name'          => 'ready_decor_image',
						'type'          => 'image',
						'instructions'  => 'Декоративная картинка внутри .into-ready. Если пусто — картинка не выводится.',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
					),

					// Button 1.
					array(
						'key'   => 'field_ready_button_1_text',
						'label' => 'Button 1 - Text',
						'name'  => 'ready_button_1_text',
						'type'  => 'text',
					),
					array(
						'key'       => 'field_ready_button_1_type',
						'label'     => 'Button 1 - Type',
						'name'      => 'ready_button_1_type',
						'type'      => 'radio',
						'choices'   => array(
							'link'  => 'Link',
							'popup' => 'Popup',
						),
						'default_value' => 'link',
						'layout'        => 'horizontal',
					),
					array(
						'key'               => 'field_ready_button_1_link',
						'label'             => 'Button 1 - Link',
						'name'              => 'ready_button_1_link',
						'type'              => 'text',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_ready_button_1_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),
					),

					// Button 2.
					array(
						'key'   => 'field_ready_button_2_text',
						'label' => 'Button 2 - Text',
						'name'  => 'ready_button_2_text',
						'type'  => 'text',
					),
					array(
						'key'       => 'field_ready_button_2_type',
						'label'     => 'Button 2 - Type',
						'name'      => 'ready_button_2_type',
						'type'      => 'radio',
						'choices'   => array(
							'link'  => 'Link',
							'popup' => 'Popup',
						),
						'default_value' => 'link',
						'layout'        => 'horizontal',
					),
					array(
						'key'               => 'field_ready_button_2_link',
						'label'             => 'Button 2 - Link',
						'name'              => 'ready_button_2_link',
						'type'              => 'text',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_ready_button_2_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'ready-block',
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


