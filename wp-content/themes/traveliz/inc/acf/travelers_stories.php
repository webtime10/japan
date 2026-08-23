<?php
/**
 * ACF fields for "Travelers Stories"
 *
 * 1) Options page: block title/subtitle.
 * 2) Fields for custom post type `travelers_stories`.
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

add_action(
	'acf/init',
	function() {
		// Options page for block texts.
		if ( function_exists( 'acf_add_options_sub_page' ) ) {
			acf_add_options_sub_page(
				array(
					'page_title'  => 'Travelers Stories',
					'menu_title'  => 'Travelers Stories',
					'parent_slug' => 'settings',
					'menu_slug'   => 'travelers-stories',
					'capability'  => 'edit_posts',
					'redirect'    => false,
				)
			);
		}

		// Block fields (title + subtitle) on options page.
		acf_add_local_field_group(
			array(
				'key'    => 'group_travelers_stories_options',
				'title'  => 'Travelers Stories – Block Settings',
				'fields' => array(
					array(
						'key'   => 'field_travelers_stories_title',
						'label' => 'Block title',
						'name'  => 'travelers_stories_title',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_travelers_stories_subtitle',
						'label' => 'Block subtitle',
						'name'  => 'travelers_stories_subtitle',
						'type'  => 'text',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'travelers-stories',
						),
					),
				),
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
			)
		);

		// Fields for each Travelers Story post.
		acf_add_local_field_group(
			array(
				'key'    => 'group_travelers_stories_post',
				'title'  => 'Traveler Story Fields',
				'fields' => array(
					array(
						'key'           => 'field_travelers_story_image',
						'label'         => 'Story image',
						'name'          => 'story_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'   => 'field_travelers_story_route',
						'label' => 'Route',
						'name'  => 'route',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_travelers_story_link_text',
						'label' => 'Button text',
						'name'  => 'link_text',
						'type'  => 'text',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'travelers_stories',
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


