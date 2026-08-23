<?php
/**
 * Приём данных из Laravel и создание/обновление страниц (post_type page) по языкам + Polylang.
 *
 * Контент AI пишется в ACF flexible `s_flexibol_constructor` (см. inc/acf/s_flexibol_constructor.php),
 * а не в HTML post_content (post_content можно оставить пустым).
 *
 * Подробные логи в debug.log — только при TRAVELIZ_LARAVEL_API_DEBUG в wp-config.php
 * или фильтре traveliz_laravel_api_verbose_log. Ошибки (WP_Error, исключения, отказ по ключу) логируются всегда.
 *
 * Ключ: LARAVEL_API_KEY (WP) ↔ X-Laravel-Api-Key (Laravel .env WORDPRESS_WEBHOOK_SECRET).
 */

// «создай API-роут в WordPress, который принимает POST-запрос и обрабатывает его»

add_action('rest_api_init', function () {
    register_rest_route('my-api/v1', '/create', [
        'methods' => 'POST',
        'callback' => 'traveliz_handle_laravel_create',
        'permission_callback' => 'traveliz_laravel_create_permission_check',
    ]);
});
//функция проверяет, нужно ли писать подробные логи
function traveliz_laravel_api_verbose(): bool
{
    if (defined('TRAVELIZ_LARAVEL_API_DEBUG') && TRAVELIZ_LARAVEL_API_DEBUG) {
        return true;
    }

    return (bool) apply_filters('traveliz_laravel_api_verbose_log', false);
}

/**
 * @param mixed $context
 */

 //«запиши лог, если включён debug (или если принудительно)»
function traveliz_laravel_api_log(string $label, $context = null, bool $force = false): void
{
    if (! $force && ! traveliz_laravel_api_verbose()) {
        return;
    }

    $prefix = '['.gmdate('Y-m-d H:i:s').' UTC] [laravel-api] '.$label;

    if ($context === null) {
        error_log($prefix);

        return;
    }

    if (is_string($context)) {
        error_log($prefix.' '.$context);

        return;
    }

    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR;
    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
    }

    $json = wp_json_encode($context, $flags);
    if ($json === false) {
        $json = '(wp_json_encode failed)';
    }

    error_log($prefix.' '.$json);
}

/**
 * @return array<string, string>
 */
//«возьми все заголовки запроса и подготовь их для логов (скрывая секреты)»

function traveliz_laravel_api_headers_for_log(WP_REST_Request $request): array
{
    $out = [];
    foreach ($request->get_headers() as $name => $values) {
        $lower = strtolower((string) $name);
        $val = is_array($values) ? implode(', ', $values) : (string) $values;
        if (str_contains($lower, 'laravel') && str_contains($lower, 'api') && str_contains($lower, 'key')) {
            $val = $val === '' ? '' : '***';
        }
        $out[$name] = $val;
    }

    return $out;
}

/**
 * Slug'и языков, которые Polylang реально знает (чтобы не вызывать pll_set_post_language с «левым» кодом).
 *
 * @return array<int, string>
 */

//Я получаю список языков, которые реально настроены в Polylang 
function traveliz_laravel_pll_language_slugs(): array
{
    if (! function_exists('pll_languages_list')) {
        return [];
    }

    $list = pll_languages_list(['fields' => 'slug']);
    if (! is_array($list)) {
        return [];
    }

    return array_values(array_filter(array_map('strval', $list)));
}
//«если ключ задан — проверь его, если нет — пускай всех»
function traveliz_laravel_create_permission_check(WP_REST_Request $request): bool
{
    if (! defined('LARAVEL_API_KEY') || LARAVEL_API_KEY === '') {
        return true;
    }

    $expected = trim((string) LARAVEL_API_KEY);
    $sent = trim((string) $request->get_header('x-laravel-api-key'));
    // Резерв: некоторые прокси режут кастомные заголовки, поэтому допускаем ключ в параметре.
    if ($sent === '') {
        $sent = trim((string) $request->get_param('api_key'));
    }
    $ok = ($expected !== '' && $sent !== '') ? hash_equals($expected, $sent) : false;

    if (! $ok) {
        traveliz_laravel_api_log('PERMISSION_DENIED', [
            'hint' => 'Неверный или пустой API key (header X-Laravel-Api-Key или param api_key)',
            'headers' => traveliz_laravel_api_headers_for_log($request),
            'has_api_key_param' => $request->get_param('api_key') !== null,
        ], true);
    }

    return $ok;
}

