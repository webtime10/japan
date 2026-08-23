<?php

if (! function_exists('traveliz_laravel_where_to_stay_pick_scalar')) {
    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     */
    function traveliz_laravel_where_to_stay_pick_scalar(array $source, array $keys): string
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

if (! function_exists('traveliz_laravel_row_s_flexibol_where_to_stay_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_where_to_stay_from_data(array $data): array
    {
        $cardsRaw = $data['items'] ?? $data['cards'] ?? $data['s_flexibol_where_stay_cards'] ?? [];
        if (! is_array($cardsRaw)) {
            $cardsRaw = [];
        }

        $cards = [];
        foreach ($cardsRaw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $cards[] = [
                's_flexibol_where_stay_card_title' => traveliz_laravel_where_to_stay_pick_scalar($item, [
                    's_flexibol_where_stay_card_title',
                    'title',
                    'name',
                    'heading',
                ]),
                's_flexibol_where_stay_card_text' => traveliz_laravel_where_to_stay_pick_scalar($item, [
                    's_flexibol_where_stay_card_text',
                    'text',
                    'description',
                    'body',
                ]),
            ];
        }

        return [
            'acf_fc_layout' => 's_flexibol_where_to_stay',
            's_flexibol_where_stay_section_title' => traveliz_laravel_where_to_stay_pick_scalar($data, [
                's_flexibol_where_stay_section_title',
                'title',
                'heading',
            ]),
            's_flexibol_where_stay_lead_text' => traveliz_laravel_where_to_stay_pick_scalar($data, [
                's_flexibol_where_stay_lead_text',
                'lead_text',
                'subtitle',
                's_flexibol_where_stay_subtitle',
                'intro',
                'text',
            ]),
            's_flexibol_where_stay_cards' => $cards,
        ];
    }
}
