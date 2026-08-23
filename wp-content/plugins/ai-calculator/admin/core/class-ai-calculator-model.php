<?php
/**
 * Base model.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class AI_Calculator_Model {

	/** @var wpdb */
	protected $wpdb;

	/** @var string */
	protected $prefix;

	public function __construct() {
		global $wpdb;
		$this->wpdb   = $wpdb;
		$this->prefix = $wpdb->prefix . 'ai_calculator_';
	}

	/**
	 * @param string $name Table suffix without prefix.
	 */
	protected function table( $name ) {
		return $this->prefix . $name;
	}

	/**
	 * Текущий язык Polylang: name (English), locale (en_US), slug (en).
	 *
	 * @return array{name: string, locale: string, slug: string}
	 */
	protected function get_polylang_current_language() {
		$empty = array(
			'name'   => '',
			'locale' => '',
			'slug'   => '',
		);

		if ( ! function_exists( 'pll_current_language' ) ) {
			return $empty;
		}

		$name   = pll_current_language( 'name' );
		$locale = pll_current_language( 'locale' );
		$slug   = pll_current_language( 'slug' );

		if ( ( ! is_string( $slug ) || $slug === '' ) && function_exists( 'pll_default_language' ) ) {
			$slug = pll_default_language( 'slug' );
		}

		return array(
			'name'   => is_string( $name ) ? trim( $name ) : '',
			'locale' => is_string( $locale ) ? trim( $locale ) : '',
			'slug'   => is_string( $slug ) ? sanitize_key( $slug ) : '',
		);
	}

	/**
	 * Сопоставить Polylang с записью в wp_ai_calculator_language.
	 *
	 * Порядок (как в админке после Import from Polylang):
	 * 1) name  — «English» на переключателе = поле Name;
	 * 2) locale — en_US, he_IL = поле Locale (и короткий en, если locale в БД без региона);
	 * 3) slug  — en, he = поле Code.
	 *
	 * @param array{name: string, locale: string, slug: string} $pll
	 * @return int language_id или 0.
	 */
	protected function resolve_language_id_from_polylang( array $pll ) {
		$table = $this->table( 'language' );

		if ( $pll['name'] !== '' ) {
			$id = (int) $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT language_id FROM `{$table}` WHERE status = 1 AND LOWER(name) = LOWER(%s) LIMIT 1",
					$pll['name']
				)
			);
			if ( $id > 0 ) {
				return $id;
			}
		}

		if ( $pll['locale'] !== '' ) {
			$id = (int) $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT language_id FROM `{$table}` WHERE status = 1 AND LOWER(locale) = LOWER(%s) LIMIT 1",
					$pll['locale']
				)
			);
			if ( $id > 0 ) {
				return $id;
			}

			// Polylang: en_US → в БД после импорта часто только en в locale/code.
			$short = strtok( $pll['locale'], '_' );
			if ( is_string( $short ) && $short !== '' && $short !== $pll['locale'] ) {
				$id = (int) $this->wpdb->get_var(
					$this->wpdb->prepare(
						"SELECT language_id FROM `{$table}` WHERE status = 1 AND (LOWER(locale) = LOWER(%s) OR LOWER(code) = LOWER(%s)) LIMIT 1",
						$short,
						$short
					)
				);
				if ( $id > 0 ) {
					return $id;
				}
			}
		}

		if ( $pll['slug'] !== '' ) {
			$id = (int) $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT language_id FROM `{$table}` WHERE status = 1 AND LOWER(code) = LOWER(%s) LIMIT 1",
					$pll['slug']
				)
			);
			if ( $id > 0 ) {
				return $id;
			}
		}

		return 0;
	}

	/**
	 * language_id для текущей страницы (язык из Polylang).
	 *
	 * @return int
	 */
	protected function get_current_language_id() {
		static $cached = null;

		if ( null !== $cached ) {
			return $cached;
		}

		$id = $this->resolve_language_id_from_polylang( $this->get_polylang_current_language() );

		if ( $id <= 0 ) {
			$table = $this->table( 'language' );
			$id    = (int) $this->wpdb->get_var( "SELECT language_id FROM `{$table}` WHERE status = 1 ORDER BY sort_order ASC LIMIT 1" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		$cached = $id > 0 ? $id : 1;

		return $cached;
	}
}