require_once __DIR__.'/laravel_wp_flexibol/mapping.php';
require_once __DIR__.'/laravel_wp_flexibol/shared.php';
require_once __DIR__.'/laravel_wp_flexibol/faq.php';
require_once __DIR__.'/laravel_wp_flexibol/seasons_line.php';
require_once __DIR__.'/laravel_wp_flexibol/regions_comparison.php';
require_once __DIR__.'/laravel_wp_flexibol/attractions_slider.php';
require_once __DIR__.'/laravel_wp_flexibol/route_one_day.php';
require_once __DIR__.'/laravel_wp_flexibol/price_table.php';
require_once __DIR__.'/laravel_wp_flexibol/active_otd.php';
require_once __DIR__.'/laravel_wp_flexibol/expert.php';
require_once __DIR__.'/laravel_wp_flexibol/where_to_stay.php';
require_once __DIR__.'/laravel_wp_flexibol/parking.php';
require_once __DIR__.'/laravel_wp_flexibol/tourist_reviews.php';
require_once __DIR__.'/laravel_wp_flexibol/section_expert_advice_new.php';
require_once __DIR__.'/laravel_wp_flexibol/builder.php';

// Легаси-реализация оставлена ниже как резерв (не исполняется, т.к. функции уже загружены из папки).
if (! function_exists('traveliz_laravel_ai_layout_to_acf_map')) {

/**
 * Соответствие layout из JSON AI → имя макета ACF flexible (поле acf_fc_layout).
 *
 * @return array<string, string>
 */
//«преобразуй layout из AI (Laravel) в layout ACF (WordPress)»
// 'acf_fc_layout' => 's_flexibol_faq'
function traveliz_laravel_ai_layout_to_acf_map(): array
{
    return apply_filters('traveliz_laravel_ai_layout_to_acf', [
        'text_country' => 's_flexibol_country_text',
        // Отдельные макеты в конструкторе (как при ручном добавлении), не три раза «о стране»
        'weather' => 's_flexibol_editor',
        'climate' => 's_flexibol_editor',
        'about_weather' => 's_flexibol_editor',
        'fake_news' => 's_flexibol_editor',
        'fakenews' => 's_flexibol_editor',
        'news' => 's_flexibol_editor',
        'editor' => 's_flexibol_editor',
        'wysiwyg' => 's_flexibol_editor',
        'map' => 's_flexibol_map',
        // FAQ: JSON { "layout":"faq", "data":{ "title":"...", "items":[{ "question":"...", "answer":"..." }] } }
        'faq' => 's_flexibol_faq',
        'questions_and_answers' => 's_flexibol_faq',
        // Линейка сезонов: layout seasons_line + data jan_title / jan_text / …
        'seasons_line' => 's_flexibol_seasons_line',
        'seasons' => 's_flexibol_seasons_line',
    ]);
}

/**
 * Ключ родительского поля flexible (имя в админке — s_flexibol_constructor).
 * Через API надёжнее писать по key, как рекомендует ACF.
 */

 //"запиши эти блоки в flexible-конструктор ACF"
function traveliz_laravel_acf_flexible_field_selector(): string
{
    return (string) apply_filters('traveliz_laravel_acf_flexible_field_selector', 'field_s_flexibol_constructor');
}

/**
 * Строки flexible_content из структуры AI (поля с ключом sections[]).
 *
 * @param array<string, mixed> $fields
 * @return list<array<string, mixed>>
 */

//«собери массив ACF-блоков (flexible rows) из AI-данных» 
function traveliz_laravel_build_flexible_rows_from_ai(array $fields): array
{
    $rows = [];
    $map = traveliz_laravel_ai_layout_to_acf_map();

    foreach ($fields as $ai_key => $block_value) {
        if (! is_array($block_value)) {
            continue;
        }
        $sections = $block_value['sections'] ?? $block_value['blocks'] ?? null;
        if (! is_array($sections)) {
            // Один объект без обёртки sections: например { "layout":"weather", "data":{...} }
            if (isset($block_value['layout']) || isset($block_value['data']) || isset($block_value['title']) || isset($block_value['text'])) {
                $sections = [ $block_value ];
            } else {
                continue;
            }
        }

        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }
            $layout = isset($section['layout']) ? (string) $section['layout'] : '';
            $data = [];
            if (isset($section['data']) && is_array($section['data'])) {
                $data = $section['data'];
            } elseif ($layout !== '' && $section !== []) {
                // data развернули на верхний уровень секции
                $data = $section;
                unset($data['layout']);
            }
            $row = traveliz_laravel_map_ai_section_to_acf_row($layout, $data, $map);
            if ($row !== null) {
                $rows[] = $row;
            } else {
                traveliz_laravel_api_log('FLEX_SECTION_SKIPPED', [
                    'ai_field_hint' => is_string($ai_key) ? $ai_key : '',
                    'layout' => $layout,
                    'data_keys' => is_array($data) ? array_keys($data) : [],
                ], true);
            }
        }
    }

    return apply_filters('traveliz_laravel_flexible_rows', $rows, $fields);
}

