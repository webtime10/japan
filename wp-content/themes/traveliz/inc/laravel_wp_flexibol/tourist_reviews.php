<?php

if (! function_exists('traveliz_laravel_tourist_reviews_pick_scalar')) {
    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     */
    function traveliz_laravel_tourist_reviews_pick_scalar(array $source, array $keys): string
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

if (! function_exists('traveliz_laravel_tourist_reviews_pick_image')) {
    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     * @return array<string, mixed>|int|string|false
     */
    function traveliz_laravel_tourist_reviews_pick_image(array $source, array $keys)
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

if (! function_exists('traveliz_laravel_row_s_flexibol_tourist_reviews_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_tourist_reviews_from_data(array $data): array
    {
        $itemsRaw = $data['items'] ?? $data['reviews'] ?? $data['s_flexibol_tourist_items'] ?? [];
        if (! is_array($itemsRaw)) {
            $itemsRaw = [];
        }

        $items = [];
        foreach ($itemsRaw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $items[] = [
                's_flexibol_tourist_photo' => traveliz_laravel_tourist_reviews_pick_image($item, [
                    's_flexibol_tourist_photo',
                    'photo',
                    'image',
                    'avatar',
                ]),
                's_flexibol_tourist_name' => traveliz_laravel_tourist_reviews_pick_scalar($item, [
                    's_flexibol_tourist_name',
                    'name',
                    'author',
                ]),
                's_flexibol_tourist_text' => traveliz_laravel_tourist_reviews_pick_scalar($item, [
                    's_flexibol_tourist_text',
                    'text',
                    'review',
                    'quote',
                ]),
            ];
        }

        return [
            'acf_fc_layout' => 's_flexibol_tourist_reviews',
            's_flexibol_tourist_block_title' => traveliz_laravel_tourist_reviews_pick_scalar($data, [
                's_flexibol_tourist_block_title',
                'title',
                'heading',
                'block_title',
            ]),
            's_flexibol_tourist_items' => $items,
        ];
    }
}
