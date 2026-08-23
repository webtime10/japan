<?php
/*add_action('acf/init', function () {

    if (!function_exists('acf_add_options_page')) return;
    if (!function_exists('pll_languages_list')) return;


    acf_add_options_page([
        'page_title' => 'Theme Settings',
        'menu_title' => 'Theme Settings',
        'menu_slug'  => 'theme-settings',
        'redirect'   => true // Изменил на true, чтобы при наведении меню вылетало вправо
    ]);

    // Страница Header
    acf_add_options_sub_page([
        'page_title'  => 'Header Settings',
        'menu_title'  => 'Header',
        'parent_slug' => 'theme-settings',
        'menu_slug'   => 'header-settings'
    ]);

    // НОВАЯ страница Reviews
    acf_add_options_sub_page([
        'page_title'  => 'Customer Reviews Settings',
        'menu_title'  => 'Reviews',
        'parent_slug' => 'theme-settings',
        'menu_slug'   => 'reviews-settings'
    ]);


    $languages = pll_languages_list(['fields' => 'slug']);


    $header_base_fields = [
        [
            'key' => 'header_title',
            'label' => 'Header Title',
            'name' => 'header_title',
            'type' => 'text',
        ],
        [
            'key' => 'header_phone',
            'label' => 'Header Phone',
            'name' => 'header_phone',
            'type' => 'text',
        ],
    ];

    $header_fields = [];
    foreach ($languages as $lang) {
        $header_fields[] = [
            'key' => 'tab_header_' . $lang,
            'label' => strtoupper($lang),
            'type' => 'tab',
            'placement' => 'top',
        ];
        foreach ($header_base_fields as $field) {
            $header_fields[] = [
                'key' => $field['key'] . '_' . $lang,
                'label' => $field['label'] . ' (' . strtoupper($lang) . ')',
                'name' => $field['name'] . '_' . $lang,
                'type' => $field['type'],
            ];
        }
    }

    acf_add_local_field_group([
        'key' => 'group_header_settings',
        'title' => 'Header Settings',
        'fields' => $header_fields,
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'header-settings']]],
    ]);

    // --- 2. ГРУППА ПОЛЕЙ ДЛЯ REVIEWS (Customer Reviews) ---
    $reviews_fields = [];
    foreach ($languages as $lang) {
        // Вкладка языка
        $reviews_fields[] = [
            'key' => 'tab_reviews_' . $lang,
            'label' => strtoupper($lang),
            'type' => 'tab',
            'placement' => 'top',
        ];

        // НЕ повторитель: общие поля заголовка и кнопки для блока отзывов
        $reviews_fields[] = [
            'key'   => 'reviews_title_' . $lang,
            'label' => 'Reviews Title (' . strtoupper($lang) . ')',
            'name'  => 'reviews_title_' . $lang,
            'type'  => 'text',
        ];

        $reviews_fields[] = [
            'key'   => 'reviews_button_' . $lang,
            'label' => 'Reviews Button Text (' . strtoupper($lang) . ')',
            'name'  => 'reviews_button_' . $lang,
            'type'  => 'text',
        ];

        $reviews_fields[] = [
            'key'   => 'reviews_link_' . $lang,
            'label' => 'Reviews Button Link (' . strtoupper($lang) . ')',
            'name'  => 'reviews_link_' . $lang,
            'type'  => 'text',
        ];

        // Поле повторителя для отзывов (только изображения и подписи)
        $reviews_fields[] = [
            'key' => 'customer_reviews_repeater_' . $lang,
            'label' => 'Customer Reviews List (' . strtoupper($lang) . ')',
            'name' => 'customer_reviews_' . $lang,
            'type' => 'repeater',
            'instructions' => 'Add screenshots of customer reviews and descriptions.',
            'button_label' => 'Add Review',
            'layout' => 'block', // 'block' удобнее для просмотра фото
            'sub_fields' => [
                [
                    'key' => 'review_image_' . $lang,
                    'label' => 'Review Image (Screenshot)',
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ],
                [
                    'key' => 'review_content_' . $lang,
                    'label' => 'Review Text',
                    'name' => 'content',
                    'type' => 'textarea',
                ],
                [
                    'key' => 'review_name_' . $lang,
                    'label' => 'Customer Name',
                    'name' => 'name',
                    'type' => 'text',
                ],
            ],
        ];
    }

    acf_add_local_field_group([
        'key' => 'group_reviews_settings',
        'title' => 'Customer Reviews Settings',
        'fields' => $reviews_fields,
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'reviews-settings']]],
    ]);

});
*/

// Include separate ACF group files (Swiss Experience, Landing, Guarantees, Travelers Stories, Ready, Tourist Reviews, Region).
if ( function_exists( 'acf_add_local_field_group' ) ) {
	require_once get_template_directory() . '/inc/acf/reviews_from _tourists.php';
	require_once get_template_directory() . '/inc/acf/swiss_experience.php';
	require_once get_template_directory() . '/inc/acf/web_expert.php';
	require_once get_template_directory() . '/inc/acf/landing.php';
	require_once get_template_directory() . '/inc/acf/garanty.php';
	require_once get_template_directory() . '/inc/acf/travelers_stories.php';
	require_once get_template_directory() . '/inc/acf/ready.php';
	require_once get_template_directory() . '/inc/acf/region.php';
	require_once get_template_directory() . '/inc/acf/s_flexibol_constructor.php';
	require_once get_template_directory() . '/inc/acf/page.php';
	require_once get_template_directory() . '/inc/acf/footer.php';
}



