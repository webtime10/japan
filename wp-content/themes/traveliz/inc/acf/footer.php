<?php
/**
 * Footer: дополнительные поля через код (страница Footer в ACF UI не трогаем).
 *
 * @package traveliz
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

add_action(
	'acf/init',
	function () {
		acf_add_local_field_group(
			array(
				'key'    => 'group_footer_ua_fields',
				'title'  => 'Footer (поля из кода)',
				'fields' => array(
					array(
						'key'   => 'field_ua_footer_ratings_heading',
						'label' => 'Заголовок рейтинга',
						'name'  => 'ua_footer_ratings_heading',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_ua_footer_reviews_label',
						'label' => 'Сколько отзывов (подпись к числу)',
						'name'  => 'ua_footer_reviews_label',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_ua_footer_sitemap_title',
						'label' => 'Карта сайта — заголовок',
						'name'  => 'ua_footer_sitemap_title',
						'type'  => 'text',
					),
					array(
						'key'        => 'field_ua_footer_sitemap_links',
						'label'      => 'Карта сайта — ссылки',
						'name'       => 'ua_footer_sitemap_links',
						'type'       => 'repeater',
						'layout'     => 'table',
						'button_label' => 'Добавить ссылку',
						'sub_fields' => array(
							array(
								'key'   => 'field_ua_footer_sitemap_link_title',
								'label' => 'Название',
								'name'  => 'ua_footer_sitemap_link_title',
								'type'  => 'text',
							),
							array(
								'key'   => 'field_ua_footer_sitemap_link_url',
								'label' => 'Ссылка',
								'name'  => 'ua_footer_sitemap_link_url',
								'type'  => 'text',
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'footer',
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
