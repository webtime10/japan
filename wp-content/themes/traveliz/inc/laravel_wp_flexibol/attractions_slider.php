<?php

if (! function_exists('traveliz_laravel_row_s_flexibol_attractions_slider_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_attractions_slider_from_data(array $data): array
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
            'acf_fc_layout' => 's_flexibol_attractions_slider',
            's_flexibol_attractions_title' => $pick($data, [
                's_flexibol_attractions_title',
                'title',
                'heading',
                'section_title',
            ]),
            'dop_text_landmark' => $pick($data, [
                'dop_text_landmark',
            ]),
        ];

        $items = $data['items'] ?? $data['s_flexibol_attractions_items'] ?? [];
        if (! is_array($items)) {
            $items = [];
        }

        $rows = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $rows[] = [
                's_flexibol_attractions_card_title' => $pick($item, [
                    's_flexibol_attractions_card_title',
                    'title',
                    'name',
                    'heading',
                ]),
                's_flexibol_attractions_image' => false,
                's_flexibol_attractions_text' => $pick($item, [
                    's_flexibol_attractions_text',
                    'text',
                    'description',
                    'body',
                ]),
                's_flexibol_attractions_button_text' => $pick($item, [
                    's_flexibol_attractions_button_text',
                    'button_text',
                    'cta_text',
                ]),
                's_flexibol_attractions_button_link' => $pick($item, [
                    's_flexibol_attractions_button_link',
                    'button_link',
                    'cta_url',
                    'link',
                    'url',
                ]),
            ];
        }
        $row['s_flexibol_attractions_items'] = $rows;

        return $row;
    }
}
