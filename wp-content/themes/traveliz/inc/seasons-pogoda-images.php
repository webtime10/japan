<?php
/**
 * Автоподстановка main image для seasons_line из папки img_pogoda.
 *
 * Файлы: japan-{month}-{N}_1.webp (например japan-april-10_1.webp).
 * Ресайзы вида *-150x150.webp не используются.
 *
 * @package traveliz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'traveliz_pogoda_months' ) ) {
	/**
	 * @return list<string>
	 */
	function traveliz_pogoda_months(): array {
		return array(
			'january',
			'february',
			'march',
			'april',
			'may',
			'june',
			'july',
			'august',
			'september',
			'october',
			'november',
			'december',
		);
	}
}

if ( ! function_exists( 'traveliz_pogoda_dir' ) ) {
	function traveliz_pogoda_dir(): string {
		return trailingslashit( get_template_directory() ) . 'img_pogoda';
	}
}

if ( ! function_exists( 'traveliz_pogoda_list_month_files' ) ) {
	/**
	 * Только оригиналы japan-{month}-{N}_1.webp, без -150x150 и прочих ресайзов.
	 *
	 * @return list<string> absolute paths
	 */
	function traveliz_pogoda_list_month_files( string $month ): array {
		$month = strtolower( trim( $month ) );
		$dir   = traveliz_pogoda_dir();
		if ( $month === '' || ! is_dir( $dir ) ) {
			return array();
		}

		$pattern = '/^japan-' . preg_quote( $month, '/' ) . '-\d+_1\.webp$/i';
		$files   = array();

		foreach ( scandir( $dir ) ?: array() as $name ) {
			if ( $name === '.' || $name === '..' ) {
				continue;
			}
			if ( ! preg_match( $pattern, $name ) ) {
				continue;
			}
			// На всякий случай отсекаем WxH в имени.
			if ( preg_match( '/-\d+x\d+\./i', $name ) ) {
				continue;
			}
			$path = $dir . DIRECTORY_SEPARATOR . $name;
			if ( is_file( $path ) ) {
				$files[] = $path;
			}
		}

		sort( $files, SORT_NATURAL | SORT_FLAG_CASE );

		return $files;
	}
}

if ( ! function_exists( 'traveliz_pogoda_find_attachment_by_source' ) ) {
	function traveliz_pogoda_find_attachment_by_source( string $basename ): int {
		$basename = basename( $basename );
		if ( $basename === '' ) {
			return 0;
		}

		$q = new WP_Query(
			array(
				'post_type'              => 'attachment',
				'post_status'            => 'inherit',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					array(
						'key'   => '_traveliz_pogoda_source',
						'value' => $basename,
					),
				),
			)
		);

		if ( ! empty( $q->posts[0] ) ) {
			return (int) $q->posts[0];
		}

		// Fallback: attachment с таким же именем файла в uploads.
		global $wpdb;
		$like = '%' . $wpdb->esc_like( $basename );
		$id   = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta}
				 WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s
				 ORDER BY post_id DESC LIMIT 1",
				$like
			)
		);

		if ( $id > 0 ) {
			update_post_meta( $id, '_traveliz_pogoda_source', $basename );
		}

		return $id;
	}
}

