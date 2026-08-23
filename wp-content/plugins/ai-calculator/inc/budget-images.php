<?php
/**
 * Картинки калькулятора бюджета — ключи и URL.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Имена файлов в assets/img/ (ключ => файл).
 *
 * @return array<string, string>
 */
function ai_calculator_budget_image_files() {
	return array(
		'calendar'    => 'calendar.svg',
		'users'       => 'users.svg',
		'oteli'       => 'oteli.webp',
		'apartamenti' => 'apartamenti.webp',
		'deshevle'    => 'deshevle.webp',
		'sredniii'    => 'sredniii.webp',
		'visokii'     => 'visokii.webp',
		'kazdii_den'  => 'kazdii_den.webp',
		'razvlechenia_raz_v_neskolko_dnay' => 'razvlechenia_raz_v_neskolko_dnay.webp',
		'kak_mojno_menhe_platnix' => 'kak_mojno_menhe_platnix.webp',
		'restorany_xoroshego_uravna' => 'restorany_xoroshego_uravna.webp',
		'nedorogie_restorany_kafe' => 'nedorogie_restorany_kafe.webp',
		'v_osnovnom_gotovit_doma' => 'v_osnovnom_gotovit_doma.webp',
		'da'  => 'da.webp',
		'net' => 'net.webp',
		'visokii_avto'  => 'visokii_avto.webp',
		'srednii_avto'  => 'srednii_avto.webp',
		'deshovii_avto' => 'deshovii_avto.webp',
		'budget_ne_vagen'       => 'budget_ne_vagen.webp',
		'bydget_vasgen'         => 'bydget_vasgen.webp',
		'budzet_vashnee_vsego'  => 'budzet_vashnee_vsego.webp',
		'dengy'         => 'dengy.png',
		'transport'     => 'car.png',
		'prozhivanie'   => 'prochivanie.png',
		'pitanie'       => 'pitanie.png',
		'razvlechenie'  => 'razvlichenie.png',
	);
}

/**
 * Полные URL картинок калькулятора бюджета.
 *
 * @return array<string, string>
 */
function ai_calculator_budget_images() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$cache = array();

	foreach ( ai_calculator_budget_image_files() as $key => $file ) {
		$cache[ $key ] = ai_calculator_img_url( $file );
	}

	return $cache;
}

/**
 * URL одной картинки по ключу.
 *
 * @param string $key calendar, users, oteli, apartamenti …
 * @return string
 */
function ai_calculator_budget_image_url( $key ) {
	$key    = sanitize_key( (string) $key );
	$images = ai_calculator_budget_images();

	return isset( $images[ $key ] ) ? $images[ $key ] : '';
}
