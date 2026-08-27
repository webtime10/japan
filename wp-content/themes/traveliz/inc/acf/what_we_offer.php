<?php
/**
 * ACF: «Что мы предлагаем» / What we offer
 * Макет как у what_you_will_get (круги + заголовок + текст).
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

add_action(
	'acf/init',
	function () {
		if ( function_exists( 'acf_add_options_sub_page' ) ) {
			acf_add_options_sub_page(
				array(
					'page_title'  => 'What we offer',
					'menu_title'  => 'What we offer',
					'parent_slug' => 'settings',
					'menu_slug'   => 'what-we-offer',
					'capability'  => 'edit_posts',
					'redirect'    => false,
				)
			);
		}

		acf_add_local_field_group(
			array(
				'key'    => 'group_what_we_offer',
				'title'  => 'What we offer',
				'fields' => array(
					array(
						'key'   => 'field_title_what_we_offer',
						'label' => 'Section title (H2)',
						'name'  => 'title_what_we_offer',
						'type'  => 'text',
					),
					array(
						'key'          => 'field_item_what_we_offer',
						'label'        => 'Cards',
						'name'         => 'item_what_we_offer',
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => 'Add card',
						'sub_fields'   => array(
							array(
								'key'           => 'field_item_what_we_offer_img',
								'label'         => 'Image',
								'name'          => 'item_what_we_offer_img',
								'type'          => 'image',
								'return_format' => 'array',
								'preview_size'  => 'medium',
								'library'       => 'all',
							),
							array(
								'key'   => 'field_item_what_we_offer_title',
								'label' => 'Card title',
								'name'  => 'item_what_we_offer_title',
								'type'  => 'text',
							),
							array(
								'key'   => 'field_item_what_we_offer_text',
								'label' => 'Card text',
								'name'  => 'item_what_we_offer_text',
								'type'  => 'wysiwyg',
								'tabs'  => 'all',
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'what-we-offer',
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
