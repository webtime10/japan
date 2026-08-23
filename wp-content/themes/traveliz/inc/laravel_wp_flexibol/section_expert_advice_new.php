<?php

if (! function_exists('traveliz_laravel_row_section_expert_advice_new_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_advice_from_data(array $data): array
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

        $itemsRaw = $data['items'] ?? $data['cards'] ?? $data['s_flexibol_advice_items'] ?? [];
        if (! is_array($itemsRaw)) {
            $itemsRaw = [];
        }

        $items = [];
        foreach ($itemsRaw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $items[] = [
                's_flexibol_advice_item_image' => false,
                's_flexibol_advice_item_title' => $pick($item, ['s_flexibol_advice_item_title', 'title']),
                's_flexibol_advice_item_text' => $pick($item, ['s_flexibol_advice_item_text', 'text', 'description']),
            ];
        }

        return [
            'acf_fc_layout' => 's_flexibol_advice',
            's_flexibol_advice_section_title' => $pick($data, [
                's_flexibol_advice_section_title',
                'heading',
                'title',
            ]),
            's_flexibol_advice_items' => $items,
        ];
    }
}

if (! function_exists('traveliz_laravel_row_section_expert_advice_new_from_data')) {
    /**
     * Специальный адаптер:
     * section_expert_advice_new -> s_flexibol_advice.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_section_expert_advice_new_from_data(array $data): array
    {
        $normalized = $data;

        return traveliz_laravel_row_s_flexibol_advice_from_data($normalized);
    }
}
