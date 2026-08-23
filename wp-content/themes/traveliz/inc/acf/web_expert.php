<?php
/**
 * ACF fields for "Web Expert" block (shortcode [web_expert]).
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
					'page_title'  => 'Web Expert',
					'menu_title'  => 'Web Expert',
					'parent_slug' => 'settings',
					'menu_slug'   => 'web-expert',
					'capability'  => 'edit_posts',
					'redirect'    => false,
				)
			);
		}

		acf_add_local_field_group(
			array(
				'key'    => 'group_web_expert',
				'title'  => 'Web Expert',
				'fields' => array(
					array(
						'key'           => 'field_web_expert_image',
						'label'         => 'Expert photo',
						'name'          => 'web_expert_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'   => 'field_web_expert_title',
						'label' => 'Name / title',
						'name'  => 'web_expert_title',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_web_expert_text',
						'label' => 'Text',
						'name'  => 'web_expert_text',
						'type'  => 'textarea',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'web-expert',
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
