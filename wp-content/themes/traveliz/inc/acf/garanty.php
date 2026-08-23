<?php
/**
 * ACF fields for "Guarantees and Trust" block
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
					'page_title'  => 'Guarantees and Trust',
					'menu_title'  => 'Guarantees & Trust',
					'parent_slug' => 'settings',
					'menu_slug'   => 'guarantees-trust',
					'capability'  => 'edit_posts',
					'redirect'    => false,
				)
			);
		}

		acf_add_local_field_group(
			array(
				'key'    => 'group_garanty_trust',
				'title'  => 'Guarantees and Trust',
				'fields' => array(
					array(
						'key'   => 'field__garanty_title',
						'label' => 'Section title',
						'name'  => '_garanty_title',
						'type'  => 'text',
					),
					array(
						'key'   => 'field__garanty_subtitle',
						'label' => 'Section subtitle',
						'name'  => '_garanty_subtitle',
						'type'  => 'text',
					),

					// Card 1.
					array(
						'key'   => 'field__garanty_card1_title',
						'label' => 'Card 1 - Title',
						'name'  => '_garanty_card1_title',
						'type'  => 'text',
					),
					array(
						'key'   => 'field__garanty_card1_text',
						'label' => 'Card 1 - Text',
						'name'  => '_garanty_card1_text',
						'type'  => 'textarea',
					),

					// Card 2.
					array(
						'key'   => 'field__garanty_card2_title',
						'label' => 'Card 2 - Title',
						'name'  => '_garanty_card2_title',
						'type'  => 'text',
					),
					array(
						'key'   => 'field__garanty_card2_text',
						'label' => 'Card 2 - Text',
						'name'  => '_garanty_card2_text',
						'type'  => 'textarea',
					),

					// Card 3.
					array(
						'key'   => 'field__garanty_card3_title',
						'label' => 'Card 3 - Title',
						'name'  => '_garanty_card3_title',
						'type'  => 'text',
					),
					array(
						'key'   => 'field__garanty_card3_text',
						'label' => 'Card 3 - Text',
						'name'  => '_garanty_card3_text',
						'type'  => 'textarea',
					),

					// Trust text under cards.
					array(
						'key'   => 'field__garanty_trust_text',
						'label' => 'Trust text under cards',
						'name'  => '_garanty_trust_text',
						'type'  => 'textarea',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'guarantees-trust',
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


