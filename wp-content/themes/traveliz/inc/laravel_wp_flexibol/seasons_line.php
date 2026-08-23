<?php

if (! function_exists('traveliz_laravel_seasons_data_pick_scalar')) {
    /**
     * @param array<string, mixed> $data
     * @param list<string> $keys
     */
    function traveliz_laravel_seasons_data_pick_scalar(array $data, array $keys): string
    {
        foreach ($keys as $k) {
            if (! array_key_exists($k, $data)) {
                continue;
            }
            $v = $data[$k];
            if (is_string($v) || is_numeric($v)) {
                return (string) $v;
            }
        }

        return '';
    }
}

if (! function_exists('traveliz_laravel_seasons_data_pick_image')) {
    /**
     * @param array<string, mixed> $data
     * @param list<string> $keys
     * @return mixed
     */
    function traveliz_laravel_seasons_data_pick_image(array $data, array $keys)
    {
        foreach ($keys as $k) {
            if (! array_key_exists($k, $data)) {
                continue;
            }
            $v = $data[$k];
            if (is_array($v) && $v !== []) {
                return $v;
            }
            if (is_numeric($v) && (int) $v > 0) {
                return (int) $v;
            }
        }

        return false;
    }
}

if (! function_exists('traveliz_laravel_seasons_month_abbr_map')) {
    /**
     * @return array<string, string>
     */
    function traveliz_laravel_seasons_month_abbr_map(): array
    {
        return [
            'jan' => 'january',
            'feb' => 'february',
            'mar' => 'march',
            'apr' => 'april',
            'may' => 'may',
            'jun' => 'june',
            'jul' => 'july',
            'aug' => 'august',
            'sep' => 'september',
            'oct' => 'october',
            'nov' => 'november',
            'dec' => 'december',
        ];
    }
}

if (! function_exists('traveliz_laravel_row_s_flexibol_seasons_line_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_seasons_line_from_data(array $data): array
    {
        $row = [
            'acf_fc_layout' => 's_flexibol_seasons_line',
            's_flexibol_seasons_line_section_title' => traveliz_laravel_seasons_data_pick_scalar($data, [
                's_flexibol_seasons_line_section_title',
                'title',
                'heading',
                'section_title',
            ]),
            'pod_zag_pogoda' => traveliz_laravel_seasons_data_pick_scalar($data, [
                'pod_zag_pogoda',
            ]),
        ];

        foreach (traveliz_laravel_seasons_month_abbr_map() as $abbr => $full) {
            $p = "s_flexibol_season_{$full}_";
            $row[$p.'title'] = traveliz_laravel_seasons_data_pick_scalar($data, [$p.'title', "{$abbr}_title", "{$full}_title"]);
            $row[$p.'subtitle'] = traveliz_laravel_seasons_data_pick_scalar($data, [$p.'subtitle', "{$abbr}_subtitle", "{$full}_subtitle"]);
            $row[$p.'short_text'] = traveliz_laravel_seasons_data_pick_scalar($data, [$p.'short_text', "{$abbr}_text", "{$abbr}_short_text", "{$full}_text", "{$full}_short_text"]);
            $row[$p.'image'] = traveliz_laravel_seasons_data_pick_image($data, [$p.'image', "{$abbr}_image", "{$full}_image"]);
            $row[$p.'weather_icon'] = traveliz_laravel_seasons_data_pick_image($data, [$p.'weather_icon', "{$abbr}_weather_icon", "{$full}_weather_icon"]);
        }

        foreach ($data as $k => $v) {
            if (! is_string($k) || ! str_starts_with($k, 's_flexibol_season_')) {
                continue;
            }
            if ($k === 's_flexibol_seasons_line_section_title') {
                continue;
            }
            if (str_ends_with($k, '_image') || str_ends_with($k, '_weather_icon')) {
                if (is_array($v) && $v !== []) {
                    $row[$k] = $v;
                } elseif (is_numeric($v) && (int) $v > 0) {
                    $row[$k] = (int) $v;
                } else {
                    $row[$k] = false;
                }
                continue;
            }
            if (is_scalar($v) && ! is_bool($v)) {
                $row[$k] = (string) $v;
            }
        }

        if (function_exists('traveliz_pogoda_fill_seasons_line_images')) {
            $row = traveliz_pogoda_fill_seasons_line_images($row);
        }

        return $row;
    }
}
