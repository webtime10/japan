<?php
/**
 * PageTop fields (ACF) - used to configure the top section on standard pages.
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

add_action(
	'acf/init',
	function() {
		acf_add_local_field_group(
			[
				'key'                   => 'group_ph_pagelink_top',
				'title'                 => 'PageTop',
				'fields'                => [
					[
						'key'           => 'field_ph_header_background',
						'label'         => 'header_background',
						'name'          => 'header_background',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
					],
					[
						'key'           => 'field_ph_shadow_under_letters',
						'label'         => 'Shadow under the letters',
						'name'          => 'shadow_under_letters',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
					],
					[
						'key'   => 'field_ph_title',
						'label' => 'Title 1',
						'name'  => 'page_top_title_1',
						'type'  => 'text',
					],
					[
						'key'   => 'field_ph_title_2',
						'label' => 'Title 2',
						'name'  => 'page_top_title_2',
						'type'  => 'text',
					],
					[
						'key'   => 'field_ph_subtitle',
						'label' => 'Subtitle',
						'name'  => 'page_top_subtitle',
						'type'  => 'text',
					],
					[
						'key'   => 'field_ph_button_text',
						'label' => 'Button - Text',
						'name'  => 'page_top_button_text',
						'type'  => 'text',
					],
					[
						'key'       => 'field_ph_button_type',
						'label'     => 'Button - Type',
						'name'      => 'page_top_button_type',
						'type'      => 'radio',
						'choices'   => [
							'link'  => 'Link',
							'popup' => 'Popup',
						],
						'default_value' => 'link',
						'layout'        => 'horizontal',
					],
					[
						'key'   => 'field_ph_button_link',
						'label' => 'Button - Link',
						'name'  => 'page_top_button_link',
						'type'  => 'text',
					],
				],
				'location'              => [
					[
						[
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'page',
						],
					],
				],
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			]
		);
	}
);


