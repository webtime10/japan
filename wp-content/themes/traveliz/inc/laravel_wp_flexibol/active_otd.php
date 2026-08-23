<?php

if (! function_exists('traveliz_laravel_active_otd_pick_scalar')) {
    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     */
    function traveliz_laravel_active_otd_pick_scalar(array $source, array $keys): string
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

if (! function_exists('traveliz_laravel_active_otd_pick_image')) {
    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     * @return array<string, mixed>|int|string|false
     */
    function traveliz_laravel_active_otd_pick_image(array $source, array $keys)
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

if (! function_exists('traveliz_laravel_row_s_flexibol_active_otd_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_active_otd_from_data(array $data): array
    {
        $itemsRaw = $data['items'] ?? $data['cards'] ?? $data['slides'] ?? $data['s_flexibol_active_otd_items'] ?? [];
        if (! is_array($itemsRaw)) {
            $itemsRaw = [];
        }

        $items = [];
        foreach ($itemsRaw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $items[] = [
                's_flexibol_active_otd_item_image' => traveliz_laravel_active_otd_pick_image($item, [
                    's_flexibol_active_otd_item_image',
                    'image',
                    'photo',
                ]),
                's_flexibol_active_otd_item_title' => traveliz_laravel_active_otd_pick_scalar($item, [
                    's_flexibol_active_otd_item_title',
                    'title',
                    'name',
                    'heading',
                ]),
                's_flexibol_active_otd_item_text' => traveliz_laravel_active_otd_pick_scalar($item, [
                    's_flexibol_active_otd_item_text',
                    'text',
                    'description',
                    'body',
                ]),
            ];
        }

        return [
            'acf_fc_layout' => 's_flexibol_active_otd',
            's_flexibol_active_otd_section_title' => traveliz_laravel_active_otd_pick_scalar($data, [
                's_flexibol_active_otd_section_title',
                'title',
                'heading',
            ]),
            's_flexibol_active_otd_items' => $items,
            's_flexibol_active_otd_bottom_text' => traveliz_laravel_active_otd_pick_scalar($data, [
                's_flexibol_active_otd_bottom_text',
                'bottom_text',
            ]),
        ];
    }
}
