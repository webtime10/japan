<?php

// Options for "Tourist Reviews" block: separate settings page under Settings Block.
add_action(
	'acf/init',
	function() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		// Create subpage "Tourist Reviews" under existing Options Page with slug = settings.
		if ( function_exists( 'acf_add_options_sub_page' ) ) {
			acf_add_options_sub_page(
				array(
					'page_title'  => 'Tourist Reviews',
					'menu_title'  => 'Tourist Reviews',
					'parent_slug' => 'settings',
					'menu_slug'   => 'tourist-reviews',
					'capability'  => 'edit_posts',
					'redirect'    => false,
				)
			);
		}

		// Field group for this options page.
		acf_add_local_field_group(
			array(
				'key'    => 'group_tourist_reviews_settings',
				'title'  => 'Tourist Reviews',
				'fields' => array(
					array(
						'key'   => 'field_tourist_reviews_title',
						'label' => 'Block title',
						'name'  => 'tourist_reviews_title',
						'type'  => 'text',
					),
					array(
						'key'           => 'field_tourist_reviews_background_image',
						'label'         => 'Фотка бекграунда',
						'name'          => 'tourist_reviews_background_image',
						'type'          => 'image',
						'instructions'  => 'Фон для секции reviews-section google treveler reviews2.',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
					),
					array(
						'key'   => 'field_tourist_reviews_subtitle',
						'label' => 'Block subtitle',
						'name'  => 'tourist_reviews_subtitle',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_tourist_reviews_button',
						'label' => 'Button text',
						'name'  => 'tourist_reviews_button',
						'type'  => 'text',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'tourist-reviews',
						),
					),
				),
			)
		);
	}
);


