<?php

if (! function_exists('traveliz_laravel_extract_title_and_html_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array{0:string,1:string}
     */
    function traveliz_laravel_extract_title_and_html_from_data(array $data): array
    {
        $title = '';
        foreach (['title', 'heading', 'headline', 'name', 'label'] as $k) {
            if (! array_key_exists($k, $data)) {
                continue;
            }
            $v = $data[$k];
            if (is_string($v) || is_numeric($v)) {
                $title = trim((string) $v);
                if ($title !== '') {
                    break;
                }
            }
        }

        $text = '';
        foreach (['text', 'content', 'html', 'body', 'description', 'copy', 'article'] as $k) {
            if (! array_key_exists($k, $data)) {
                continue;
            }
            $v = $data[$k];
            if (is_string($v) || is_numeric($v)) {
                $text = (string) $v;
                if (trim($text) !== '') {
                    break;
                }
            }
        }

        if ($text === '' && $title === '') {
            foreach ($data as $k => $v) {
                if ($k === 'layout' || $k === 'acf_fc_layout') {
                    continue;
                }
                if (is_string($v) && trim($v) !== '') {
                    $text .= '<p><strong>'.esc_html((string) $k).'</strong></p>'.wp_kses_post($v);
                } elseif (is_array($v) && $v !== []) {
                    $enc = wp_json_encode($v, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    $text .= '<p><strong>'.esc_html((string) $k).'</strong></p><pre>'.esc_html((string) $enc).'</pre>';
                }
            }
        }

        return [$title, $text];
    }
}

if (! function_exists('traveliz_laravel_row_from_acf_layout_passthrough')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    function traveliz_laravel_row_from_acf_layout_passthrough(string $layout, array $data): ?array
    {
        if (! preg_match('/^s_flexibol_[a-z0-9_]+$/', $layout)) {
            return null;
        }

        $has_named_subfield = false;
        foreach (array_keys($data) as $dk) {
            if (is_string($dk) && str_starts_with($dk, 's_flexibol_')) {
                $has_named_subfield = true;
                break;
            }
        }
        if (! $has_named_subfield) {
            return null;
        }

        $row = ['acf_fc_layout' => $layout];
        foreach ($data as $k => $v) {
            if (! is_string($k) || $k === 'layout' || $k === 'acf_fc_layout') {
                continue;
            }
            if (is_string($v) || is_numeric($v)) {
                $row[$k] = (string) $v;
            } elseif (is_array($v)) {
                $row[$k] = $v;
            } elseif ($v === false) {
                $row[$k] = false;
            }
        }

        return $row;
    }
}