/**
 * Достаёт заголовок и HTML из произвольного data (разные имена полей у промптов / LLM). title = "Погода в Израиле"
 *
 * @param array<string, mixed> $data
 * @return array{0:string,1:string} [title, html]
 */
function traveliz_laravel_extract_title_and_html_from_data(array $data): array
{
    $title = '';
    foreach (['title', 'heading', 'headline', 'name', 'label'] as $k) {
        if (! array_key_exists($k, $data)) {
            continue;
        }
        $v = $data[ $k ];
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
        $v = $data[ $k ];
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

    return [ $title, $text ];
}

/**
 * JSON уже в формате имён макетов ACF (s_flexibol_*) — пробрасываем подполя как при ручном сохранении.
 *
 * @param array<string, mixed> $data
 * @return array<string, mixed>|null
 */
//AI прислал готовый ACF → не мапим → сразу используем
/*
еслт пришходит так то записвывем напрямую  layout = "s_flexibol_faq"
data = [
  "s_flexibol_faq_title" => "...",
]

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
    // Иначе это тот же JSON, что и для text_country (title/text) — не passthrough, а общий маппер ниже
    if (! $has_named_subfield) {
        return null;
    }

    $row = [
        'acf_fc_layout' => $layout,
    ];

    foreach ($data as $k => $v) {
        if (! is_string($k) || $k === 'layout' || $k === 'acf_fc_layout') {
            continue;
        }
        if (is_string($v) || is_numeric($v)) {
            $row[ $k ] = (string) $v;
        } elseif (is_array($v)) {
            $row[ $k ] = $v;
        } elseif ($v === false) {
            $row[ $k ] = false;
        }
    }

    return $row;
}

/**
 * @param array<string, mixed> $data
 * @return array<string, mixed>
 */
/*
«собери HTML и положи его в editor-блок ACF»
*/
function traveliz_laravel_row_s_flexibol_editor_from_data(array $data): array
{
    [ $t, $html ] = traveliz_laravel_extract_title_and_html_from_data($data);
    if (trim($html) === '' && isset($data['s_flexibol_editor']) && is_scalar($data['s_flexibol_editor'])) {
        $html = (string) $data['s_flexibol_editor'];
    }
    $body = $html;
    if ($t !== '') {
        $body = '<h2>'.esc_html($t).'</h2>'."\n".$body;
    }

    return [
        'acf_fc_layout' => 's_flexibol_editor',
        's_flexibol_editor' => $body,
    ];
}

/**
 * FAQ: ACF layout s_flexibol_faq — заголовок + repeater s_flexibol_faq_items (вопрос / ответ).
 * Принимает «человеческий» JSON от AI: title + items[].question / .answer
 * или уже поля s_flexibol_faq_*.
 *
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

/**
 * @param array<string, mixed> $data
 * @param list<string> $keys
 */
if (! function_exists('traveliz_laravel_seasons_data_pick_scalar')) {
function traveliz_laravel_seasons_data_pick_scalar(array $data, array $keys): string
{
    foreach ($keys as $k) {
        if (! array_key_exists($k, $data)) {
            continue;
        }
        $v = $data[ $k ];
        if (is_string($v) || is_numeric($v)) {
            return (string) $v;
        }
    }

    return '';
}
}

/**
 * @param array<string, mixed> $data
 * @param list<string> $keys
 * @return mixed attachment id / array / false
 */
if (! function_exists('traveliz_laravel_seasons_data_pick_image')) {
function traveliz_laravel_seasons_data_pick_image(array $data, array $keys)
{
    foreach ($keys as $k) {
        if (! array_key_exists($k, $data)) {
            continue;
        }
        $v = $data[ $k ];
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

/**
 * @return array<string, string> сокращение месяца → часть имени поля ACF (january…december)
 */
if (! function_exists('traveliz_laravel_seasons_month_abbr_map')) {
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

/**
 * Сезоны (s_flexibol_seasons_line): из AI с jan_title / jan_subtitle / jan_text …
 * или уже с именами s_flexibol_season_january_*.
 *
 * @param array<string, mixed> $data
 * @return array<string, mixed>
 */
if (! function_exists('traveliz_laravel_row_s_flexibol_seasons_line_from_data')) {
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
    ];

    foreach (traveliz_laravel_seasons_month_abbr_map() as $abbr => $full) {
        $p = "s_flexibol_season_{$full}_";
        $row[ $p.'title' ] = traveliz_laravel_seasons_data_pick_scalar($data, [
            $p.'title',
            "{$abbr}_title",
            "{$full}_title",
        ]);
        $row[ $p.'subtitle' ] = traveliz_laravel_seasons_data_pick_scalar($data, [
            $p.'subtitle',
            "{$abbr}_subtitle",
            "{$full}_subtitle",
        ]);
        $row[ $p.'short_text' ] = traveliz_laravel_seasons_data_pick_scalar($data, [
            $p.'short_text',
            "{$abbr}_text",
            "{$abbr}_short_text",
            "{$full}_text",
            "{$full}_short_text",
        ]);
        $row[ $p.'image' ] = traveliz_laravel_seasons_data_pick_image($data, [
            $p.'image',
            "{$abbr}_image",
            "{$full}_image",
        ]);
        $row[ $p.'weather_icon' ] = traveliz_laravel_seasons_data_pick_image($data, [
            $p.'weather_icon',
            "{$abbr}_weather_icon",
            "{$full}_weather_icon",
        ]);
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
                $row[ $k ] = $v;
            } elseif (is_numeric($v) && (int) $v > 0) {
                $row[ $k ] = (int) $v;
            } else {
                $row[ $k ] = false;
            }
            continue;
        }
        if (is_scalar($v) && ! is_bool($v)) {
            $row[ $k ] = (string) $v;
        }
    }

    if (function_exists('traveliz_pogoda_fill_seasons_line_images')) {
        $row = traveliz_pogoda_fill_seasons_line_images($row);
    }

    return $row;
}
}

function traveliz_laravel_map_ai_section_to_acf_row(string $layout, array $data, array $map): ?array
{
    $layout_raw = trim((string) $layout);
    $layout_key = strtolower($layout_raw);

    // Явные имена макетов ACF при JSON с title/text (без префиксов s_flexibol_* в data)
    if ($layout_key === 's_flexibol_editor') {
        return traveliz_laravel_row_s_flexibol_editor_from_data($data);
    }
    if ($layout_key === 's_flexibol_country_text') {
        [ $t, $html ] = traveliz_laravel_extract_title_and_html_from_data($data);

        return [
            'acf_fc_layout' => 's_flexibol_country_text',
            's_flexibol_title' => $t,
            's_flexibol_text' => $html,
            's_flexibol_image' => false,
            's_flexibol_text_2' => isset($data['text_2']) && is_scalar($data['text_2']) ? (string) $data['text_2'] : '',
        ];
    }
    if ($layout_key === 's_flexibol_faq') {
        $faq_row = traveliz_laravel_row_s_flexibol_faq_from_data($data);
        if ($faq_row !== null) {
            return $faq_row;
        }
    }
    if ($layout_key === 's_flexibol_seasons_line') {
        return traveliz_laravel_row_s_flexibol_seasons_line_from_data($data);
    }

    // Полный проброс подполей ACF (ручной формат сохранения)
    $passthrough = traveliz_laravel_row_from_acf_layout_passthrough($layout_key, $data);
    if ($passthrough !== null && count($passthrough) > 1) {
        return $passthrough;
    }

    if ($layout_key !== '' && isset($map[ $layout_key ])) {
        $acf_layout = $map[ $layout_key ];

        if ($acf_layout === 's_flexibol_country_text') {
            [ $t, $html ] = traveliz_laravel_extract_title_and_html_from_data($data);

            return [
                'acf_fc_layout' => $acf_layout,
                's_flexibol_title' => $t,
                's_flexibol_text' => $html,
                's_flexibol_image' => false,
                's_flexibol_text_2' => isset($data['text_2']) && is_scalar($data['text_2']) ? (string) $data['text_2'] : '',
            ];
        }

        if ($acf_layout === 's_flexibol_editor') {
            return traveliz_laravel_row_s_flexibol_editor_from_data($data);
        }

        if ($acf_layout === 's_flexibol_map') {
            return [
                'acf_fc_layout' => $acf_layout,
            ];
        }

        if ($acf_layout === 's_flexibol_faq') {
            $faq_row = traveliz_laravel_row_s_flexibol_faq_from_data($data);
            if ($faq_row !== null) {
                return $faq_row;
            }
        }

        if ($acf_layout === 's_flexibol_seasons_line') {
            return traveliz_laravel_row_s_flexibol_seasons_line_from_data($data);
        }
    }

    // Неизвестный slug — отдельный блок Editor, чтобы не дублировать «о стране»
    if ($layout_key !== '') {
        [ $t, $html ] = traveliz_laravel_extract_title_and_html_from_data($data);
        if ($t !== '' || trim($html) !== '') {
            return traveliz_laravel_row_s_flexibol_editor_from_data(array_merge($data, [
                'title' => $t !== '' ? $t : ucfirst(str_replace('_', ' ', $layout_key)),
                'text' => $html,
            ]));
        }
    }

    return null;
}
}

