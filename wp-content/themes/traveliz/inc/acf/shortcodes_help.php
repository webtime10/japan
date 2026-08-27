<?php
/**
 * ACF Options: справочник шорткодов темы.
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
					'page_title'  => 'Шорткоды',
					'menu_title'  => 'Шорткоды',
					'parent_slug' => 'settings',
					'menu_slug'   => 'theme-shortcodes',
					'capability'  => 'edit_posts',
					'redirect'    => false,
				)
			);
		}

		$message  = '<p>Шорткоды для контента / редактора. Общие блоки удобнее добавлять в <strong>Flexible Constructor</strong> с пометкой «(из общих)».</p>';
		$message .= '<table class="widefat striped" style="max-width:720px">';
		$message .= '<thead><tr><th>Шорткод</th><th>Блок</th><th>Где править контент</th></tr></thead><tbody>';

		$rows = array(
			array(
				'code'  => '[short_reviews]',
				'block' => 'Отзывы (также flexible: общие отзывы)',
				'where' => 'Settings → Tourist Reviews / Reviews',
			),
			array(
				'code'  => '[web_expert]',
				'block' => 'Web Expert (эксперт)',
				'where' => 'Settings → Web Expert',
			),
			array(
				'code'  => '[what_we_offer]',
				'block' => 'Что мы предлагаем (также flexible)',
				'where' => 'Settings → What we offer',
			),
			array(
				'code'  => '— (только flexible)',
				'block' => 'Как мы работаем (из общих)',
				'where' => 'Settings → How it works · layout в конструкторе',
			),
			array(
				'code'  => '[ai_weather_calculator]',
				'block' => 'Калькулятор погоды',
				'where' => 'Плагин AI Calculator',
			),
			array(
				'code'  => '[ai_budget_calculator]',
				'block' => 'Калькулятор бюджета',
				'where' => 'Плагин AI Calculator',
			),
			array(
				'code'  => '[ai_ideal_region_calculator]',
				'block' => 'Идеальный регион',
				'where' => 'Плагин AI Calculator',
			),
			array(
				'code'  => '[family_comfort_calculator]<br>или [ai_family_comfort_calculator]',
				'block' => 'Семейный комфорт',
				'where' => 'Плагин AI Calculator',
			),
			array(
				'code'  => '[ai_chat_calculator]<br>или [chat_calculator]',
				'block' => 'Чат-калькулятор',
				'where' => 'Плагин AI Calculator',
			),
		);

		foreach ( $rows as $row ) {
			$message .= '<tr>';
			$message .= '<td><code>' . $row['code'] . '</code></td>';
			$message .= '<td>' . esc_html( $row['block'] ) . '</td>';
			$message .= '<td>' . esc_html( $row['where'] ) . '</td>';
			$message .= '</tr>';
		}

		$message .= '</tbody></table>';

		acf_add_local_field_group(
			array(
				'key'    => 'group_theme_shortcodes_help',
				'title'  => 'Шорткоды',
				'fields' => array(
					array(
						'key'     => 'field_theme_shortcodes_list',
						'label'   => 'Список шорткодов',
						'name'    => 'theme_shortcodes_list',
						'type'    => 'message',
						'message' => $message,
						'new_lines' => '',
						'esc_html'  => 0,
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'theme-shortcodes',
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
