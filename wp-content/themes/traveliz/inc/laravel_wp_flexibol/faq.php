<?php

if (! function_exists('traveliz_laravel_row_s_flexibol_faq_from_data')) {
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    function traveliz_laravel_row_s_flexibol_faq_from_data(array $data): ?array
    {
        $main = '';
        if (isset($data['s_flexibol_faq_main_title']) && is_scalar($data['s_flexibol_faq_main_title'])) {
            $main = trim((string) $data['s_flexibol_faq_main_title']);
        }
        if ($main === '' && isset($data['title']) && is_scalar($data['title'])) {
            $main = trim((string) $data['title']);
        }

        $raw_items = $data['s_flexibol_faq_items'] ?? $data['items'] ?? [];
        if (! is_array($raw_items)) {
            $raw_items = [];
        }

        $repeater_rows = [];
        foreach ($raw_items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $q = '';
            if (isset($item['s_flexibol_faq_question']) && is_scalar($item['s_flexibol_faq_question'])) {
                $q = trim((string) $item['s_flexibol_faq_question']);
            } elseif (isset($item['question']) && is_scalar($item['question'])) {
                $q = trim((string) $item['question']);
            }

            $a = '';
            if (isset($item['s_flexibol_faq_answer']) && is_scalar($item['s_flexibol_faq_answer'])) {
                $a = (string) $item['s_flexibol_faq_answer'];
            } elseif (isset($item['answer']) && is_scalar($item['answer'])) {
                $a = (string) $item['answer'];
            }

            if ($q === '' && trim($a) === '') {
                continue;
            }
            $repeater_rows[] = [
                's_flexibol_faq_question' => $q,
                's_flexibol_faq_answer' => $a,
            ];
        }

        if ($main === '' && $repeater_rows === []) {
            return null;
        }

        return [
            'acf_fc_layout' => 's_flexibol_faq',
            's_flexibol_faq_main_title' => $main,
            's_flexibol_faq_items' => $repeater_rows,
        ];
    }
}