/**
 * WordPress + PHP 8.1+: null в данных поста после wp_slash остаётся null; дальше по цепочке вызывается
 * wp_normalize_path(null) → Deprecated strpos(null, …) в wp-includes/functions.php.
 * Заменяем null на пустую строку (ID — целое).
 *
 * @param array<string, mixed> $postarr
 * @return array<string, mixed>
 */

//Я очищаю данные от null, чтобы WordPress не падал при сохранении 
function traveliz_laravel_sanitize_postarr_for_wp_save(array $postarr): array
{
    foreach ($postarr as $key => $value) {
        if ($value === null) {
            if ($key === 'ID') {
                $postarr[ $key ] = 0;
            } else {
                $postarr[ $key ] = '';
            }
        }
    }

    return $postarr;
}

/**
 * Рекурсивно убирает null из значений ACF (иначе плагин/ядро могут передать null в пути файлов).
 *
 * @param mixed $value
 * @return mixed
 */
function traveliz_laravel_acf_value_no_null($value)
{
    if ($value === false) {
        return false;
    }
    if ($value === null) {
        return '';
    }
    if (is_array($value)) {
        $out = [];
        foreach ($value as $k => $v) {
            $out[ $k ] = traveliz_laravel_acf_value_no_null($v);
        }

        return $out;
    }

    return $value;
}

