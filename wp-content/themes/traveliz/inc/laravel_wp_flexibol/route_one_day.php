<?php

if (! function_exists('traveliz_laravel_route_day_timeline_rows')) {
    /**
     * @param array<string, mixed> $point
     * @return array<string, string>|null
     */
    function traveliz_laravel_route_day_timeline_rows(array $point): ?array
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

        $time = $pick($point, ['s_flexibol_route_time', 'time', 'hour']);
        $text = $pick($point, ['s_flexibol_route_text', 'text', 'description']);
        if ($time === '' && trim($text) === '') {
            return null;
        }

        return [
            's_flexibol_route_time' => $time,
            's_flexibol_route_text' => $text,
        ];
    }
}

if (! function_exists('traveliz_laravel_route_day_photo_rows')) {
    /**
     * @param array<string, mixed> $photoItem
     * @return array<string, mixed>|null
     */
    function traveliz_laravel_route_day_photo_rows(array $photoItem): ?array
    {
        $img = null;
        foreach (['s_flexibol_route_photo', 'photo', 'image', 'url'] as $pk) {
            if (! array_key_exists($pk, $photoItem)) {
                continue;
            }
            $img = $photoItem[$pk];
            break;
        }
        if ($img === null || $img === '' || $img === false) {
            return ['s_flexibol_route_photo' => false];
        }
        if (is_numeric($img)) {
            return ['s_flexibol_route_photo' => (int) $img];
        }
        if (is_string($img)) {
            return ['s_flexibol_route_photo' => $img];
        }
        if (is_array($img)) {
            return ['s_flexibol_route_photo' => $img];
        }

        return null;
    }
}

if (! function_exists('traveliz_laravel_route_one_day_single_from_data')) {
    /**
     * Одна строка репитера «день»: badge, subtitle, timeline, photos (сырой data одного дня).
     *
     * @param array<string, mixed> $day
     * @return array<string, mixed>
     */
    function traveliz_laravel_route_one_day_single_from_data(array $day): array
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

        $timelineRaw = $day['timeline'] ?? $day['schedule'] ?? $day['stops'] ?? $day['s_flexibol_route_day_timeline'] ?? [];
        if (! is_array($timelineRaw)) {
            $timelineRaw = [];
        }

        $timelineRows = [];
        foreach ($timelineRaw as $point) {
            if (! is_array($point)) {
                continue;
            }
            $row = traveliz_laravel_route_day_timeline_rows($point);
            if ($row !== null) {
                $timelineRows[] = $row;
            }
        }

        $photosRaw = $day['photos'] ?? $day['images'] ?? $day['s_flexibol_route_day_photos'] ?? [];
        if (! is_array($photosRaw)) {
            $photosRaw = [];
        }

        $photoRows = [];
        foreach ($photosRaw as $photoItem) {
            if (! is_array($photoItem)) {
                continue;
            }
            $pr = traveliz_laravel_route_day_photo_rows($photoItem);
            if ($pr !== null) {
                $photoRows[] = $pr;
            }
        }

        return [
            's_flexibol_route_day_badge' => $pick($day, [
                's_flexibol_route_day_badge',
                'badge',
                'day_badge',
            ]),
            's_flexibol_route_day_subtitle' => $pick($day, [
                's_flexibol_route_day_subtitle',
                'subtitle',
                'day_title',
                'heading',
            ]),
            's_flexibol_route_day_timeline' => $timelineRows,
            's_flexibol_route_day_photos' => $photoRows,
        ];
    }
}

if (! function_exists('traveliz_laravel_row_s_flexibol_route_one_day_from_data')) {
    /**
     * Новый формат: section_title + days[{ badge, subtitle, timeline[], photos[] }].
     * Старый формат (один день в объекте): title, subtitle, badge, timeline, photos → один общий заголовок + один день.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    function traveliz_laravel_row_s_flexibol_route_one_day_from_data(array $data): array
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

        $daysRaw = $data['days'] ?? null;
        $days = [];

        if (is_array($daysRaw) && $daysRaw !== []) {
            foreach ($daysRaw as $day) {
                if (! is_array($day)) {
                    continue;
                }
                $days[] = traveliz_laravel_route_one_day_single_from_data($day);
            }
        } else {
            $days = [ traveliz_laravel_route_one_day_single_from_data($data) ];
        }

        return [
            'acf_fc_layout' => 's_flexibol_route_one_day',
            's_flexibol_route_section_title' => $pick($data, [
                'section_title',
                's_flexibol_route_section_title',
                'route_title',
                'title',
                'heading',
                'main_title',
            ]),
            'rout_dop_text' => $pick($data, [
                'rout_dop_text',
            ]),
            's_flexibol_route_days' => $days,
        ];
    }
}
