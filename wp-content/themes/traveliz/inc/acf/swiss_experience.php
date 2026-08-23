<?php
/**
 * ACF fields for "Swiss experience" block on the front page.
 *
 * Section: <section class="swiss-experience l">
 * Template: front-page.php (variables around lines 82–91).
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

add_action(
	'acf/init',
	function() {
		// Отдельная страница настроек "Swiss Experience" внутри Settings Block (parent_slug = settings).
		if ( function_exists( 'acf_add_options_sub_page' ) ) {
			acf_add_options_sub_page(
				array(
					'page_title'  => 'Swiss Experience',
					'menu_title'  => 'Swiss Experience',
					'parent_slug' => 'settings',
					'menu_slug'   => 'swiss-experience',
					'capability'  => 'edit_posts',
					'redirect'    => false,
				)
			);
		}

		// Группа полей для блока .swiss-experience
		acf_add_local_field_group(
			array(
				'key'                   => 'group_swiss_experience_home',
				'title'                 => 'Swiss Experience',
				'fields'                => array(
					array(
						'key'   => 'field_swiss_title_block2_home',
						'label' => 'Block title (H2)',
						'name'  => 'title_block2_home',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_swiss_title_h3_home',
						'label' => 'Right column title (H3)',
						'name'  => 'title_h3_home',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_swiss_text_h3_block2_home',
						'label' => 'Right column text',
						'name'  => 'text_h3_block2_home',
						'type'  => 'textarea',
					),
					array(
						'key'           => 'field_swiss_forma112',
						'label'         => 'Main image (forma112)',
						'name'          => 'forma112',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'   => 'field_swiss_img_1_digit_home',
						'label' => 'Top left number',
						'name'  => 'img_1_digit_home',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_swiss_img_1_text_home',
						'label' => 'Top left text',
						'name'  => 'img_1_text_home',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_swiss_img_2_digit_home',
						'label' => 'Flag number',
						'name'  => 'img_2_digit_home',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_swiss_img_2_text_home',
						'label' => 'Flag text',
						'name'  => 'img_2_text_home',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_swiss_video_swiss',
						'label' => 'Video URL (YouTube)',
						'name'  => 'video_swiss',
						'type'  => 'text',
						'instructions' => 'Enter YouTube video URL',
					),
					array(
						'key'           => 'field_swiss_mini_img_ytub',
						'label'         => 'YouTube mini image',
						'name'          => 'mini_img_ytub',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'   => 'field_swiss_text_under_the_video',
						'label' => 'Text under the video (around circle)',
						'name'  => 'text_under_the_video',
						'type'  => 'textarea',
					),
					array(
						'key'          => 'field_swiss_slider_img_block2_home',
						'label'        => 'Slider items',
						'name'         => 'slider_img_block2_home',
						'type'         => 'repeater',
						'button_label' => 'Add slide',
						'sub_fields'   => array(
							array(
								'key'           => 'field_swiss_img_block2_slider',
								'label'         => 'Slide image',
								'name'          => 'img_block2_slider',
								'type'          => 'image',
								'return_format' => 'array',
								'preview_size'  => 'medium',
							),
							array(
								'key'   => 'field_swiss_h3_block2_slider',
								'label' => 'Slide title',
								'name'  => 'h3_block2_slider',
								'type'  => 'text',
							),
							array(
								'key'   => 'field_swiss_text_block2_slider',
								'label' => 'Slide text',
								'name'  => 'text_block2_slider',
								'type'  => 'textarea',
							),
						),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'swiss-experience',
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