/**
 * @param list<array<string, mixed>> $rows
 * @return list<array<string, mixed>>
 */
function traveliz_laravel_sanitize_acf_flexible_rows(array $rows): array
{
    $clean = [];
    foreach ($rows as $row) {
        if (is_array($row)) {
            $clean[] = traveliz_laravel_acf_value_no_null($row);
        }
    }

    return $clean;
}

/**
 * @param array<string, mixed> $row
 */
function traveliz_laravel_flexible_row_layout(array $row): string
{
    return isset($row['acf_fc_layout']) && is_string($row['acf_fc_layout'])
        ? trim($row['acf_fc_layout'])
        : '';
}

/**
 * Merge режим: заменяем только пришедшие layout-строки, остальные оставляем.
 *
 * @param list<array<string, mixed>> $existing
 * @param list<array<string, mixed>> $incoming
 * @return list<array<string, mixed>>
 */
function traveliz_laravel_merge_flexible_rows(array $existing, array $incoming): array
{
    $incomingByLayout = [];
    foreach ($incoming as $row) {
        if (! is_array($row)) {
            continue;
        }
        $layout = traveliz_laravel_flexible_row_layout($row);
        if ($layout === '') {
            continue;
        }
        if (! isset($incomingByLayout[$layout])) {
            $incomingByLayout[$layout] = [];
        }
        $incomingByLayout[$layout][] = $row;
    }

    if ($incomingByLayout === []) {
        return $existing;
    }

    $merged = [];
    foreach ($existing as $row) {
        if (! is_array($row)) {
            continue;
        }
        $layout = traveliz_laravel_flexible_row_layout($row);
        if ($layout !== '' && isset($incomingByLayout[$layout]) && $incomingByLayout[$layout] !== []) {
            $merged[] = array_shift($incomingByLayout[$layout]);
            continue;
        }
        $merged[] = $row;
    }

    foreach ($incomingByLayout as $rows) {
        foreach ($rows as $row) {
            $merged[] = $row;
        }
    }

    return $merged;
}