if ( ! function_exists( 'traveliz_pogoda_ensure_attachment' ) ) {
	/**
	 * Копирует файл из темы в медиатеку (один раз на basename).
	 */
	function traveliz_pogoda_ensure_attachment( string $absolute_path ): int {
		$absolute_path = wp_normalize_path( $absolute_path );
		if ( $absolute_path === '' || ! is_file( $absolute_path ) ) {
			return 0;
		}

		$basename = basename( $absolute_path );
		$existing = traveliz_pogoda_find_attachment_by_source( $basename );
		if ( $existing > 0 ) {
			return $existing;
		}

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$tmp = wp_tempnam( $basename );
		if ( ! $tmp || ! @copy( $absolute_path, $tmp ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( $tmp && file_exists( $tmp ) ) {
				@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			}

			return 0;
		}

		$file_array = array(
			'name'     => $basename,
			'tmp_name' => $tmp,
		);

		$attachment_id = media_handle_sideload( $file_array, 0, null, array( 'post_title' => pathinfo( $basename, PATHINFO_FILENAME ) ) );

		if ( is_wp_error( $attachment_id ) ) {
			if ( file_exists( $tmp ) ) {
				@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			}

			return 0;
		}

		$attachment_id = (int) $attachment_id;
		update_post_meta( $attachment_id, '_traveliz_pogoda_source', $basename );

		return $attachment_id;
	}
}

if ( ! function_exists( 'traveliz_pogoda_random_month_attachment_id' ) ) {
	function traveliz_pogoda_random_month_attachment_id( string $month ): int {
		$files = traveliz_pogoda_list_month_files( $month );
		if ( $files === array() ) {
			return 0;
		}

		$path = $files[ array_rand( $files ) ];

		return traveliz_pogoda_ensure_attachment( $path );
	}
}

if ( ! function_exists( 'traveliz_pogoda_image_field_empty' ) ) {
	/**
	 * @param mixed $value
	 */
	function traveliz_pogoda_image_field_empty( $value ): bool {
		if ( $value === null || $value === false || $value === '' || $value === 0 || $value === '0' ) {
			return true;
		}
		if ( is_array( $value ) && $value === array() ) {
			return true;
		}
		if ( is_array( $value ) ) {
			$id = isset( $value['ID'] ) ? (int) $value['ID'] : ( isset( $value['id'] ) ? (int) $value['id'] : 0 );

			return $id <= 0;
		}

		return false;
	}
}

if ( ! function_exists( 'traveliz_pogoda_fill_seasons_line_images' ) ) {
	/**
	 * Для пустых s_flexibol_season_{month}_image подставляет random из img_pogoda.
	 *
	 * @param array<string, mixed> $row
	 * @return array<string, mixed>
	 */
	function traveliz_pogoda_fill_seasons_line_images( array $row ): array {
		$layout = isset( $row['acf_fc_layout'] ) ? (string) $row['acf_fc_layout'] : '';
		if ( $layout !== '' && $layout !== 's_flexibol_seasons_line' ) {
			return $row;
		}

		foreach ( traveliz_pogoda_months() as $month ) {
			$key = 's_flexibol_season_' . $month . '_image';
			if ( ! traveliz_pogoda_image_field_empty( $row[ $key ] ?? null ) ) {
				continue;
			}

			$attachment_id = traveliz_pogoda_random_month_attachment_id( $month );
			if ( $attachment_id > 0 ) {
				$row[ $key ] = $attachment_id;
			}
		}

		return $row;
	}
}

if ( ! function_exists( 'traveliz_pogoda_fill_flexible_rows' ) ) {
	/**
	 * @param list<array<string, mixed>> $rows
	 * @return list<array<string, mixed>>
	 */
	function traveliz_pogoda_fill_flexible_rows( array $rows ): array {
		foreach ( $rows as $i => $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			if ( ( $row['acf_fc_layout'] ?? '' ) !== 's_flexibol_seasons_line' ) {
				continue;
			}
			$rows[ $i ] = traveliz_pogoda_fill_seasons_line_images( $row );
		}

		return $rows;
	}
}

// При сборке rows из Laravel/AI.
add_filter(
	'traveliz_laravel_flexible_rows',
	static function ( $rows, $fields = null ) {
		unset( $fields );
		if ( ! is_array( $rows ) || $rows === array() ) {
			return $rows;
		}

		return traveliz_pogoda_fill_flexible_rows( $rows );
	},
	20,
	2
);

// При сохранении страницы в админке ACF.
add_action(
	'acf/save_post',
	static function ( $post_id ) {
		static $busy = false;
		if ( $busy ) {
			return;
		}
		if ( ! is_numeric( $post_id ) || (int) $post_id <= 0 ) {
			return;
		}
		if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
			return;
		}

		$post_type = get_post_type( (int) $post_id );
		if ( $post_type !== 'page' && $post_type !== 'post' ) {
			return;
		}

		$rows = get_field( 's_flexibol_constructor', (int) $post_id );
		if ( ! is_array( $rows ) || $rows === array() ) {
			return;
		}

		$filled  = traveliz_pogoda_fill_flexible_rows( $rows );
		$changed = wp_json_encode( $filled ) !== wp_json_encode( $rows );
		if ( ! $changed ) {
			return;
		}

		$busy = true;
		$ok   = update_field( 'field_s_flexibol_constructor', $filled, (int) $post_id );
		if ( ! $ok ) {
			update_field( 's_flexibol_constructor', $filled, (int) $post_id );
		}
		$busy = false;
	},
	25
);
