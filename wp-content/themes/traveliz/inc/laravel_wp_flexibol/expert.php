<?php

if (! function_exists('traveliz_laravel_expert_pick_scalar')) {
    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     */
    function traveliz_laravel_expert_pick_scalar(array $source, array $keys): string
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

if (! function_exists('traveliz_laravel_expert_pick_image')) {
    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     * @return array<string, mixed>|int|string|false
     */
    function traveliz_laravel_expert_pick_image(array $source, array $keys)
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

if (! function_exists('traveliz_laravel_row_s_flexibol_expert_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_expert_from_data(array $data): array
    {
        return [
            'acf_fc_layout' => 's_flexibol_expert',
            's_flexibol_expert_section_title' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_section_title',
                'title',
                'heading',
            ]),
            's_flexibol_expert_photo' => traveliz_laravel_expert_pick_image($data, [
                's_flexibol_expert_photo',
                'photo',
                'image',
                'avatar',
            ]),
            's_flexibol_expert_name' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_name',
                'name',
            ]),
            's_flexibol_expert_role' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_role',
                'role',
            ]),
            's_flexibol_expert_stat_strong' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_stat_strong',
                'stat_value',
                'stat_strong',
            ]),
            's_flexibol_expert_stat_text' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_stat_text',
                'stat_label',
                'stat_text',
            ]),
            's_flexibol_expert_quote' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_quote',
                'quote',
            ]),
            's_flexibol_expert_body' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_body',
                'text',
                'body',
                'description',
            ]),
            's_flexibol_expert_item_1_title' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_item_1_title',
                'item_1_title',
                'item1_title',
            ]),
            's_flexibol_expert_item_1_text' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_item_1_text',
                'item_1_text',
                'item1_text',
            ]),
            's_flexibol_expert_item_2_title' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_item_2_title',
                'item_2_title',
                'item2_title',
            ]),
            's_flexibol_expert_item_2_text' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_item_2_text',
                'item_2_text',
                'item2_text',
            ]),
            's_flexibol_expert_item_3_title' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_item_3_title',
                'item_3_title',
                'item3_title',
            ]),
            's_flexibol_expert_item_3_text' => traveliz_laravel_expert_pick_scalar($data, [
                's_flexibol_expert_item_3_text',
                'item_3_text',
                'item3_text',
            ]),
        ];
    }
}
