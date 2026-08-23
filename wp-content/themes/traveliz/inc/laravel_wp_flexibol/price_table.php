<?php

if (! function_exists('traveliz_laravel_price_table_pick_scalar')) {
    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     */
    function traveliz_laravel_price_table_pick_scalar(array $source, array $keys): string
    {
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
    }
}

if (! function_exists('traveliz_laravel_price_table_pick_image')) {
    /**
     * ACF image: array|id|url|false.
     *
     * @param array<string, mixed> $source
     * @param list<string> $keys
     * @return array<string, mixed>|int|string|false
     */
    function traveliz_laravel_price_table_pick_image(array $source, array $keys)
    {
        foreach ($keys as $k) {
            if (! array_key_exists($k, $source)) {
                continue;
            }
            $v = $source[$k];
            if ($v === null || $v === '' || $v === false) {
                return false;
            }
            if (is_array($v) || is_numeric($v) || is_string($v)) {
                return $v;
            }
        }

        return false;
    }
}

if (! function_exists('traveliz_laravel_price_table_item_row_from_data')) {
    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    function traveliz_laravel_price_table_item_row_from_data(array $item): array
    {
        return [
            's_flexibol_price_image_1' => traveliz_laravel_price_table_pick_image($item, [
                's_flexibol_price_image_1',
                'image_1',
            ]),
            's_flexibol_price_title' => traveliz_laravel_price_table_pick_scalar($item, [
                's_flexibol_price_title',
                'title',
                'name',
                'heading',
            ]),
            's_flexibol_price_input' => traveliz_laravel_price_table_pick_scalar($item, [
                's_flexibol_price_input',
                'input',
                'text',
                'description',
            ]),
            's_flexibol_price_image_2' => traveliz_laravel_price_table_pick_image($item, [
                's_flexibol_price_image_2',
                'image_2',
            ]),
            's_flexibol_price_input_2' => traveliz_laravel_price_table_pick_scalar($item, [
                's_flexibol_price_input_2',
                'input_2',
                'subtitle',
            ]),
            's_flexibol_price_item_price' => traveliz_laravel_price_table_pick_scalar($item, [
                's_flexibol_price_item_price',
                'price',
            ]),
            's_flexibol_price_item_night' => traveliz_laravel_price_table_pick_scalar($item, [
                's_flexibol_price_item_night',
                'night',
                'period',
            ]),
        ];
    }
}

if (! function_exists('traveliz_laravel_row_s_flexibol_price_table_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_price_table_from_data(array $data): array
    {
        $itemsRaw = $data['items'] ?? $data['s_flexibol_price_items'] ?? [];
        if (! is_array($itemsRaw)) {
            $itemsRaw = [];
        }

        $items = [];
        foreach ($itemsRaw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $items[] = traveliz_laravel_price_table_item_row_from_data($item);
        }

        return [
            'acf_fc_layout' => 's_flexibol_price_table',
            's_flexibol_price_table_section_title' => traveliz_laravel_price_table_pick_scalar($data, [
                's_flexibol_price_table_section_title',
                'title',
                'section_title',
                'heading',
            ]),
            's_flexibol_price_top_input' => traveliz_laravel_price_table_pick_scalar($data, [
                's_flexibol_price_top_input',
                'top_input',
            ]),
            's_flexibol_price_top_image' => traveliz_laravel_price_table_pick_image($data, [
                's_flexibol_price_top_image',
                'top_image',
            ]),
            's_flexibol_price_items' => $items,
            's_flexibol_price_bottom_block_1' => [
                's_flexibol_price_bb1_top' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb1_top', 'bb1_top']),
                's_flexibol_price_bb1_middle' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb1_middle', 'bb1_middle']),
                's_flexibol_price_bb1_extra' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb1_extra', 'bb1_extra']),
                's_flexibol_price_bb1_button_price' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb1_button_price', 'bb1_price']),
                's_flexibol_price_bb1_button_day' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb1_button_day', 'bb1_day']),
            ],
            's_flexibol_price_bottom_block_2' => [
                's_flexibol_price_bb2_top' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb2_top', 'bb2_top']),
                's_flexibol_price_bb2_middle' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb2_middle', 'bb2_middle']),
                's_flexibol_price_bb2_extra' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb2_extra', 'bb2_extra']),
                's_flexibol_price_bb2_button_price' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb2_button_price', 'bb2_price']),
                's_flexibol_price_bb2_button_day' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb2_button_day', 'bb2_day']),
            ],
            's_flexibol_price_bottom_block_3' => [
                's_flexibol_price_bb3_top' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb3_top', 'bb3_top']),
                's_flexibol_price_bb3_middle' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb3_middle', 'bb3_middle']),
                's_flexibol_price_bb3_extra' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb3_extra', 'bb3_extra']),
                's_flexibol_price_bb3_button_price' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb3_button_price', 'bb3_price']),
                's_flexibol_price_bb3_button_day' => traveliz_laravel_price_table_pick_scalar($data, ['s_flexibol_price_bb3_button_day', 'bb3_day']),
            ],
        ];
    }
}
