<?php

if (! function_exists('traveliz_laravel_parking_pick_scalar')) {
    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     */
    function traveliz_laravel_parking_pick_scalar(array $source, array $keys): string
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

if (! function_exists('traveliz_laravel_row_s_flexibol_parking_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_parking_from_data(array $data): array
    {
        $cardsRaw = $data['items'] ?? $data['cards'] ?? $data['s_flexibol_parking_cards'] ?? [];
        if (! is_array($cardsRaw)) {
            $cardsRaw = [];
        }

        $cards = [];
        foreach ($cardsRaw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $cards[] = [
                's_flexibol_parking_card_title' => traveliz_laravel_parking_pick_scalar($item, [
                    's_flexibol_parking_card_title',
                    'title',
                    'name',
                    'heading',
                ]),
                's_flexibol_parking_card_text' => traveliz_laravel_parking_pick_scalar($item, [
                    's_flexibol_parking_card_text',
                    'text',
                    'description',
                    'body',
                ]),
                's_flexibol_parking_card_map_link' => traveliz_laravel_parking_pick_scalar($item, [
                    's_flexibol_parking_card_map_link',
                    'map_link',
                    'link',
                    'url',
                ]),
                's_flexibol_parking_card_button_label' => traveliz_laravel_parking_pick_scalar($item, [
                    's_flexibol_parking_card_button_label',
                    'button_label',
                    'button_text',
                ]),
            ];
        }

        return [
            'acf_fc_layout' => 's_flexibol_parking',
            's_flexibol_parking_section_title' => traveliz_laravel_parking_pick_scalar($data, [
                's_flexibol_parking_section_title',
                'title',
                'heading',
            ]),
            's_flexibol_parking_subtitle' => traveliz_laravel_parking_pick_scalar($data, [
                's_flexibol_parking_subtitle',
                'subtitle',
                'subheading',
            ]),
            's_flexibol_parking_cards' => $cards,
            's_flexibol_parking_footer_text' => traveliz_laravel_parking_pick_scalar($data, [
                's_flexibol_parking_footer_text',
                'footer_text',
                'bottom_text',
            ]),
        ];
    }
}
