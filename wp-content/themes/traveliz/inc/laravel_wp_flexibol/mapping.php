<?php

if (! function_exists('traveliz_laravel_ai_layout_to_acf_map')) {
    /**
     * Соответствие layout из JSON AI → имя макета ACF flexible (поле acf_fc_layout).
     *
     * @return array<string, string>
     */
    function traveliz_laravel_ai_layout_to_acf_map(): array
    {
        return apply_filters('traveliz_laravel_ai_layout_to_acf', [
            'text_country' => 's_flexibol_country_text',   
            'faq' => 's_flexibol_faq',
            'seasons_line' => 's_flexibol_seasons_line',
            'regions_comparison' => 's_flexibol_regions_comparison',
            'attractions' => 's_flexibol_attractions_slider',
            'landmark' => 's_flexibol_attractions_slider',
            'route_one_day' => 's_flexibol_route_one_day',
            'rout-new' => 's_flexibol_route_one_day',
            'rout_new' => 's_flexibol_route_one_day',
            'price_table' => 's_flexibol_price_table',
            'section_expert_advice_new' => 's_flexibol_advice',
            'expert' => 's_flexibol_expert',
            'active' => 's_flexibol_active_otd',
            'where_to_stay' => 's_flexibol_where_to_stay',
            'parking' => 's_flexibol_parking',
            'tourist_reviews' => 's_flexibol_tourist_reviews',
        ]);
    }
}

if (! function_exists('traveliz_laravel_acf_flexible_field_selector')) {
    /**
     * Ключ родительского поля flexible (имя в админке — s_flexibol_constructor).
     */
    function traveliz_laravel_acf_flexible_field_selector(): string
    {
        return (string) apply_filters('traveliz_laravel_acf_flexible_field_selector', 'field_s_flexibol_constructor');
    }
}
