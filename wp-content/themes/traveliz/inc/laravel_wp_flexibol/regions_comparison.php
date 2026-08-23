<?php

if (! function_exists('traveliz_laravel_row_s_flexibol_regions_comparison_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_regions_comparison_from_data(array $data): array
    {
        $pick = static function (array $source, array $keys): string {
            foreach ($keys as $k) {
                if (! array_key_exists($k, $source)) {
                    continue;
                }
                $v = $source[$k];
                if (is_string($v) || is_numeric($v)) {
                    return (string) $v;
                }
            }

            return '';
        };

        $row = [
            'acf_fc_layout' => 's_flexibol_regions_comparison',
            's_flexibol_regions_comparison_section_title' => $pick($data, [
                's_flexibol_regions_comparison_section_title',
                'title',
                'heading',
                'section_title',
            ]),
            'comparison_of_regions_dop_text' => $pick($data, [
                'comparison_of_regions_dop_text',
            ]),
            's_flexibol_regions_left_title' => $pick($data, [
                's_flexibol_regions_left_title',
                'left_title',
            ]),
            's_flexibol_regions_left_subtitle' => $pick($data, [
                's_flexibol_regions_left_subtitle',
                'left_subtitle',
            ]),
            's_flexibol_regions_label_weather' => $pick($data, [
                's_flexibol_regions_label_weather',
                'label_weather',
            ]),
            's_flexibol_regions_label_entertainment' => $pick($data, [
                's_flexibol_regions_label_entertainment',
                'label_entertainment',
            ]),
            's_flexibol_regions_label_transport' => $pick($data, [
                's_flexibol_regions_label_transport',
                'label_transport',
            ]),
            's_flexibol_regions_label_kids' => $pick($data, [
                's_flexibol_regions_label_kids',
                'label_kids',
            ]),
            's_flexibol_regions_label_price' => $pick($data, [
                's_flexibol_regions_label_price',
                'label_price',
            ]),
        ];

        $items = $data['items'] ?? $data['s_flexibol_regions_items'] ?? [];
        if (! is_array($items)) {
            $items = [];
        }

        $rows = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $rows[] = [
                's_flexibol_region_city_name' => $pick($item, ['s_flexibol_region_city_name', 'city', 'city_name', 'region']),
                's_flexibol_region_image' => false,
                's_flexibol_region_weather' => $pick($item, ['s_flexibol_region_weather', 'weather']),
                's_flexibol_region_entertainment' => $pick($item, ['s_flexibol_region_entertainment', 'entertainment']),
                's_flexibol_region_transport' => $pick($item, ['s_flexibol_region_transport', 'transport']),
                's_flexibol_region_kids' => $pick($item, ['s_flexibol_region_kids', 'kids']),
                's_flexibol_region_price' => $pick($item, ['s_flexibol_region_price', 'price']),
            ];
        }
        $row['s_flexibol_regions_items'] = $rows;

        return $row;
    }
}