/**
 * @param WP_REST_Request $request
 * @return WP_REST_Response|WP_Error
 */

//«прими данные из Laravel и создай (или обнови) страницы в WordPress»

function traveliz_handle_laravel_create(WP_REST_Request $request)
{
    traveliz_laravel_api_log('REQUEST_START', [
        'route' => $request->get_route(),
        'method' => $request->get_method(),
        'headers' => traveliz_laravel_api_headers_for_log($request),
    ]);

    try {
        $verbose = traveliz_laravel_api_verbose();
        if ($verbose) {
            $max_raw = (int) apply_filters('traveliz_laravel_api_log_max_body_bytes', 500000);
            $raw_body = $request->get_body();
            $raw_len = strlen($raw_body);
            if ($raw_len > $max_raw) {
                traveliz_laravel_api_log('REQUEST_BODY_RAW_TRUNCATED', ['bytes_total' => $raw_len, 'bytes_logged' => $max_raw]);
                traveliz_laravel_api_log('REQUEST_BODY_RAW', substr($raw_body, 0, $max_raw)."\n... [truncated]");
            } else {
                traveliz_laravel_api_log('REQUEST_BODY_RAW', $raw_body);
            }
        }

        $data = $request->get_json_params();
        if (! is_array($data)) {
            traveliz_laravel_api_log('PARSE_JSON_FAILED', [
                'get_json_params_type' => gettype($data),
                'json_last_error' => function_exists('json_last_error') ? json_last_error() : null,
            ], true);

            return new WP_Error('invalid_json', 'Ожидается JSON-тело запроса.', ['status' => 400]);
        }

        traveliz_laravel_api_log('REQUEST_JSON_PARSED', $data);

        $product_id = isset($data['product_id']) ? (int) $data['product_id'] : 0;
        if ($product_id <= 0) {
            traveliz_laravel_api_log('VALIDATION_FAILED', ['field' => 'product_id', 'value' => $data['product_id'] ?? null], true);

            return new WP_Error('bad_request', 'Нужен положительный product_id.', ['status' => 422]);
        }

        $ai_data = $data['ai_data'] ?? null;
        $merge_flexible = ! empty($data['merge_flexible']);
        if (! is_array($ai_data) || $ai_data === []) {
            traveliz_laravel_api_log('VALIDATION_FAILED', ['field' => 'ai_data', 'type' => gettype($ai_data)], true);

            return new WP_Error('bad_request', 'Нужен непустой объект ai_data.', ['status' => 422]);
        }

        $titles = isset($data['titles']) && is_array($data['titles']) ? $data['titles'] : [];
        $slugs = isset($data['slugs']) && is_array($data['slugs']) ? $data['slugs'] : [];

        // Шаблон страницы использует ACF flexible только на типе `page`.
        $post_type = apply_filters('traveliz_laravel_import_post_type', 'page');
        if (! is_string($post_type) || $post_type === '') {
            $post_type = 'page';
        }
        $post_type = sanitize_key($post_type);
        if ($post_type === '') {
            $post_type = 'page';
        }

        $translations = [];
        $errors = [];
        $pll_slugs = traveliz_laravel_pll_language_slugs();

        foreach ($ai_data as $lang_code => $fields) {
            if (! is_string($lang_code) || $lang_code === '') {
                traveliz_laravel_api_log('SKIP_LANG_KEY', ['key' => $lang_code, 'type' => gettype($lang_code)]);
                continue;
            }
            $lang_code = strtolower($lang_code);

            if (! is_array($fields)) {
                $errors[] = "Язык {$lang_code}: ai_data должен быть объектом полей.";
                traveliz_laravel_api_log('LANG_FIELDS_INVALID', ['lang' => $lang_code, 'got_type' => gettype($fields)], true);
                continue;
            }

            $title = isset($titles[$lang_code]) && $titles[$lang_code] !== ''
                ? (string) $titles[$lang_code]
                : sprintf('Товар #%d (%s)', $product_id, $lang_code);

            $flex_rows = traveliz_laravel_build_flexible_rows_from_ai($fields);
            $post_content = '';
            if ($flex_rows === [] && $fields !== []) {
                $post_content = traveliz_laravel_render_ai_fields_html($fields);
            }

            $post_name_base = isset($slugs[$lang_code]) ? (string) $slugs[$lang_code] : '';
            $post_name = $post_name_base !== '' ? sanitize_title($post_name_base) : '';
            if ($post_name === '') {
                $post_name = sanitize_title('laravel-product-'.$product_id.'-'.$lang_code);
            }
            $post_name .= '-p'.$product_id;

            $existing_id = traveliz_laravel_find_import_post_id($product_id, $lang_code, $post_type);
            // Раньше импорт шёл в `post` — подхватываем те же мета-записи и конвертируем в `page` при сохранении.
            if ($existing_id <= 0 && $post_type === 'page') {
                $existing_id = traveliz_laravel_find_import_post_id($product_id, $lang_code, 'post');
            }

            $post_status = apply_filters('traveliz_laravel_import_post_status', 'draft');
            if (! is_string($post_status) || $post_status === '') {
                $post_status = 'draft';
            }

            $postarr = traveliz_laravel_sanitize_postarr_for_wp_save([
                'post_type' => $post_type,
                'post_status' => $post_status,
                'post_title' => wp_strip_all_tags((string) $title),
                'post_name' => (string) $post_name,
                'post_content' => (string) $post_content,
            ]);

            traveliz_laravel_api_log('POST_SAVE_ATTEMPT', [
                'lang' => $lang_code,
                'existing_id' => $existing_id,
                'flex_rows_count' => count($flex_rows),
                'post_content_len' => strlen((string) $post_content),
            ]);

            if ($existing_id > 0) {
                $postarr['ID'] = $existing_id;
                $postarr = traveliz_laravel_sanitize_postarr_for_wp_save($postarr);
                $result = wp_update_post(wp_slash($postarr), true);
            } else {
                $result = wp_insert_post(wp_slash($postarr), true);
            }

            if (is_wp_error($result)) {
                traveliz_laravel_api_log('POST_SAVE_WP_ERROR', [
                    'lang' => $lang_code,
                    'codes' => $result->get_error_codes(),
                    'messages' => $result->get_error_messages(),
                    'data' => $result->get_all_error_data(),
                ], true);
                $errors[] = $lang_code.': '.$result->get_error_message();
                continue;
            }

            $post_id = (int) $result;
            update_post_meta($post_id, '_laravel_product_id', $product_id);
            update_post_meta($post_id, '_laravel_lang', $lang_code);

            if ($flex_rows !== [] && function_exists('update_field')) {
                if ($merge_flexible && $existing_id > 0 && function_exists('get_field')) {
                    $existing_rows = get_field('s_flexibol_constructor', $post_id);
                    if (is_array($existing_rows) && $existing_rows !== []) {
                        $flex_rows = traveliz_laravel_merge_flexible_rows($existing_rows, $flex_rows);
                    }
                }

                $flex_rows_clean = traveliz_laravel_sanitize_acf_flexible_rows($flex_rows);
                $acf_selector = traveliz_laravel_acf_flexible_field_selector();
                $acf_ok = update_field($acf_selector, $flex_rows_clean, $post_id);
                if (! $acf_ok) {
                    $acf_ok = update_field('s_flexibol_constructor', $flex_rows_clean, $post_id);
                }
                if (! $acf_ok) {
                    traveliz_laravel_api_log('ACF_UPDATE_FIELD_FAILED', [
                        'post_id' => $post_id,
                        'lang' => $lang_code,
                        'selector_tried' => $acf_selector,
                        'row_count' => count($flex_rows_clean),
                        'acf_fc_layouts' => array_values(array_filter(array_map(
                            static function ($r) {
                                return is_array($r) && isset($r['acf_fc_layout']) ? (string) $r['acf_fc_layout'] : '';
                            },
                            $flex_rows_clean
                        ))),
                    ], true);
                }
            }

            if (function_exists('pll_set_post_language') && $pll_slugs !== [] && in_array($lang_code, $pll_slugs, true)) {
                pll_set_post_language($post_id, $lang_code);
            }

            $translations[$lang_code] = $post_id;
            traveliz_laravel_api_log('POST_SAVE_OK', ['lang' => $lang_code, 'post_id' => $post_id, 'acf_rows' => count($flex_rows)]);
        }

        if ($translations !== [] && function_exists('pll_save_post_translations')) {
            $pll_translations = $translations;
            if ($pll_slugs !== []) {
                $pll_translations = array_intersect_key($translations, array_flip($pll_slugs));
            }
            traveliz_laravel_api_log('POLYLANG_SAVE_TRANSLATIONS', [
                'all' => $translations,
                'pll_only' => $pll_translations,
                'pll_slugs' => $pll_slugs,
            ]);
            if ($pll_translations !== []) {
                pll_save_post_translations($pll_translations);
            }
        }

        if ($errors !== []) {
            $payload = [
                'ok' => false,
                'product_id' => $product_id,
                'translations' => $translations,
                'errors' => $errors,
            ];
            traveliz_laravel_api_log('REQUEST_DONE_PARTIAL', $payload, true);

            return new WP_REST_Response($payload, 207);
        }

        $payload = [
            'ok' => true,
            'product_id' => $product_id,
            'translations' => $translations,
            'polylang' => function_exists('pll_save_post_translations'),
        ];
        traveliz_laravel_api_log('REQUEST_DONE_OK', $payload);

        return new WP_REST_Response($payload, 200);

    } catch (Throwable $e) {
        traveliz_laravel_api_log('REQUEST_EXCEPTION', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], true);

        return new WP_Error(
            'server_error',
            'Исключение: '.$e->getMessage(),
            ['status' => 500]
        );
    }
}
// «мы уже создавали такой пост или нет?» чтобы НЕ создавать дубликаты
function traveliz_laravel_find_import_post_id(int $product_id, string $lang_code, string $post_type): int
{
    $post_type = sanitize_key($post_type);
    if ($post_type === '') {
        return 0;
    }

    $q = new WP_Query([
        'post_type' => $post_type,
        'post_status' => 'any',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => '_laravel_product_id',
                'value' => $product_id,
                'compare' => '=',
                'type' => 'NUMERIC',
            ],
            [
                'key' => '_laravel_lang',
                'value' => $lang_code,
                'compare' => '=',
            ],
        ],
    ]);

    if (! $q->have_posts()) {
        return 0;
    }

    return (int) $q->posts[0];
}

/**
 * @param array<string, mixed> $fields
 */
// Если блоки не собрались, я просто вывожу данные как HTML на страницу

function traveliz_laravel_render_ai_fields_html(array $fields): string
{
    if ($fields === []) {
        return '<p>'.esc_html('Нет AI-полей для этого языка.').'</p>';
    }

    $out = '';
    foreach ($fields as $key => $val) {
        $out .= '<h2>'.esc_html((string) $key).'</h2>';
        if (is_array($val)) {
            $out .= '<pre>'.esc_html(wp_json_encode($val, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)).'</pre>';
        } else {
            $out .= '<div class="laravel-ai-field">'.wp_kses_post((string) $val).'</div>';
        }
    }

    return $out;
}
