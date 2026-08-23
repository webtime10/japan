<?php
/**
 * Plugin Name: Weather Image
 * Description: Загрузка и удаление фото погоды в img_pogoda. Имя: japan-{month}-{N}_1.webp
 * Version: 1.1.0
 * Author: Traveliz
 * Text Domain: weather-image
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Weather_Image_Plugin {

	const SLUG = 'weather-image';

	/** @return array<string, string> slug => label */
	public static function months_labels(): array {
		return array(
			'january'   => 'Январь',
			'february'  => 'Февраль',
			'march'     => 'Март',
			'april'     => 'Апрель',
			'may'       => 'Май',
			'june'      => 'Июнь',
			'july'      => 'Июль',
			'august'    => 'Август',
			'september' => 'Сентябрь',
			'october'   => 'Октябрь',
			'november'  => 'Ноябрь',
			'december'  => 'Декабрь',
		);
	}

	/** @return list<string> */
	public static function months(): array {
		return array_keys( self::months_labels() );
	}

	public static function target_dir(): string {
		return wp_normalize_path( get_template_directory() . '/img_pogoda' );
	}

	public static function target_url(): string {
		return trailingslashit( get_template_directory_uri() ) . 'img_pogoda';
	}

	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'admin_post_weather_image_upload', array( __CLASS__, 'handle_upload' ) );
		add_action( 'admin_post_weather_image_delete', array( __CLASS__, 'handle_delete' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
	}

	public static function admin_menu(): void {
		add_menu_page(
			'Weather Image',
			'Weather Image',
			'manage_options',
			self::SLUG,
			array( __CLASS__, 'render_page' ),
			'dashicons-format-image',
			58
		);
	}

	public static function admin_assets( string $hook ): void {
		if ( $hook !== 'toplevel_page_' . self::SLUG ) {
			return;
		}

		$css = '
		.wi-wrap{max-width:960px}
		.wi-tabs{display:flex;flex-wrap:wrap;gap:6px;margin:12px 0 16px;padding:0;list-style:none}
		.wi-tabs a{display:inline-block;padding:6px 12px;border:1px solid #c3c4c7;border-radius:999px;background:#fff;text-decoration:none;color:#1d2327;font-size:13px;line-height:1.3}
		.wi-tabs a:hover{border-color:#2271b1;color:#2271b1}
		.wi-tabs a.is-active{background:#2271b1;border-color:#2271b1;color:#fff;font-weight:600}
		.wi-tabs .wi-count{opacity:.75;font-size:11px;margin-left:4px}
		.wi-upload{display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin:0 0 16px;padding:12px;background:#fff;border:1px solid #c3c4c7;border-radius:6px}
		.wi-upload .button{margin:0}
		.wi-table{width:100%;border-collapse:collapse;background:#fff}
		.wi-table th,.wi-table td{padding:8px 10px;border-bottom:1px solid #e2e4e7;vertical-align:middle;font-size:13px}
		.wi-table th{text-align:left;background:#f6f7f7;font-weight:600}
		.wi-table tr:hover td{background:#f0f6fc}
		.wi-thumb{width:48px;height:48px;object-fit:cover;border-radius:4px;border:1px solid #dcdcde;background:#f0f0f1;display:block}
		.wi-name{font-family:Consolas,Monaco,monospace;font-size:12px}
		.wi-del{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border:1px solid #d63638;border-radius:4px;background:#fff;color:#d63638;text-decoration:none;font-size:16px;line-height:1;cursor:pointer}
		.wi-del:hover{background:#d63638;color:#fff}
		.wi-empty{padding:24px;text-align:center;color:#646970;background:#fff;border:1px dashed #c3c4c7;border-radius:6px}
		.wi-hint{margin:0 0 12px;color:#646970;font-size:12px}
		';
		wp_register_style( 'weather-image-admin', false, array(), '1.1.0' );
		wp_enqueue_style( 'weather-image-admin' );
		wp_add_inline_style( 'weather-image-admin', $css );
	}

	public static function current_month(): string {
		$month = isset( $_GET['month'] ) ? sanitize_key( (string) wp_unslash( $_GET['month'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$months = self::months();
		if ( $month === '' || ! in_array( $month, $months, true ) ) {
			return $months[0];
		}

		return $month;
	}

	public static function page_url( string $month = '' ): string {
		$args = array( 'page' => self::SLUG );
		if ( $month !== '' ) {
			$args['month'] = $month;
		}

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	public static function is_valid_filename( string $name ): bool {
		$months  = implode( '|', array_map( 'preg_quote', self::months() ) );
		$pattern = '/^japan-(' . $months . ')-\d+_1\.(webp|jpg|jpeg|png)$/i';

		return (bool) preg_match( $pattern, $name );
	}

	/**
	 * @return array<string, list<string>>
	 */
	public static function files_by_month(): array {
		$out = array();
		foreach ( self::months() as $m ) {
			$out[ $m ] = array();
		}

		$dir = self::target_dir();
		if ( ! is_dir( $dir ) ) {
			return $out;
		}

		foreach ( scandir( $dir ) ?: array() as $f ) {
			if ( $f === '.' || $f === '..' ) {
				continue;
			}
			if ( ! preg_match( '/^japan-([a-z]+)-\d+_1\.(webp|jpg|jpeg|png)$/i', $f, $m ) ) {
				continue;
			}
			if ( preg_match( '/-\d+x\d+\./i', $f ) ) {
				continue;
			}
			$month = strtolower( $m[1] );
			if ( ! isset( $out[ $month ] ) ) {
				continue;
			}
			$out[ $month ][] = $f;
		}

		foreach ( $out as $month => $files ) {
			natcasesort( $files );
			$out[ $month ] = array_values( $files );
		}

		return $out;
	}

	public static function set_notices( array $messages ): void {
		set_transient( 'weather_image_notices_' . get_current_user_id(), $messages, 60 );
	}

	public static function handle_upload(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Недостаточно прав.', 'weather-image' ) );
		}

		check_admin_referer( 'weather_image_upload' );

		$month    = isset( $_POST['month'] ) ? sanitize_key( (string) wp_unslash( $_POST['month'] ) ) : '';
		$redirect = self::page_url( in_array( $month, self::months(), true ) ? $month : self::months()[0] );
		$messages = array();

		if ( empty( $_FILES['weather_images'] ) || ! is_array( $_FILES['weather_images']['name'] ) ) {
			self::set_notices(
				array(
					array(
						'type' => 'error',
						'text' => 'Файлы не выбраны.',
					),
				)
			);
			wp_safe_redirect( $redirect );
			exit;
		}

		$files = $_FILES['weather_images'];
		$count = count( $files['name'] );
		$dir   = self::target_dir();

		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		if ( ! is_dir( $dir ) || ! is_writable( $dir ) ) {
			self::set_notices(
				array(
					array(
						'type' => 'error',
						'text' => 'Папка img_pogoda недоступна для записи.',
					),
				)
			);
			wp_safe_redirect( $redirect );
			exit;
		}

		$allowed_mimes = array(
			'webp' => 'image/webp',
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
		);

		for ( $i = 0; $i < $count; $i++ ) {
			$orig_name  = isset( $files['name'][ $i ] ) ? (string) $files['name'][ $i ] : '';
			$error      = isset( $files['error'][ $i ] ) ? (int) $files['error'][ $i ] : UPLOAD_ERR_NO_FILE;
			$tmp        = isset( $files['tmp_name'][ $i ] ) ? (string) $files['tmp_name'][ $i ] : '';
			$name_check = strtolower( basename( $orig_name ) );

			if ( $orig_name === '' || $error === UPLOAD_ERR_NO_FILE ) {
				continue;
			}

			if ( $error !== UPLOAD_ERR_OK || $tmp === '' || ! is_uploaded_file( $tmp ) ) {
				$messages[] = array(
					'type' => 'error',
					'text' => $name_check . ' — ошибка загрузки.',
				);
				continue;
			}

			if ( ! self::is_valid_filename( $name_check ) ) {
				$messages[] = array(
					'type' => 'error',
					'text' => $name_check . ' — неправильный формат',
				);
				continue;
			}

			if ( ! preg_match( '/^(japan-[a-z]+-\d+_1)\.(webp|jpg|jpeg|png)$/i', $name_check, $m ) ) {
				$messages[] = array(
					'type' => 'error',
					'text' => $name_check . ' — неправильный формат',
				);
				continue;
			}

			$final_name = strtolower( $m[1] ) . '.' . strtolower( $m[2] );
			$ext        = strtolower( pathinfo( $final_name, PATHINFO_EXTENSION ) );
			if ( ! isset( $allowed_mimes[ $ext ] ) ) {
				$messages[] = array(
					'type' => 'error',
					'text' => $name_check . ' — неправильный формат',
				);
				continue;
			}

			$ft = wp_check_filetype_and_ext( $tmp, $final_name, $allowed_mimes );
			if ( empty( $ft['ext'] ) || empty( $ft['type'] ) ) {
				$finfo = @mime_content_type( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				if ( ! is_string( $finfo ) || strpos( $finfo, 'image/' ) !== 0 ) {
					$messages[] = array(
						'type' => 'error',
						'text' => $name_check . ' — неправильный формат',
					);
					continue;
				}
			}

			$dest = trailingslashit( $dir ) . $final_name;
			if ( file_exists( $dest ) ) {
				$messages[] = array(
					'type' => 'warning',
					'text' => $final_name . ' — такая уже есть',
				);
				continue;
			}

			if ( ! @move_uploaded_file( $tmp, $dest ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$messages[] = array(
					'type' => 'error',
					'text' => $final_name . ' — не удалось сохранить файл.',
				);
				continue;
			}

			@chmod( $dest, 0644 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

			$messages[] = array(
				'type' => 'success',
				'text' => $final_name . ' — загружено',
			);

			if ( preg_match( '/^japan-([a-z]+)-/i', $final_name, $mm ) ) {
				$month = strtolower( $mm[1] );
			}
		}

		if ( $messages === array() ) {
			$messages[] = array(
				'type' => 'error',
				'text' => 'Файлы не выбраны.',
			);
		}

		self::set_notices( $messages );
		wp_safe_redirect( self::page_url( in_array( $month, self::months(), true ) ? $month : self::months()[0] ) );
		exit;
	}

	public static function handle_delete(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Недостаточно прав.', 'weather-image' ) );
		}

		check_admin_referer( 'weather_image_delete' );

		$file  = isset( $_GET['file'] ) ? basename( (string) wp_unslash( $_GET['file'] ) ) : '';
		$month = isset( $_GET['month'] ) ? sanitize_key( (string) wp_unslash( $_GET['month'] ) ) : '';
		if ( ! in_array( $month, self::months(), true ) ) {
			$month = self::months()[0];
		}

		$redirect = self::page_url( $month );

		if ( $file === '' || ! self::is_valid_filename( $file ) ) {
			self::set_notices(
				array(
					array(
						'type' => 'error',
						'text' => 'неправильный формат',
					),
				)
			);
			wp_safe_redirect( $redirect );
			exit;
		}

		$path = trailingslashit( self::target_dir() ) . $file;
		$path = wp_normalize_path( $path );
		$root = trailingslashit( self::target_dir() );

		if ( strpos( $path, $root ) !== 0 || ! is_file( $path ) ) {
			self::set_notices(
				array(
					array(
						'type' => 'error',
						'text' => $file . ' — файл не найден',
					),
				)
			);
			wp_safe_redirect( $redirect );
			exit;
		}

		if ( ! @unlink( $path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			self::set_notices(
				array(
					array(
						'type' => 'error',
						'text' => $file . ' — не удалось удалить',
					),
				)
			);
			wp_safe_redirect( $redirect );
			exit;
		}

		self::set_notices(
			array(
				array(
					'type' => 'success',
					'text' => $file . ' — удалено',
				),
			)
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$uid     = get_current_user_id();
		$notices = get_transient( 'weather_image_notices_' . $uid );
		delete_transient( 'weather_image_notices_' . $uid );
		if ( ! is_array( $notices ) ) {
			$notices = array();
		}

		$current = self::current_month();
		$by_month = self::files_by_month();
		$labels   = self::months_labels();
		$files    = $by_month[ $current ] ?? array();
		$base_url = trailingslashit( self::target_url() );
		$dir      = self::target_dir();
		?>
		<div class="wrap wi-wrap">
			<h1 style="margin-bottom:8px;">Weather Image</h1>
			<p class="wi-hint">Папка: <code><?php echo esc_html( $dir ); ?></code> · формат <code>japan-{month}-{N}_1.webp</code></p>

			<?php foreach ( $notices as $notice ) : ?>
				<?php
				$class = 'notice-info';
				if ( ( $notice['type'] ?? '' ) === 'success' ) {
					$class = 'notice-success';
				} elseif ( ( $notice['type'] ?? '' ) === 'warning' ) {
					$class = 'notice-warning';
				} elseif ( ( $notice['type'] ?? '' ) === 'error' ) {
					$class = 'notice-error';
				}
				?>
				<div class="notice <?php echo esc_attr( $class ); ?> is-dismissible"><p><?php echo esc_html( (string) ( $notice['text'] ?? '' ) ); ?></p></div>
			<?php endforeach; ?>

			<ul class="wi-tabs">
				<?php foreach ( $labels as $slug => $label ) : ?>
					<?php
					$count  = count( $by_month[ $slug ] ?? array() );
					$active = $slug === $current ? ' is-active' : '';
					?>
					<li>
						<a class="<?php echo esc_attr( trim( $active ) ); ?>" href="<?php echo esc_url( self::page_url( $slug ) ); ?>">
							<?php echo esc_html( $label ); ?>
							<span class="wi-count">(<?php echo (int) $count; ?>)</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<form class="wi-upload" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
				<?php wp_nonce_field( 'weather_image_upload' ); ?>
				<input type="hidden" name="action" value="weather_image_upload">
				<input type="hidden" name="month" value="<?php echo esc_attr( $current ); ?>">
				<strong><?php echo esc_html( $labels[ $current ] ?? $current ); ?></strong>
				<input type="file" name="weather_images[]" accept=".webp,.jpg,.jpeg,.png,image/webp,image/jpeg,image/png" multiple required>
				<?php submit_button( 'Загрузить', 'primary', 'submit', false ); ?>
			</form>

			<?php if ( $files === array() ) : ?>
				<div class="wi-empty">В «<?php echo esc_html( $labels[ $current ] ?? $current ); ?>» пока нет фото.</div>
			<?php else : ?>
				<table class="wi-table">
					<thead>
						<tr>
							<th style="width:64px">Фото</th>
							<th>Файл</th>
							<th style="width:90px">Размер</th>
							<th style="width:48px"></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $files as $f ) : ?>
							<?php
							$path = trailingslashit( $dir ) . $f;
							$size = is_file( $path ) ? size_format( (int) filesize( $path ), 1 ) : '—';
							$url  = $base_url . rawurlencode( $f );
							$del  = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'weather_image_delete',
										'file'   => $f,
										'month'  => $current,
									),
									admin_url( 'admin-post.php' )
								),
								'weather_image_delete'
							);
							?>
							<tr>
								<td>
									<img class="wi-thumb" src="<?php echo esc_url( $url ); ?>" alt="" loading="lazy" width="48" height="48">
								</td>
								<td><span class="wi-name"><?php echo esc_html( $f ); ?></span></td>
								<td><?php echo esc_html( (string) $size ); ?></td>
								<td>
									<a class="wi-del" href="<?php echo esc_url( $del ); ?>" title="Удалить" aria-label="Удалить"
									   onclick="return confirm('Удалить <?php echo esc_js( $f ); ?>?');">&times;</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}
}

Weather_Image_Plugin::init();
