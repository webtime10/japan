<?php
/**
 * Кантоны Швейцарии для калькулятора бюджета (статичный список, 3 языка Polylang).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, string>
 */
function ai_calculator_budget_swiss_region_slugs() {
	return array(
		'zurich',
		'bern',
		'lucerne',
		'uri',
		'schwyz',
		'obwalden',
		'nidwalden',
		'glarus',
		'zug',
		'fribourg',
		'solothurn',
		'basel-stadt',
		'basel-landschaft',
		'schaffhausen',
		'appenzell-ausserrhoden',
		'appenzell-innerrhoden',
		'st-gallen',
		'graubunden',
		'aargau',
		'thurgau',
		'ticino',
		'vaud',
		'valais',
		'neuchatel',
		'geneva',
		'jura',
	);
}

/**
 * @return array<string, array<string, string>>
 */
function ai_calculator_budget_swiss_region_labels_by_lang() {
	return array(
		'en' => array(
			'zurich'                  => 'Zurich',
			'bern'                    => 'Bern',
			'lucerne'                 => 'Lucerne',
			'uri'                     => 'Uri',
			'schwyz'                  => 'Schwyz',
			'obwalden'                => 'Obwalden',
			'nidwalden'               => 'Nidwalden',
			'glarus'                  => 'Glarus',
			'zug'                     => 'Zug',
			'fribourg'                => 'Fribourg',
			'solothurn'               => 'Solothurn',
			'basel-stadt'             => 'Basel-Stadt',
			'basel-landschaft'        => 'Basel-Landschaft',
			'schaffhausen'            => 'Schaffhausen',
			'appenzell-ausserrhoden'  => 'Appenzell Ausserrhoden',
			'appenzell-innerrhoden'   => 'Appenzell Innerrhoden',
			'st-gallen'               => 'St. Gallen',
			'graubunden'              => 'Graubünden',
			'aargau'                  => 'Aargau',
			'thurgau'                 => 'Thurgau',
			'ticino'                  => 'Ticino',
			'vaud'                    => 'Vaud',
			'valais'                  => 'Valais',
			'neuchatel'               => 'Neuchâtel',
			'geneva'                  => 'Geneva',
			'jura'                    => 'Jura',
		),
		'he' => array(
			'zurich'                  => 'ציריך',
			'bern'                    => 'ברן',
			'lucerne'                 => 'לוצרן',
			'uri'                     => 'אורי',
			'schwyz'                  => 'שוויץ',
			'obwalden'                => 'אובוולדן',
			'nidwalden'               => 'נידוולדן',
			'glarus'                  => 'גלארוס',
			'zug'                     => 'צוג',
			'fribourg'                => 'פריבור',
			'solothurn'               => 'זולותורן',
			'basel-stadt'             => 'בזל-עיר',
			'basel-landschaft'        => 'בזל-כפר',
			'schaffhausen'            => 'שפהאוזן',
			'appenzell-ausserrhoden'  => 'אפנצל אוסרהודן',
			'appenzell-innerrhoden'   => 'אפנצל אינרהודן',
			'st-gallen'               => 'סנט גאלן',
			'graubunden'              => 'גראובונדן',
			'aargau'                  => 'ארגאו',
			'thurgau'                 => 'תורגאו',
			'ticino'                  => "טיצ'ינו",
			'vaud'                    => 'וֹד',
			'valais'                  => 'ואלה',
			'neuchatel'               => 'נשאטל',
			'geneva'                  => "ז'נבה",
			'jura'                    => "ז'ורה",
		),
		'ar' => array(
			'zurich'                  => 'زيورخ',
			'bern'                    => 'برن',
			'lucerne'                 => 'لوتسيرن',
			'uri'                     => 'أوري',
			'schwyz'                  => 'شفيتس',
			'obwalden'                => 'أوبفالدن',
			'nidwalden'               => 'نيدفالدن',
			'glarus'                  => 'غلاروس',
			'zug'                     => 'زوغ',
			'fribourg'                => 'فريبور',
			'solothurn'               => 'سولوتورن',
			'basel-stadt'             => 'بازل المدينة',
			'basel-landschaft'        => 'بازل الريف',
			'schaffhausen'            => 'شافهاوزن',
			'appenzell-ausserrhoden'  => 'أبينزيل أوسيرهودن',
			'appenzell-innerrhoden'   => 'أبينزيل إنرهودن',
			'st-gallen'               => 'سانت غالن',
			'graubunden'              => 'غراوبوندن',
			'aargau'                  => 'أرغاو',
			'thurgau'                 => 'تورغاو',
			'ticino'                  => 'تيسينو',
			'vaud'                    => 'فود',
			'valais'                  => 'فاليه',
			'neuchatel'               => 'نوشاتيل',
			'geneva'                  => 'جنيف',
			'jura'                    => 'جورا',
		),
	);
}

/**
 * Список регионов для &lt;select&gt; по текущему языку Polylang.
 *
 * @param string $lang Slug языка (en, he, ar). Пусто — текущий Polylang.
 * @return array<int, array{value: string, label: string}>
 */
function ai_calculator_budget_swiss_regions( $lang = '' ) {
	$lang   = '' !== (string) $lang ? ai_calculator_normalize_lang_slug( (string) $lang ) : ai_calculator_polylang_slug();
	$labels = ai_calculator_budget_swiss_region_labels_by_lang();

	if ( 'ar' === $lang && isset( $labels['he'] ) ) {
		$lang = 'he';
	} elseif ( ! isset( $labels[ $lang ] ) ) {
		$lang = 'ar';
	}

	$out = array();
	foreach ( ai_calculator_budget_swiss_region_slugs() as $slug ) {
		$out[] = array(
			'value' => $slug,
			'label' => isset( $labels[ $lang ][ $slug ] ) ? $labels[ $lang ][ $slug ] : $slug,
		);
	}

	return $out;
}
