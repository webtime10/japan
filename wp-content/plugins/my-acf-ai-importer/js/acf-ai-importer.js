(function ($) {
    'use strict';

    var imageUrlHintShown = false;

    // Упрощенные ключи JSON -> технические ACF поля в flexibol.
    var SECTION_CONFIG = {
        faq: {
            jsonKey: 'faq',
            layout: 's_flexibol_faq',
            aliases: ['faq_section', 'section_faq'],
            fields: {
                simple: {
                    title: ['s_flexibol_faq_main_title', 'title', 'heading']
                },
                repeater: 's_flexibol_faq_items',
                repeaterSource: ['items', 'questions'],
                repeaterMap: {
                    question: ['s_flexibol_faq_question', 'question', 'q'],
                    answer: ['s_flexibol_faq_answer', 'answer', 'a']
                }
            }
        },
        expert: {
            jsonKey: 'expert',
            layout: 's_flexibol_expert',
            fields: {
                simple: {
                    title: ['s_flexibol_expert_section_title', 'title', 'heading'],
                    photo: ['s_flexibol_expert_photo', 'photo', 'image', 'avatar'],
                    name: ['s_flexibol_expert_name', 'name'],
                    role: ['s_flexibol_expert_role', 'role'],
                    quote: ['s_flexibol_expert_quote', 'quote'],
                    text: ['s_flexibol_expert_body', 'text', 'body', 'description'],
                    stat_value: ['s_flexibol_expert_stat_strong', 'stat_value', 'stat_strong'],
                    stat_label: ['s_flexibol_expert_stat_text', 'stat_label', 'stat_text'],
                    item_1_title: ['s_flexibol_expert_item_1_title', 'item_1_title', 'item1_title'],
                    item_1_text: ['s_flexibol_expert_item_1_text', 'item_1_text', 'item1_text'],
                    item_2_title: ['s_flexibol_expert_item_2_title', 'item_2_title', 'item2_title'],
                    item_2_text: ['s_flexibol_expert_item_2_text', 'item_2_text', 'item2_text'],
                    item_3_title: ['s_flexibol_expert_item_3_title', 'item_3_title', 'item3_title'],
                    item_3_text: ['s_flexibol_expert_item_3_text', 'item_3_text', 'item3_text']
                }
            }
        },
        advice: {
            jsonKey: 'advice',
            layout: 's_flexibol_advice',
            fields: {
                simple: {
                    title: ['s_flexibol_advice_section_title', 'title', 'heading']
                },
                repeater: 's_flexibol_advice_items',
                repeaterSource: ['items', 'cards'],
                repeaterMap: {
                    title: ['s_flexibol_advice_item_title', 'title'],
                    text: ['s_flexibol_advice_item_text', 'text', 'description']
                }
            }
        },
        section_expert_advice_new: {
            jsonKey: 'section_expert_advice_new',
            layout: 's_flexibol_advice',
            fields: {
                simple: {
                    title: ['s_flexibol_advice_section_title', 'heading', 'title']
                },
                repeater: 's_flexibol_advice_items',
                repeaterSource: ['items', 'cards'],
                repeaterMap: {
                    title: ['s_flexibol_advice_item_title', 'title'],
                    text: ['s_flexibol_advice_item_text', 'text', 'description']
                }
            }
        },
        active: {
            jsonKey: 'active',
            layout: 's_flexibol_active_otd',
            fields: {
                simple: {
                    title: ['s_flexibol_active_otd_section_title', 'title', 'heading'],
                    bottom_text: ['s_flexibol_active_otd_bottom_text', 'bottom_text']
                },
                repeater: 's_flexibol_active_otd_items',
                repeaterSource: ['items', 'cards'],
                repeaterMap: {
                    title: ['s_flexibol_active_otd_item_title', 'title'],
                    text: ['s_flexibol_active_otd_item_text', 'text', 'description']
                }
            }
        },
        attractions: {
            jsonKey: 'attractions',
            layout: 's_flexibol_attractions_slider',
            aliases: ['landmark'],
            fields: {
                simple: {
                    title: ['s_flexibol_attractions_title', 'title', 'heading'],
                    dop_text_landmark: ['dop_text_landmark']
                },
                repeater: 's_flexibol_attractions_items',
                repeaterSource: ['items', 'slides'],
                repeaterMap: {
                    title: ['s_flexibol_attractions_card_title', 'title'],
                    text: ['s_flexibol_attractions_text', 'text', 'description'],
                    button_text: ['s_flexibol_attractions_button_text', 'button_text', 'cta_text'],
                    button_link: ['s_flexibol_attractions_button_link', 'button_link', 'cta_url']
                }
            }
        },
        city: {
            jsonKey: 'city',
            layout: 's_flexibol_city_slider',
            fields: {
                simple: {
                    title: ['s_flexibol_city_slider_title', 'title', 'heading']
                },
                repeater: 's_flexibol_city_slider_items',
                repeaterSource: ['items', 'slides'],
                repeaterMap: {
                    title: ['s_flexibol_city_slider_item_title', 'title'],
                    text: ['s_flexibol_city_slider_item_text', 'text', 'description']
                }
            }
        },
        parking: {
            jsonKey: 'parking',
            layout: 's_flexibol_parking',
            fields: {
                simple: {
                    title: ['s_flexibol_parking_section_title', 'title', 'heading'],
                    subtitle: ['s_flexibol_parking_subtitle', 'subtitle', 'subheading'],
                    footer_text: ['s_flexibol_parking_footer_text', 'footer_text', 'bottom_text']
                },
                repeater: 's_flexibol_parking_cards',
                repeaterSource: ['items', 'cards'],
                repeaterMap: {
                    title: ['s_flexibol_parking_card_title', 'title'],
                    text: ['s_flexibol_parking_card_text', 'text', 'description'],
                    map_link: ['s_flexibol_parking_card_map_link', 'map_link', 'link', 'url'],
                    button_label: ['s_flexibol_parking_card_button_label', 'button_label', 'button_text']
                }
            }
        },
        our_experience: {
            jsonKey: 'our_experience',
            layout: 's_flexibol_our_experience',
            fields: {
                simple: {
                    title: ['s_flexibol_our_experience_title', 'title', 'heading']
                },
                repeater: 's_flexibol_our_experience_items',
                repeaterSource: ['items', 'slides', 'cards'],
                repeaterMap: {
                    title: ['s_flexibol_card_title', 'title'],
                    text: ['s_flexibol_card_text', 'text', 'description'],
                    image: ['s_flexibol_card_image', 'image']
                }
            }
        },
        price_table: {
            jsonKey: 'price_table',
            layout: 's_flexibol_price_table',
            fields: {
                simple: {
                    title: ['s_flexibol_price_table_section_title', 'title', 'heading'],
                    top_input: ['s_flexibol_price_top_input', 'top_input', 'top_label'],
                    top_image: ['s_flexibol_price_top_image', 'top_image'],

                    bb1_top: ['s_flexibol_price_bb1_top', 'bb1_top'],
                    bb1_middle: ['s_flexibol_price_bb1_middle', 'bb1_middle'],
                    bb1_extra: ['s_flexibol_price_bb1_extra', 'bb1_extra'],
                    bb1_price: ['s_flexibol_price_bb1_button_price', 'bb1_price'],
                    bb1_day: ['s_flexibol_price_bb1_button_day', 'bb1_day'],

                    bb2_top: ['s_flexibol_price_bb2_top', 'bb2_top'],
                    bb2_middle: ['s_flexibol_price_bb2_middle', 'bb2_middle'],
                    bb2_extra: ['s_flexibol_price_bb2_extra', 'bb2_extra'],
                    bb2_price: ['s_flexibol_price_bb2_button_price', 'bb2_price'],
                    bb2_day: ['s_flexibol_price_bb2_button_day', 'bb2_day'],

                    bb3_top: ['s_flexibol_price_bb3_top', 'bb3_top'],
                    bb3_middle: ['s_flexibol_price_bb3_middle', 'bb3_middle'],
                    bb3_extra: ['s_flexibol_price_bb3_extra', 'bb3_extra'],
                    bb3_price: ['s_flexibol_price_bb3_button_price', 'bb3_price'],
                    bb3_day: ['s_flexibol_price_bb3_button_day', 'bb3_day']
                },
                repeater: 's_flexibol_price_items',
                repeaterSource: ['items', 'rows'],
                repeaterMap: {
                    image_1: ['s_flexibol_price_image_1', 'image_1'],
                    title: ['s_flexibol_price_title', 'title'],
                    input: ['s_flexibol_price_input', 'input', 'subtitle_left'],
                    input_2: ['s_flexibol_price_input_2', 'input_2', 'subtitle_right'],
                    price: ['s_flexibol_price_item_price', 'price'],
                    night: ['s_flexibol_price_item_night', 'night', 'period'],
                    image_2: ['s_flexibol_price_image_2', 'image_2']
                }
            }
        },
        text_country: {
            jsonKey: 'text_country',
            layout: 's_flexibol_country_text',
            fields: {
                simple: {
                    title: ['s_flexibol_title', 'title', 'heading'],
                    text: ['s_flexibol_text', 'text', 'content'],
                    image: ['s_flexibol_image', 'image', 'image_1'],
                    text_2: ['s_flexibol_text_2', 'text_2', 'content_2']  
                }
            }
        },
        seasons_line: {
            jsonKey: 'seasons_line',
            layout: 's_flexibol_seasons_line',
            fields: {
                simple: {
                    title: ['s_flexibol_seasons_line_section_title', 'title', 'heading'],
                    pod_zag_pogoda: ['pod_zag_pogoda'],

                    january_title: ['s_flexibol_season_january_title', 'jan_title'],
                    january_subtitle: ['s_flexibol_season_january_subtitle', 'jan_subtitle'],
                    january_text: ['s_flexibol_season_january_short_text', 'jan_text'],

                    february_title: ['s_flexibol_season_february_title', 'feb_title'],
                    february_subtitle: ['s_flexibol_season_february_subtitle', 'feb_subtitle'],
                    february_text: ['s_flexibol_season_february_short_text', 'feb_text'],

                    march_title: ['s_flexibol_season_march_title', 'mar_title'],
                    march_subtitle: ['s_flexibol_season_march_subtitle', 'mar_subtitle'],
                    march_text: ['s_flexibol_season_march_short_text', 'mar_text'],

                    april_title: ['s_flexibol_season_april_title', 'apr_title'],
                    april_subtitle: ['s_flexibol_season_april_subtitle', 'apr_subtitle'],
                    april_text: ['s_flexibol_season_april_short_text', 'apr_text'],

                    may_title: ['s_flexibol_season_may_title', 'may_title'],
                    may_subtitle: ['s_flexibol_season_may_subtitle', 'may_subtitle'],
                    may_text: ['s_flexibol_season_may_short_text', 'may_text'],

                    june_title: ['s_flexibol_season_june_title', 'jun_title'],
                    june_subtitle: ['s_flexibol_season_june_subtitle', 'jun_subtitle'],
                    june_text: ['s_flexibol_season_june_short_text', 'jun_text'],

                    july_title: ['s_flexibol_season_july_title', 'jul_title'],
                    july_subtitle: ['s_flexibol_season_july_subtitle', 'jul_subtitle'],
                    july_text: ['s_flexibol_season_july_short_text', 'jul_text'],

                    august_title: ['s_flexibol_season_august_title', 'aug_title'],
                    august_subtitle: ['s_flexibol_season_august_subtitle', 'aug_subtitle'],
                    august_text: ['s_flexibol_season_august_short_text', 'aug_text'],

                    september_title: ['s_flexibol_season_september_title', 'sep_title'],
                    september_subtitle: ['s_flexibol_season_september_subtitle', 'sep_subtitle'],
                    september_text: ['s_flexibol_season_september_short_text', 'sep_text'],

                    october_title: ['s_flexibol_season_october_title', 'oct_title'],
                    october_subtitle: ['s_flexibol_season_october_subtitle', 'oct_subtitle'],
                    october_text: ['s_flexibol_season_october_short_text', 'oct_text'],

                    november_title: ['s_flexibol_season_november_title', 'nov_title'],
                    november_subtitle: ['s_flexibol_season_november_subtitle', 'nov_subtitle'],
                    november_text: ['s_flexibol_season_november_short_text', 'nov_text'],

                    december_title: ['s_flexibol_season_december_title', 'dec_title'],
                    december_subtitle: ['s_flexibol_season_december_subtitle', 'dec_subtitle'],
                    december_text: ['s_flexibol_season_december_short_text', 'dec_text']
                }
            }
        },
        /**
         * Маршрут на несколько дней в одном flexible-блоке:
         * — data.section_title (или section_title / title) → s_flexibol_route_section_title;
         * — data.days[] → репитер s_flexibol_route_days: badge, subtitle, timeline[], photos[].
         * Старый формат без days: один день из корня data (title + subtitle + badge + timeline + photos).
         * Заполнение только через importRouteOneDayBlock (не через fields.simple/repeaters).
         */
        route_one_day: {
            jsonKey: 'route_one_day',
            layout: 's_flexibol_route_one_day',
            aliases: ['rout-new', 'rout_new'],
            fields: {}
        },
        regions_comparison: {
            jsonKey: 'regions_comparison',
            layout: 's_flexibol_regions_comparison',
            fields: {
                simple: {
                    title: ['s_flexibol_regions_comparison_section_title', 'title', 'heading'],
                    comparison_of_regions_dop_text: [
                        'comparison_of_regions_dop_text',
                        'сomparison_of_regions_dop_text',
                        'dop_text'
                    ],
                    left_title: ['s_flexibol_regions_left_title', 'left_title', 'intro_title'],
                    left_subtitle: ['s_flexibol_regions_left_subtitle', 'left_subtitle', 'intro_subtitle'],
                    label_weather: ['s_flexibol_regions_label_weather', 'label_weather'],
                    label_entertainment: ['s_flexibol_regions_label_entertainment', 'label_entertainment'],
                    label_transport: ['s_flexibol_regions_label_transport', 'label_transport'],
                    label_kids: ['s_flexibol_regions_label_kids', 'label_kids'],
                    label_price: ['s_flexibol_regions_label_price', 'label_price']
                },
                repeater: 's_flexibol_regions_items',
                repeaterSource: ['items', 'regions', 'columns'],
                repeaterMap: {
                    city: ['s_flexibol_region_city_name', 'city', 'city_name', 'title'],
                    image: ['s_flexibol_region_image', 'image'],
                    weather: ['s_flexibol_region_weather', 'weather'],
                    entertainment: ['s_flexibol_region_entertainment', 'entertainment'],
                    transport: ['s_flexibol_region_transport', 'transport'],
                    kids: ['s_flexibol_region_kids', 'kids'],
                    price: ['s_flexibol_region_price', 'price']
                }
            }
        },
        where_to_stay: {
            jsonKey: 'where_to_stay',
            layout: 's_flexibol_where_to_stay',
            fields: {
                simple: {
                    title: ['s_flexibol_where_stay_section_title', 'title', 'heading'],
                    lead_text: [
                        's_flexibol_where_stay_lead_text',
                        'lead_text',
                        'subtitle',
                        's_flexibol_where_stay_subtitle',
                        'intro',
                        'text'
                    ]
                },
                repeater: 's_flexibol_where_stay_cards',
                repeaterSource: ['items', 'cards'],
                repeaterMap: {
                    title: ['s_flexibol_where_stay_card_title', 'title'],
                    text: ['s_flexibol_where_stay_card_text', 'text', 'description']
                }
            }
        },
        tourist_reviews: {
            jsonKey: 'tourist_reviews',
            layout: 's_flexibol_tourist_reviews',
            fields: {
                simple: {
                    title: ['s_flexibol_tourist_block_title', 'title', 'heading', 'block_title']
                },
                repeater: 's_flexibol_tourist_items',
                repeaterSource: ['items', 'reviews'],
                repeaterMap: {
                    photo: ['s_flexibol_tourist_photo', 'photo', 'image', 'avatar'],
                    name: ['s_flexibol_tourist_name', 'name', 'author'],
                    text: ['s_flexibol_tourist_text', 'text', 'review', 'quote']
                }
            }
        },
        editor: {
            jsonKey: 'editor',
            layout: 's_flexibol_editor',
            fields: {
                simple: {
                    content: ['s_flexibol_editor', 'content', 'html', 'text']
                }
            }
        }
    };

    function listKnownLayouts() {
        var parts = [];
        for (var key in SECTION_CONFIG) {
            if (!Object.prototype.hasOwnProperty.call(SECTION_CONFIG, key)) continue;
            var c = SECTION_CONFIG[key];
            parts.push(c.jsonKey + ' → ' + c.layout);
        }
        return parts.join(' · ');
    }

    function log(message, type) {
        type = type || 'info';
        var icon = type === 'error' ? '❌' : (type === 'success' ? '✅' : (type === 'warn' ? '⚠️' : '➡️'));
        var color = type === 'error' ? '#b32d2e' : (type === 'success' ? '#00a32a' : (type === 'warn' ? '#dba617' : '#2271b1'));
        var line = icon + ' ' + message;
        if (type === 'error') {
            console.error('[AI Importer]', message);
        } else if (type === 'warn') {
            console.warn('[AI Importer]', message);
        } else {
            console.log('[AI Importer]', message);
        }
        var $log = $('#my-acf-ai-importer-log');
        if ($log.length) {
            $log.show();
            $log.append('<div style="color:' + color + ';margin-bottom:4px;">' + escapeHtml(line) + '</div>');
            if ($log[0]) {
                $log.scrollTop($log[0].scrollHeight);
            }
        }
    }

    function escapeHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    var INVISIBLE_CHARS_RE = /[\uFEFF\u200B-\u200F\u202A-\u202E\u2060-\u2069]/g;
    var SKIP_CLEAN_KEY_RE = /(?:^|_)(url|link|email|phone|tel|href|image|photo|avatar|insta|telegram|whatsapp|yotube|viber)(?:$|_)/i;

    function shouldSkipCleanValue(key) {
        return !!key && SKIP_CLEAN_KEY_RE.test(String(key));
    }

    function decodeHtmlEntities(str) {
        var ta = document.createElement('textarea');
        ta.innerHTML = str;
        return ta.value;
    }

    function stripLiteralEntities(str) {
        return str
            .replace(/&rlm;/gi, '')
            .replace(/&amp;rlm;/gi, '')
            .replace(/&#0*8207;/gi, '')
            .replace(/&#x200f;/gi, '')
            .replace(/&#0*xfeff;/gi, '')
            .replace(/&nbsp;/gi, ' ');
    }

    function stripAccidentalHtml(str) {
        return str
            .replace(/<br\s*\/?>/gi, '\n')
            .replace(/<\/?(p|div|span|bdi|strong|em|b|i)\b[^>]*>/gi, '')
            .replace(/<[^>]+>/g, '');
    }

    function normalizeCleanWhitespace(str) {
        return str
            .replace(/\r\n/g, '\n')
            .replace(/\r/g, '\n')
            .replace(/[ \t]+\n/g, '\n')
            .replace(/\n{3,}/g, '\n\n')
            .replace(/[ \t]{2,}/g, ' ')
            .trim();
    }

    function countInvisibleChars(str) {
        var n = 0;
        var m = str.match(INVISIBLE_CHARS_RE);
        if (m) n += m.length;
        return n;
    }

    function cleanTextValue(str, key) {
        if (typeof str !== 'string' || str === '') {
            return { value: str, changed: false, removed: 0, issues: [] };
        }
        if (shouldSkipCleanValue(key)) {
            return { value: str, changed: false, removed: 0, issues: [] };
        }

        var original = str;
        var issues = [];
        var removed = countInvisibleChars(str);

        if (removed > 0) {
            issues.push('невидимые символы (' + removed + ')');
        }
        if (/&rlm;|&amp;rlm;|&#0*8207;|&#x200f;|&#0*xfeff;|&nbsp;/i.test(original)) {
            issues.push('HTML-сущности');
        }
        if (/<[a-z][\s\S]*?>/i.test(original)) {
            issues.push('HTML-теги');
        }
        if (/[\u201C\u201D\u2018\u2019]/.test(original)) {
            issues.push('«умные» кавычки');
        }
        if (/\r|\n{3,}|[ \t]{2,}/.test(original)) {
            issues.push('лишние пробелы/переносы');
        }

        var value = decodeHtmlEntities(str);
        value = stripLiteralEntities(value);
        value = value.replace(INVISIBLE_CHARS_RE, '');
        value = value.replace(/[\u201C\u201D]/g, '"').replace(/[\u2018\u2019]/g, "'");
        value = stripAccidentalHtml(value);
        value = normalizeCleanWhitespace(value);

        removed += Math.max(0, original.length - value.length);

        return {
            value: value,
            changed: value !== original,
            removed: removed,
            issues: issues
        };
    }

    function buildCleanFieldPath(basePath, key) {
        if (!basePath) {
            return String(key);
        }
        if (typeof key === 'number' || /^\d+$/.test(String(key))) {
            return basePath + '[' + key + ']';
        }
        return basePath + '.' + key;
    }

    function cleanJsonValueDeep(node, key, stats, path) {
        var currentPath = path ? buildCleanFieldPath(path, key) : String(key || '');

        if (typeof node === 'string') {
            var cleaned = cleanTextValue(node, key);
            if (cleaned.changed) {
                stats.fieldsChanged += 1;
                stats.details.push({
                    path: currentPath,
                    issues: cleaned.issues,
                    removed: cleaned.removed
                });
            }
            stats.charsRemoved += cleaned.removed;
            stats.invisibleRemoved += countInvisibleChars(node);
            if (/&rlm;|&amp;rlm;|&#0*8207;/i.test(node)) {
                stats.entitiesFound += 1;
            }
            if (/<[a-z]/i.test(node)) {
                stats.htmlFound += 1;
            }
            return cleaned.value;
        }
        if (Array.isArray(node)) {
            return node.map(function (item, index) {
                return cleanJsonValueDeep(item, index, stats, currentPath);
            });
        }
        if (node && typeof node === 'object') {
            var out = {};
            Object.keys(node).forEach(function (childKey) {
                out[childKey] = cleanJsonValueDeep(node[childKey], childKey, stats, currentPath);
            });
            return out;
        }
        return node;
    }

    function createCleanStats() {
        return {
            fieldsChanged: 0,
            charsRemoved: 0,
            invisibleRemoved: 0,
            entitiesFound: 0,
            htmlFound: 0,
            rawIssues: [],
            details: []
        };
    }

    function cleanRawJsonText(raw, stats) {
        var text = String(raw || '').trim();
        var original = text;

        if (/^\uFEFF/.test(text)) {
            text = text.replace(/^\uFEFF+/, '');
            stats.rawIssues.push('BOM в начале файла');
        }

        var fenced = text.match(/^```(?:json)?\s*([\s\S]*?)```\s*$/i);
        if (fenced) {
            text = fenced[1].trim();
            stats.rawIssues.push('обёртка ```json ... ```');
        }

        var firstBrace = text.indexOf('{');
        var lastBrace = text.lastIndexOf('}');
        if (firstBrace > 0 || (lastBrace >= 0 && lastBrace < text.length - 1)) {
            if (firstBrace >= 0 && lastBrace > firstBrace) {
                var trimmed = text.slice(firstBrace, lastBrace + 1);
                if (trimmed !== text) {
                    stats.rawIssues.push('лишний текст вокруг JSON');
                    text = trimmed;
                }
            }
        }

        if (text !== original && stats.rawIssues.length === 0) {
            stats.rawIssues.push('нормализация пробелов вокруг JSON');
        }

        return text;
    }

    function formatCleanAlertMessage(result) {
        var stats = result.stats;
        var lines = ['Чистка JSON завершена', ''];

        if (stats.rawIssues.length) {
            lines.push('До разбора:');
            stats.rawIssues.forEach(function (issue) {
                lines.push('• ' + issue);
            });
            lines.push('');
        }

        lines.push('Итого:');
        lines.push('• изменено полей: ' + stats.fieldsChanged);
        lines.push('• убрано символов: ' + stats.charsRemoved);
        lines.push('• невидимых символов: ' + stats.invisibleRemoved);
        lines.push('• полей с HTML-сущностями: ' + stats.entitiesFound);
        lines.push('• полей с HTML-тегами: ' + stats.htmlFound);

        if (stats.details.length) {
            lines.push('');
            lines.push('Изменённые поля (до ' + Math.min(stats.details.length, 12) + '):');
            stats.details.slice(0, 12).forEach(function (item) {
                var issueText = item.issues.length ? item.issues.join(', ') : 'нормализация';
                lines.push('• ' + item.path + ' — ' + issueText);
            });
            if (stats.details.length > 12) {
                lines.push('• … и ещё ' + (stats.details.length - 12) + ' полей');
            }
        } else if (!stats.rawIssues.length) {
            lines.push('');
            lines.push('Мусор не найден — JSON уже чистый.');
        }

        lines.push('');
        lines.push('Нажмите OK, затем «Запустить импорт».');

        return lines.join('\n');
    }

    function prepareJsonFromTextarea(updateTextarea) {
        var raw = $('#ai-json').val() || '';
        if (!String(raw).trim()) {
            return { error: new Error('Текстовое поле JSON пустое — вставьте объект с ключом sections.') };
        }

        var stats = createCleanStats();
        var cleanedRaw = cleanRawJsonText(raw, stats);
        var parsed;

        try {
            parsed = JSON.parse(cleanedRaw);
        } catch (e) {
            return { error: e, stats: stats };
        }

        parsed = cleanJsonValueDeep(parsed, '', stats, '');
        var pretty = JSON.stringify(parsed, null, 2);

        if (updateTextarea !== false) {
            $('#ai-json').val(pretty);
        }

        return {
            parsed: parsed,
            stats: stats,
            cleanedRaw: pretty
        };
    }

    function toArray(v) { return Array.isArray(v) ? v : [v]; }

    function normalizeImportKey(k) {
        return String(k || '')
            .toLowerCase()
            .replace(/\u0441/g, 'c');
    }

    function getValueByKeys(obj, keys) {
        obj = obj || {};
        var list = toArray(keys);
        for (var i = 0; i < list.length; i++) {
            if (obj[list[i]] !== undefined) return obj[list[i]];
        }
        var wanted = list.map(normalizeImportKey).filter(Boolean);
        for (var k in obj) {
            if (!Object.prototype.hasOwnProperty.call(obj, k)) continue;
            if (wanted.indexOf(normalizeImportKey(k)) !== -1) return obj[k];
        }
        return '';
    }

    /** Первый ключ из списка, значение которого — массив (для timeline, photos и т.д.). */
    function getFirstArrayFromObject(obj, keys) {
        obj = obj || {};
        var list = toArray(keys);
        for (var i = 0; i < list.length; i++) {
            var v = obj[list[i]];
            if (Array.isArray(v)) {
                return v;
            }
        }
        return [];
    }

    function findLiveAcfField($context, fieldNames) {
        var names = toArray(fieldNames);
        for (var i = 0; i < names.length; i++) {
            var name = names[i];
            if (!name) continue;
            var $fields = $context.find('.acf-field[data-name="' + name + '"], .acf-field[data-key="' + name + '"]').filter(function () {
                return $(this).closest('.acf-clone').length === 0;
            });
            if ($fields.length) return $fields.first();
        }
        return $();
    }

    async function waitForField($context, fieldNames, timeout) {
        timeout = timeout || 2500;
        var start = Date.now();
        while (Date.now() - start < timeout) {
            var $field = findLiveAcfField($context, fieldNames);
            if ($field.length) return $field;
            await new Promise(function (r) { setTimeout(r, 100); });
        }
        return null;
    }

    async function setFieldValue($context, fieldNames, value, warnTag) {
        var names = toArray(fieldNames);
        var $field = await waitForField($context, fieldNames);
        if (!$field) {
            if (warnTag) {
                log('Поле не найдено в DOM: ' + warnTag + ' (ожидалось data-name: ' + names[0] + ')', 'warn');
            }
            return false;
        }
        var fType = ($field.attr('data-type') || '').toLowerCase();
        if (fType === 'image' && value && /^https?:\/\//i.test(String(value)) && !imageUrlHintShown) {
            imageUrlHintShown = true;
            log('Поле Image: если значение не применилось, в ACF часто нужен attachment_id (число из медиатеки), а не URL.', 'warn');
        }
        var outVal = value == null ? '' : value;
        var $input;
        if (fType === 'textarea' || fType === 'wysiwyg') {
            $input = $field.find('textarea').first();
        } else {
            $input = $field.find('input, textarea, select').filter(':not([type="hidden"])').first();
            if (!$input.length) {
                $input = $field.find('input, textarea, select').first();
            }
        }
        if ($input.length) {
            $input.val(outVal).trigger('input').trigger('change');
        }
        var fieldObj = acf.getField($field);
        if (fieldObj && typeof fieldObj.val === 'function') {
            fieldObj.val(outVal);
        }
        if (fType === 'wysiwyg') {
            var $ta = $field.find('textarea').first();
            var id = $ta.attr('id');
            if (window.tinymce && id && tinymce.get(id)) {
                tinymce.get(id).setContent(String(outVal));
                tinymce.get(id).save();
            }
        }
        return true;
    }

    async function fillLayoutByMatchingJsonKeys($layout, data) {
        if (!data || typeof data !== 'object') return;
        var $fields = $layout.find('.acf-fields').first().children('.acf-field[data-name]');
        if (!$fields.length) {
            $fields = $layout.find('> .acf-fields > .acf-field[data-name]');
        }
        for (var i = 0; i < $fields.length; i++) {
            var $f = $($fields[i]);
            var fType = ($f.attr('data-type') || '').toLowerCase();
            if (fType === 'repeater' || fType === 'flexible_content' || fType === 'group') continue;
            var name = $f.attr('data-name');
            if (!name) continue;
            var val = getValueByKeys(data, [name]);
            if (val === '' || val == null || typeof val === 'object') continue;
            var ok = await setFieldValue($layout, [name], val, 'прямое поле «' + name + '»');
            if (ok) {
                log('Заполнено поле «' + name + '»', 'success');
            }
        }
    }

    function dismissAcfOverlays() {
        try {
            $(document).trigger($.Event('keydown', { key: 'Escape', keyCode: 27, which: 27 }));
            $(document).trigger($.Event('keyup', { key: 'Escape', keyCode: 27, which: 27 }));
            $(document.body).trigger('click');
        } catch (e) {}

        try {
            if (typeof acf !== 'undefined') {
                if (typeof acf.hideTooltip === 'function') {
                    acf.hideTooltip();
                }
                if (typeof acf.unlockForm === 'function') {
                    acf.unlockForm($('#post'));
                }
                if (typeof acf.enable === 'function') {
                    acf.enable($('#post'));
                    acf.enable($(document.body));
                }
                if (typeof acf.enableForm === 'function') {
                    acf.enableForm($('#post'));
                }
            }
        } catch (e) {}

        $('body > .acf-fc-popup, .acf-fc-popup, body > .acf-tooltip, .acf-tooltip, .acf-tooltip.-confirm, .acf-confirm, .acf-more-layout-actions, .acf-fc-popup-backdrop').remove();
        $('.acf-popup, #acf-popup, .acf-popup-backdrop, [data-name="acf-popup"]').remove();
        $('html, body').removeClass('modal-open acf-modal-open acf-loading');
        $('#post, #poststuff, #submitdiv, #publishing-action').css('pointer-events', 'auto');
        $('#publish, #save-post, #post-preview').prop('disabled', false).removeClass('disabled button-disabled').css({
            'pointer-events': 'auto',
            'position': 'relative',
            'z-index': 100050
        });
    }

    async function findOrCreateLayout(layoutName, logFn) {
        logFn = logFn || log;
        var $existing = $('.layout[data-layout="' + layoutName + '"]').not('.acf-clone').first();
        if ($existing.length) {
            logFn('Макет уже есть на странице: ' + layoutName, 'step');
            return $existing;
        }
        var $flex = $('.acf-field-flexible-content').first();
        if (!$flex.length) {
            logFn('На странице нет ACF Flexible Content. Откройте нужный тип записи и поле flex, обновите страницу.', 'error');
            return null;
        }
        if (typeof acf === 'undefined') {
            logFn('Глобальный объект acf не определён. Проверьте, что ACF загружен в админке.', 'error');
            return null;
        }
        var $add = $flex.find('.acf-actions .button, [data-event="add-layout"]').filter(':visible').last();
        if (!$add.length) {
            logFn('Не найдена кнопка «Добавить» у Flexible Content (возможно поле свёрнуто или нет прав).', 'error');
            return null;
        }
        $add.trigger('click');
        await new Promise(function (r) { setTimeout(r, 600); });
        var $layoutLink = $('body').find('.acf-fc-popup a[data-layout="' + layoutName + '"], .acf-fc-list a[data-layout="' + layoutName + '"]').first();
        if (!$layoutLink.length) {
            dismissAcfOverlays();
            logFn('Макет «' + layoutName + '» нет в списке добавления. Добавьте этот layout в группу полей ACF для данного типа записи.', 'error');
            return null;
        }
        $layoutLink.trigger('click');
        await new Promise(function (r) { setTimeout(r, 1000); });
        dismissAcfOverlays();
        var $newLay = $('.layout[data-layout="' + layoutName + '"]').not('.acf-clone').last();
        if (!$newLay.length) {
            logFn('Макет «' + layoutName + '» не появился после клика. Разверните все группы ACF и попробуйте снова.', 'error');
            return null;
        }
        logFn('Создан/открыт макет: ' + layoutName, 'success');
        return $newLay;
    }

    async function fillRepeaterRowsIn($root, repeaterName, rows, repeaterMap) {
        var $rep = $root.find('.acf-field[data-name="' + repeaterName + '"]').first();
        if (!$rep.length) {
            log('Репитер не найден: «' + repeaterName + '». Разверните блок или проверьте имя поля.', 'error');
            return 0;
        }
        var repField = acf.getField($rep);
        if (!repField) {
            log('ACF не отдал объект поля для репитера «' + repeaterName + '». Подождите загрузку и повторите.', 'warn');
            return 0;
        }
        var $rows = repField.$el.find('.acf-row:not(.acf-clone)');
        $rows.each(function () {
            $(this).remove();
        });
        dismissAcfOverlays();
        if (!Array.isArray(rows)) {
            log('Для репитера «' + repeaterName + '» ожидался массив строк, получено: ' + typeof rows, 'warn');
            rows = [];
        }
        for (var i = 0; i < rows.length; i++) {
            var item = rows[i] || {};
            repField.add();
            await new Promise(function (r) { setTimeout(r, 400); });
            var $row = repField.$el.find('.acf-row:not(.acf-clone)').last();
            if (!$row.length) {
                log('Строка репитера «' + repeaterName + '» не создалась после add() (строка ' + (i + 1) + ').', 'error');
                continue;
            }
            for (var k in repeaterMap) {
                if (!Object.prototype.hasOwnProperty.call(repeaterMap, k)) continue;
                var aliases = repeaterMap[k];
                var val = getValueByKeys(item, aliases);
                var tag = '«' + repeaterName + '» строка ' + (i + 1) + ', подполе «' + k + '»';
                await setFieldValue($row, aliases, val, tag);
            }
        }
        log('Репитер «' + repeaterName + '»: обработано строк — ' + rows.length, 'success');
        return rows.length;
    }

    async function fillRepeaterRows($layout, repeaterName, rows, repeaterMap) {
        return fillRepeaterRowsIn($layout, repeaterName, rows, repeaterMap);
    }

    /**
     * Маршрут: общий заголовок + репитер дней (таймлайн и фото внутри каждого дня).
     */
    async function importRouteOneDayBlock($layout, data) {
        data = data || {};
        log(
            'Маршрут: логика v2 — сначала репитер «s_flexibol_route_days», таймлайн/фото внутри каждой строки дня (не s_flexibol_route_left_*).',
            'step'
        );
        await setFieldValue(
            $layout,
            ['s_flexibol_route_section_title', 'section_title', 'route_title', 'title', 'heading'],
            getValueByKeys(data, ['s_flexibol_route_section_title', 'section_title', 'route_title', 'title', 'heading']),
            'маршрут: общий заголовок'
        );
        await setFieldValue(
            $layout,
            ['rout_dop_text'],
            getValueByKeys(data, ['rout_dop_text']),
            'маршрут: текст под заголовком'
        );

        var days = data.days;
        if (!Array.isArray(days) || days.length === 0) {
            days = [{
                badge: data.badge,
                subtitle: data.subtitle,
                timeline: getFirstArrayFromObject(data, ['timeline', 'schedule', 'stops', 'items']),
                photos: getFirstArrayFromObject(data, ['photos', 'images'])
            }];
        }

        var $daysRep = $layout.find('.acf-field[data-name="s_flexibol_route_days"]').first();
        var daysField = acf.getField($daysRep);
        if (!daysField) {
            log('Не найден репитер «s_flexibol_route_days». Обновите поля ACF в группе flexible.', 'error');
            return;
        }
        daysField.$el.find('.acf-row:not(.acf-clone)').remove();
        dismissAcfOverlays();
        await new Promise(function (r) { setTimeout(r, 250); });

        for (var di = 0; di < days.length; di++) {
            var d = days[di] || {};
            daysField.add();
            await new Promise(function (r) { setTimeout(r, 600); });
            var $dayRow = daysField.$el.find('.acf-row:not(.acf-clone)').last();
            if (!$dayRow.length) {
                log('Строка дня ' + (di + 1) + ' не создалась.', 'error');
                continue;
            }

            await setFieldValue(
                $dayRow,
                ['s_flexibol_route_day_badge', 'badge', 'day_badge'],
                getValueByKeys(d, ['s_flexibol_route_day_badge', 'badge', 'day_badge']),
                'день ' + (di + 1) + ': бейдж'
            );
            await setFieldValue(
                $dayRow,
                ['s_flexibol_route_day_subtitle', 'subtitle', 'day_title', 'heading'],
                getValueByKeys(d, ['s_flexibol_route_day_subtitle', 'subtitle', 'day_title', 'heading']),
                'день ' + (di + 1) + ': подзаголовок'
            );

            var tl = getFirstArrayFromObject(d, ['timeline', 'schedule', 'stops', 'items']);
            await fillRepeaterRowsIn($dayRow, 's_flexibol_route_day_timeline', tl, {
                time: ['s_flexibol_route_time', 'time', 'hour'],
                text: ['s_flexibol_route_text', 'text', 'description']
            });

            var ph = getFirstArrayFromObject(d, ['photos', 'images']);
            await fillRepeaterRowsIn($dayRow, 's_flexibol_route_day_photos', ph, {
                photo: ['s_flexibol_route_photo', 'photo', 'image', 'url']
            });
        }
        log('Маршрут: заполнено дней — ' + days.length, 'success');
    }

    function resolveConfig(section) {
        var layoutKey = (section.layout || '')
            .toLowerCase()
            .trim();
        var sectionKey = (section.key || '')
            .toLowerCase()
            .trim();
        var layoutKeyNoPrefix = layoutKey.replace(/^section_/, '');
        var sectionKeyNoPrefix = sectionKey.replace(/^section_/, '');
        var candidateKeys = [layoutKey, sectionKey, layoutKeyNoPrefix, sectionKeyNoPrefix]
            .filter(function (v, i, arr) {
                return v && arr.indexOf(v) === i;
            });
    
        for (var key in SECTION_CONFIG) {
            if (!Object.prototype.hasOwnProperty.call(SECTION_CONFIG, key)) continue;
            var cfg = SECTION_CONFIG[key];
            var aliases = cfg.aliases || [];
            var cfgCandidates = [cfg.layout, cfg.jsonKey].concat(aliases)
                .map(function (v) { return String(v || '').toLowerCase().trim(); })
                .filter(function (v, i, arr) { return v && arr.indexOf(v) === i; });
            var prefixedCfgCandidates = cfgCandidates
                .map(function (v) {
                    return v.indexOf('section_') === 0 ? v : ('section_' + v);
                });
            cfgCandidates = cfgCandidates.concat(prefixedCfgCandidates)
                .filter(function (v, i, arr) { return arr.indexOf(v) === i; });
    
            if (candidateKeys.some(function (candidate) {
                return cfgCandidates.includes(candidate);
            })) {
                return cfg;
            }
        }
    
        return null;
    }

    /**
     * Проверки данных до заливки в ACF — понятные «ошибки» в логе.
     */
    function validateSectionData(cfg, data) {
        data = data || {};
        if (cfg.jsonKey === 'tourist_reviews') {
            var rs = cfg.fields.repeaterSource ? toArray(cfg.fields.repeaterSource) : ['items'];
            var rows = null;
            for (var ri = 0; ri < rs.length; ri++) {
                if (data[rs[ri]] !== undefined) {
                    rows = data[rs[ri]];
                    break;
                }
            }
            if (rows === null) {
                log('Отзывы туристов: в «data» нет массива отзывов. Добавьте ключ «items» или «reviews»: [ { "name": "...", "text": "..." }, ... ]', 'error');
                return;
            }
            if (!Array.isArray(rows)) {
                log('Отзывы туристов: «items»/«reviews» должны быть массивом [], а не ' + typeof rows + '.', 'error');
                return;
            }
            if (rows.length === 0) {
                log('Отзывы туристов: массив отзывов пуст. В блоке ACF нажмите «Добавить отзыв» через импорт — нужен хотя бы один элемент.', 'error');
                return;
            }
            var map = cfg.fields.repeaterMap || {};
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i] || {};
                var nameVal = getValueByKeys(row, map.name || ['name']);
                var textVal = getValueByKeys(row, map.text || ['text']);
                var nameOk = nameVal != null && String(nameVal).trim() !== '';
                var textOk = textVal != null && String(textVal).trim() !== '';
                if (!nameOk && !textOk) {
                    log('Отзывы туристов: элемент ' + (i + 1) + ' — пустые «Имя» и «Текст отзыва». Вёрстка может пропустить такую карточку.', 'warn');
                } else if (!textOk) {
                    log('Отзывы туристов: элемент ' + (i + 1) + ' — пустой текст отзыва (поле text/review).', 'warn');
                } else if (!nameOk) {
                    log('Отзывы туристов: элемент ' + (i + 1) + ' — пустое имя (поле name).', 'warn');
                }
            }
            var titleVal = cfg.fields.simple && cfg.fields.simple.title
                ? getValueByKeys(data, cfg.fields.simple.title)
                : '';
            if (!titleVal || String(titleVal).trim() === '') {
                log('Отзывы туристов: «Заголовок блока» пуст (задайте title или block_title). Блок на сайте показывается, если есть отзывы.', 'warn');
            }
            log('Отзывы туристов: проверка OK — ' + rows.length + ' отзыв(ов) в JSON.', 'success');
            return;
        }
        if (cfg.fields.repeater && cfg.fields.repeaterSource) {
            var rlist = getValueByKeys(data, cfg.fields.repeaterSource);
            if (rlist !== '' && rlist !== undefined && !Array.isArray(rlist)) {
                log('Секция «' + cfg.jsonKey + '»: для репитера ожидался массив в «items» (или альтернативном ключе), получено: ' + typeof rlist + '.', 'error');
            }
        }
        if (cfg.jsonKey === 'route_one_day') {
            if (Array.isArray(data.days) && data.days.length > 0) {
                log('Маршрут: «days» — ' + data.days.length + ' дн., заголовок секции из section_title / title.', 'step');
            } else {
                var hasLegacy = data.timeline || data.schedule || data.badge || data.subtitle || data.photos;
                if (hasLegacy) {
                    log('Маршрут: формат одного дня из корня data (без массива days).', 'step');
                } else {
                    log('Маршрут: нет «days» и нет полей одного дня — проверьте JSON.', 'warn');
                }
            }
        }
    }

    async function runImport(json) {
        try {
        if (!json || (typeof json !== 'object')) {
            log('Некорректный корень JSON (ожидается объект).', 'error');
            return;
        }
        if (!json.sections && json.layout) {
            json = { sections: [json] };
            log('JSON без ключа sections — обёрнуто в одну секцию.', 'step');
        }
        var sections = json.sections || [];
        if (!Array.isArray(sections)) {
            log('Ключ sections должен быть массивом объектов { layout, data }.', 'error');
            return;
        }
        if (sections.length === 0) {
            log('Массив sections пуст — нечего импортировать.', 'warn');
            return;
        }

        log('Старт импорта, секций в очереди: ' + sections.length, 'step');
        imageUrlHintShown = false;

        var done = 0;
        var skipped = 0;
        for (var i = 0; i < sections.length; i++) {
            var section = sections[i] || {};
            var layoutKey = section.layout || section.key || '';
            log('— Секция ' + (i + 1) + '/' + sections.length + ', layout в JSON: «' + layoutKey + '»', 'step');

            var cfg = resolveConfig(section);
            if (!cfg && typeof layoutKey === 'string') {
                var normalizedLayout = layoutKey.toLowerCase().trim().replace(/^section_/, '');
                if (normalizedLayout && normalizedLayout !== layoutKey.toLowerCase().trim()) {
                    cfg = resolveConfig({
                        layout: normalizedLayout,
                        key: section.key,
                        data: section.data
                    });
                    if (cfg) {
                        log('Распознан alias layout: «' + layoutKey + '» → «' + normalizedLayout + '».', 'step');
                    }
                }
            }
            if (!cfg) {
                skipped++;
                log('Неизвестный layout «' + layoutKey + '». Укажите jsonKey из списка или системное имя ACF. Известно: ' + listKnownLayouts(), 'error');
                continue;
            }

            var $layout = await findOrCreateLayout(cfg.layout, log);
            if (!$layout) {
                skipped++;
                log('Секция «' + cfg.jsonKey + '» пропущена: макет ACF «' + cfg.layout + '» не удалось открыть.', 'error');
                continue;
            }
            if ($layout.hasClass('-collapsed')) {
                $layout.find('.acf-fc-layout-handle, [data-name="collapse-layout"]').first().trigger('click');
                await new Promise(function (r) { setTimeout(r, 400); });
            }

            var data = section.data || {};
            if (!data || typeof data !== 'object') {
                log('У секции «' + cfg.jsonKey + '» нет объекта data — пропуск заполнения полей.', 'warn');
            } else {
                log('Ключи JSON data: ' + Object.keys(data).join(', '), 'step');
                validateSectionData(cfg, data);
            }

            if (cfg.jsonKey === 'route_one_day') {
                await importRouteOneDayBlock($layout, data);
            } else {
                if (cfg.fields.simple) {
                    for (var sk in cfg.fields.simple) {
                        if (!Object.prototype.hasOwnProperty.call(cfg.fields.simple, sk)) continue;
                        var simpleAliases = cfg.fields.simple[sk];
                        var tagSimple = 'секция «' + cfg.jsonKey + '», простое поле «' + sk + '»';
                        await setFieldValue($layout, simpleAliases, getValueByKeys(data, simpleAliases.concat([sk])), tagSimple);
                    }
                }

                await fillLayoutByMatchingJsonKeys($layout, data);

                if (cfg.fields.repeater && cfg.fields.repeaterMap) {
                    var rows = cfg.fields.repeaterSource ? getValueByKeys(data, cfg.fields.repeaterSource) : data.items;
                    await fillRepeaterRows($layout, cfg.fields.repeater, rows, cfg.fields.repeaterMap);
                }

                if (cfg.fields.repeaters && cfg.fields.repeaters.length) {
                    for (var ri = 0; ri < cfg.fields.repeaters.length; ri++) {
                        var rp = cfg.fields.repeaters[ri];
                        if (!rp || !rp.repeater || !rp.repeaterMap) continue;
                        var rrows = rp.repeaterSource ? getValueByKeys(data, rp.repeaterSource) : data.items;
                        await fillRepeaterRows($layout, rp.repeater, rrows, rp.repeaterMap);
                    }
                }
            }

            done++;
            log('Секция завершена: «' + cfg.jsonKey + '» (ACF layout: ' + cfg.layout + ')', 'success');
        }

        if (skipped === 0) {
            log('Итог: успешно обработано секций — ' + done + ' из ' + sections.length + '. Сохраните запись (Обновить).', 'success');
        } else {
            log('Итог: успешно — ' + done + ', с ошибками/пропусками — ' + skipped + ' из ' + sections.length + '. Проверьте лог и консоль F12.', 'warn');
        }
        } finally {
            dismissAcfOverlays();
        }
    }

    function init() {
        if ($('#my-acf-ai-importer-box').length) return;
        if (!$('#my-acf-ai-importer-style').length) {
            $('head').append(
                '<style id="my-acf-ai-importer-style">' +
                '#my-acf-ai-importer-box{margin:16px 0;max-width:100%;}' +
                '#my-acf-ai-importer-box .hndle{padding:10px 12px;margin:0;border-bottom:1px solid #dcdcde;}' +
                '#my-acf-ai-importer-box .inside{padding:12px;}' +
                '#my-acf-ai-importer-box #ai-json{width:100%;min-height:180px;resize:vertical;box-sizing:border-box;font-family:monospace;}' +
                '#my-acf-ai-importer-box .ai-importer-actions{margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;}' +
                '#my-acf-ai-importer-box #ai-clean-btn{background:#00a32a;border-color:#00a32a;color:#fff;}' +
                '#my-acf-ai-importer-box #ai-clean-btn:hover,#my-acf-ai-importer-box #ai-clean-btn:focus{background:#008a20;border-color:#008a20;color:#fff;}' +
                '#my-acf-ai-importer-box #my-acf-ai-importer-log{display:none;margin-top:10px;max-height:220px;overflow:auto;background:#f6f7f7;padding:10px;border:1px solid #dcdcde;font-family:monospace;font-size:12px;line-height:1.4;}' +
                '#submitdiv,#publishing-action,#publish,#save-post{position:relative;z-index:100060;pointer-events:auto !important;}' +
                '</style>'
            );
        }

        var $box = $(
            '<div id="my-acf-ai-importer-box" class="postbox">' +
                '<h2 class="hndle"><span>AI Importer (Flexibol)</span></h2>' +
                '<div class="inside">' +
                    '<textarea id="ai-json" placeholder="Вставьте JSON сюда..."></textarea>' +
                    '<p class="ai-importer-actions">' +
                        '<button type="button" class="button button-primary" id="ai-btn">Запустить импорт</button>' +
                        '<button type="button" class="button" id="ai-clean-btn">Чистка</button>' +
                    '</p>' +
                    '<div id="my-acf-ai-importer-log"></div>' +
                '</div>' +
            '</div>'
        );

        var $target = $('#poststuff');
        if (!$target.length) $target = $('.edit-post-layout__metaboxes');
        if (!$target.length) $target = $('.wrap');
        if (!$target.length) $target = $('body');
        $target.first().append($box);

        $(document).off('click', '#ai-clean-btn').on('click', '#ai-clean-btn', function () {
            var result = prepareJsonFromTextarea(true);
            if (result.error) {
                window.alert('Ошибка чистки\n\n' + result.error.message);
                return;
            }
            window.alert(formatCleanAlertMessage(result));
        });

        $(document).off('click', '#ai-btn').on('click', '#ai-btn', function () {
            var raw = ($('#ai-json').val() || '').trim();
            $('#my-acf-ai-importer-log').empty();
            if (!raw) {
                log('Текстовое поле JSON пустое — вставьте объект с ключом sections.', 'error');
                return;
            }
            if (typeof acf === 'undefined') {
                log('ACF не загружен (window.acf). Откройте экран редактирования записи с полями ACF и обновите страницу.', 'error');
                return;
            }
            var parsed;
            try {
                parsed = JSON.parse(raw);
            } catch (e) {
                log('Ошибка разбора JSON: ' + e.message + '. Проверьте запятые, кавычки и лишние символы в конце.', 'error');
                return;
            }
            runImport(parsed).catch(function (err) {
                log('Сбой во время импорта: ' + (err && err.message ? err.message : String(err)), 'error');
            });
        });
    }

    $(document).ready(init);
    if (typeof acf !== 'undefined' && acf.add_action) acf.add_action('ready', init);
})(jQuery);