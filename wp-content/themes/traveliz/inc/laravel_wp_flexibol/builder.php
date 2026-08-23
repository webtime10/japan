<?php

if (! function_exists('traveliz_laravel_build_flexible_rows_from_ai')) {
    /**
     * @param array<string, mixed> $fields
     * @return list<array<string, mixed>>
     */
    function traveliz_laravel_build_flexible_rows_from_ai(array $fields): array
    {
        $rows = [];
        $map = traveliz_laravel_ai_layout_to_acf_map();

        foreach ($fields as $ai_key => $block_value) {
            if (! is_array($block_value)) {
                continue;
            }
            $sections = $block_value['sections'] ?? $block_value['blocks'] ?? null;
            if (! is_array($sections)) {
                if (isset($block_value['layout']) || isset($block_value['data']) || isset($block_value['title']) || isset($block_value['text'])) {
                    $sections = [$block_value];
                } else {
                    continue;
                }
            }

            foreach ($sections as $section) {
                if (! is_array($section)) {
                    continue;
                }
                $layout = isset($section['layout']) ? (string) $section['layout'] : '';
                $data = [];
                if (isset($section['data']) && is_array($section['data'])) {
                    $data = $section['data'];
                } elseif ($layout !== '' && $section !== []) {
                    $data = $section;
                    unset($data['layout']);
                }

                $row = traveliz_laravel_map_ai_section_to_acf_row($layout, $data, $map);
                if ($row !== null) {
                    $rows[] = $row;
                } else {
                    traveliz_laravel_api_log('FLEX_SECTION_SKIPPED', [
                        'ai_field_hint' => is_string($ai_key) ? $ai_key : '',
                        'layout' => $layout,
                        'data_keys' => is_array($data) ? array_keys($data) : [],
                    ], true);
                }
            }
        }

        return apply_filters('traveliz_laravel_flexible_rows', $rows, $fields);
    }
}

if (! function_exists('traveliz_laravel_map_ai_section_to_acf_row')) {
    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $map
     * @return array<string, mixed>|null
     */
    function traveliz_laravel_map_ai_section_to_acf_row(string $layout, array $data, array $map): ?array
    {
        $layout_key = strtolower(trim((string) $layout));

        if ($layout_key === 's_flexibol_country_text') {
            [$t, $html] = traveliz_laravel_extract_title_and_html_from_data($data);
            return [
                'acf_fc_layout' => 's_flexibol_country_text',
                's_flexibol_title' => $t,
                's_flexibol_text' => $html,
                's_flexibol_image' => false,
                's_flexibol_text_2' => isset($data['text_2']) && is_scalar($data['text_2']) ? (string) $data['text_2'] : '',
            ];
        }
        if ($layout_key === 's_flexibol_faq') {
            $faq_row = traveliz_laravel_row_s_flexibol_faq_from_data($data);
            if ($faq_row !== null) {
                return $faq_row;
            }
        }
        if ($layout_key === 's_flexibol_seasons_line') {
            return traveliz_laravel_row_s_flexibol_seasons_line_from_data($data);
        }
        if ($layout_key === 's_flexibol_regions_comparison') {
            return traveliz_laravel_row_s_flexibol_regions_comparison_from_data($data);
        }
        if ($layout_key === 's_flexibol_attractions_slider') {
            return traveliz_laravel_row_s_flexibol_attractions_slider_from_data($data);
        }
        if ($layout_key === 's_flexibol_route_one_day') {
            return traveliz_laravel_row_s_flexibol_route_one_day_from_data($data);
        }
        if ($layout_key === 's_flexibol_price_table') {
            return traveliz_laravel_row_s_flexibol_price_table_from_data($data);
        }
        if ($layout_key === 's_flexibol_expert') {
            return traveliz_laravel_row_s_flexibol_expert_from_data($data);
        }
        if ($layout_key === 's_flexibol_where_to_stay') {
            return traveliz_laravel_row_s_flexibol_where_to_stay_from_data($data);
        }
        if ($layout_key === 's_flexibol_parking') {
            return traveliz_laravel_row_s_flexibol_parking_from_data($data);
        }
        if ($layout_key === 's_flexibol_tourist_reviews') {
            return traveliz_laravel_row_s_flexibol_tourist_reviews_from_data($data);
        }
        if ($layout_key === 's_flexibol_active_otd') {
            return traveliz_laravel_row_s_flexibol_active_otd_from_data($data);
        }
        if ($layout_key === 's_flexibol_advice') {
            return traveliz_laravel_row_s_flexibol_advice_from_data($data);
        }
        if ($layout_key === 'section_expert_advice_new') {
            return traveliz_laravel_row_section_expert_advice_new_from_data($data);
        }

        $passthrough = traveliz_laravel_row_from_acf_layout_passthrough($layout_key, $data);
        if ($passthrough !== null && count($passthrough) > 1) {
            return $passthrough;
        }

        if ($layout_key !== '' && isset($map[$layout_key])) {
            $acf_layout = $map[$layout_key];
            if ($acf_layout === 's_flexibol_country_text') {
                [$t, $html] = traveliz_laravel_extract_title_and_html_from_data($data);
                return [
                    'acf_fc_layout' => $acf_layout,
                    's_flexibol_title' => $t,
                    's_flexibol_text' => $html,
                    's_flexibol_image' => false,
                    's_flexibol_text_2' => isset($data['text_2']) && is_scalar($data['text_2']) ? (string) $data['text_2'] : '',
                ];
            }
            if ($acf_layout === 's_flexibol_map') {
                return ['acf_fc_layout' => $acf_layout];
            }
            if ($acf_layout === 's_flexibol_faq') {
                $faq_row = traveliz_laravel_row_s_flexibol_faq_from_data($data);
                if ($faq_row !== null) {
                    return $faq_row;
                }
            }
            if ($acf_layout === 's_flexibol_seasons_line') {
                return traveliz_laravel_row_s_flexibol_seasons_line_from_data($data);
            }
            if ($acf_layout === 's_flexibol_regions_comparison') {
                return traveliz_laravel_row_s_flexibol_regions_comparison_from_data($data);
            }
            if ($acf_layout === 's_flexibol_attractions_slider') {
                return traveliz_laravel_row_s_flexibol_attractions_slider_from_data($data);
            }
            if ($acf_layout === 's_flexibol_route_one_day') {
                return traveliz_laravel_row_s_flexibol_route_one_day_from_data($data);
            }
            if ($acf_layout === 's_flexibol_price_table') {
                return traveliz_laravel_row_s_flexibol_price_table_from_data($data);
            }
            if ($acf_layout === 's_flexibol_expert') {
                return traveliz_laravel_row_s_flexibol_expert_from_data($data);
            }
            if ($acf_layout === 's_flexibol_where_to_stay') {
                return traveliz_laravel_row_s_flexibol_where_to_stay_from_data($data);
            }
            if ($acf_layout === 's_flexibol_parking') {
                return traveliz_laravel_row_s_flexibol_parking_from_data($data);
            }
            if ($acf_layout === 's_flexibol_tourist_reviews') {
                return traveliz_laravel_row_s_flexibol_tourist_reviews_from_data($data);
            }
            if ($acf_layout === 's_flexibol_active_otd') {
                return traveliz_laravel_row_s_flexibol_active_otd_from_data($data);
            }
            if ($acf_layout === 's_flexibol_advice') {
                return traveliz_laravel_row_s_flexibol_advice_from_data($data);
            }
        }

        return null;
    }
}
