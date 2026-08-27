<?php
/**
 * Admin home.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once AI_CALCULATOR_PATH . 'inc/class-ai-calculator-settings.php';

$rest_index_path   = (string) wp_parse_url( rest_url( 'ai-calculator/v1/' ), PHP_URL_PATH );
$active_url        = AI_Calculator_Settings::get_active_url();
$api_key           = AI_Calculator_Settings::get_api_key();
$api_key_in_config = defined( 'AI_CALCULATOR_LARA_API_KEY' );
$laravel_paths     = array();
$rest_paths        = array();
$calculator_meta   = array();

$calculator_titles = get_option( 'ai_calculator_titles', array() );
if ( ! is_array( $calculator_titles ) ) {
	$calculator_titles = array();
}

// Save calculator titles (frontend <h2> override).
if ( is_admin() && current_user_can( 'manage_options' ) && 'POST' === strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	if ( isset( $_POST['ai_calculator_titles_nonce'] ) && check_admin_referer( 'ai_calculator_titles_save', 'ai_calculator_titles_nonce' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$posted_titles = isset( $_POST['ai_calculator_titles'] ) && is_array( $_POST['ai_calculator_titles'] )
			? $_POST['ai_calculator_titles']
			: array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated

		foreach ( $posted_titles as $slug => $title ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$slug = sanitize_key( (string) $slug );
			if ( '' === $slug ) {
				continue;
			}

			$title = sanitize_text_field( wp_unslash( (string) $title ) );
			if ( '' === $title ) {
				unset( $calculator_titles[ $slug ] );
				continue;
			}

			$calculator_titles[ $slug ] = $title;
		}

		update_option( 'ai_calculator_titles', $calculator_titles, false );

		if ( isset( $_POST['ai_calculator_chat_labels'] ) && is_array( $_POST['ai_calculator_chat_labels'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$chat_defaults = function_exists( 'ai_calculator_chat_label_defaults' )
				? ai_calculator_chat_label_defaults()
				: array();
			$posted_chat   = wp_unslash( $_POST['ai_calculator_chat_labels'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing
			$chat_labels   = array();

			foreach ( array_keys( $chat_defaults ) as $key ) {
				$value = isset( $posted_chat[ $key ] ) ? sanitize_text_field( (string) $posted_chat[ $key ] ) : '';
				if ( '' !== $value ) {
					$chat_labels[ $key ] = $value;
				}
			}

			update_option( 'ai_calculator_chat_labels', $chat_labels, false );
		}

		if ( isset( $_POST['ai_calculator_ideal_region_laravel_manufacturer_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$laravel_mfr = max( 0, (int) wp_unslash( (string) $_POST['ai_calculator_ideal_region_laravel_manufacturer_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			update_option( 'ai_calculator_ideal_region_laravel_manufacturer_id', $laravel_mfr, false );
		}

		if ( isset( $_POST['ai_calculator_ideal_region_labels'] ) && is_array( $_POST['ai_calculator_ideal_region_labels'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$ir_labels_raw = wp_unslash( $_POST['ai_calculator_ideal_region_labels'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing
			if ( function_exists( 'ai_calculator_save_ideal_region_labels' ) ) {
				$ideal_region_labels = ai_calculator_save_ideal_region_labels( $ir_labels_raw );
			} else {
				$ir_labels = array();
				foreach ( array( 'user_goal_placeholder', 'results_title', 'other_variants', 'more', 'hide' ) as $ir_key ) {
					$val = isset( $ir_labels_raw[ $ir_key ] ) ? sanitize_text_field( (string) $ir_labels_raw[ $ir_key ] ) : '';
					if ( '' !== $val ) {
						$ir_labels[ $ir_key ] = $val;
					}
				}
				update_option( 'ai_calculator_ideal_region_labels', $ir_labels, false );
				$ideal_region_labels = $ir_labels;
			}
			$calculator_titles = get_option( 'ai_calculator_titles', array() );
			if ( ! is_array( $calculator_titles ) ) {
				$calculator_titles = array();
			}
		}
	}

	// Фон Ideal Region: фото + надпись.
	if ( isset( $_POST['ai_calculator_ideal_region_bg_nonce'] ) && check_admin_referer( 'ai_calculator_ideal_region_bg_save', 'ai_calculator_ideal_region_bg_nonce' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$bg_image = isset( $_POST['ai_calculator_ideal_region_bg_image'] )
			? esc_url_raw( wp_unslash( (string) $_POST['ai_calculator_ideal_region_bg_image'] ) )
			: '';
		$bg_label = isset( $_POST['ai_calculator_ideal_region_bg_label'] )
			? sanitize_text_field( wp_unslash( (string) $_POST['ai_calculator_ideal_region_bg_label'] ) )
			: '';

		update_option(
			'ai_calculator_ideal_region_bg',
			array(
				'image' => $bg_image,
				'label' => $bg_label,
			),
			false
		);

		if ( '' !== $bg_label ) {
			$ir_labels = get_option( 'ai_calculator_ideal_region_labels', array() );
			if ( ! is_array( $ir_labels ) ) {
				$ir_labels = array();
			}
			$ir_labels['title'] = $bg_label;
			update_option( 'ai_calculator_ideal_region_labels', $ir_labels, false );

			$titles = get_option( 'ai_calculator_titles', array() );
			if ( ! is_array( $titles ) ) {
				$titles = array();
			}
			$titles['ideal_region'] = $bg_label;
			update_option( 'ai_calculator_titles', $titles, false );
			$calculator_titles = $titles;
		}
	}
}

$ideal_region_bg = function_exists( 'ai_calculator_get_ideal_region_background' )
	? ai_calculator_get_ideal_region_background()
	: array( 'image' => '', 'label' => '' );
$ideal_region_laravel_manufacturer_id = function_exists( 'ai_calculator_get_ideal_region_laravel_manufacturer_id' )
	? (int) ai_calculator_get_ideal_region_laravel_manufacturer_id()
	: 1;

$ideal_region_labels          = get_option( 'ai_calculator_ideal_region_labels', array() );
if ( ! is_array( $ideal_region_labels ) ) {
	$ideal_region_labels = array();
}
$ideal_region_label_defaults = function_exists( 'ai_calculator_ideal_region_label_defaults' )
	? ai_calculator_ideal_region_label_defaults()
	: array(
		'user_goal_placeholder' => 'Ваш результат',
		'results_title'         => 'Мы подобрали для вас лучшие регионы',
		'other_variants'        => 'Ещё {n} варианта',
		'more'                  => 'Подробнее',
		'hide'                  => 'Скрыть',
	);
$chat_labels = function_exists( 'ai_calculator_get_chat_labels' )
	? ai_calculator_get_chat_labels()
	: array();
$chat_label_defaults = function_exists( 'ai_calculator_chat_label_defaults' )
	? ai_calculator_chat_label_defaults()
	: array();

if ( class_exists( 'AI_Calculator_Manager' ) ) {
	foreach ( AI_Calculator_Manager::slugs() as $slug ) {
		$laravel_paths[] = AI_Calculator_Settings::get_laravel_plugin_path( $slug );
		$rest_paths[]    = (string) wp_parse_url( rest_url( 'ai-calculator/v1/' . $slug ), PHP_URL_PATH );
	}
	$calculator_meta = AI_Calculator_Manager::all_meta();
}
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php esc_html_e( 'Куда отправлять запрос (Laravel)', 'ai-calculator' ); ?></h3>
	</div>
	<div class="panel-body">
		<form id="ai-calculator-remote-site-form" class="form-horizontal">
			<div class="form-group">
				<label class="control-label" for="ai-calculator-remote-url"><?php esc_html_e( 'Laravel (хост)', 'ai-calculator' ); ?></label>
				<input type="text" class="form-control" id="ai-calculator-remote-url" value="<?php echo esc_attr( $active_url ); ?>" placeholder="lara2.loc" autocomplete="off">
			</div>
			<div class="form-group">
				<label class="control-label" for="ai-calculator-remote-api-key"><?php esc_html_e( 'API ключ Laravel', 'ai-calculator' ); ?></label>
				<input type="password" class="form-control" id="ai-calculator-remote-api-key" value="<?php echo esc_attr( $api_key ); ?>" placeholder="PLUGIN_WEATHER_API_KEY из .env" autocomplete="off" <?php echo $api_key_in_config ? 'readonly' : ''; ?>>
				<?php if ( $api_key_in_config ) : ?>
					<p class="help-block"><?php esc_html_e( 'Задан в wp-config.php (AI_CALCULATOR_LARA_API_KEY).', 'ai-calculator' ); ?></p>
				<?php endif; ?>
			</div>

			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Сохранить', 'ai-calculator' ); ?></button>
		</form>
		<?php if ( ! empty( $laravel_paths ) ) : ?>
			<ul style="margin-top:16px;margin-bottom:0;">
				<?php foreach ( $laravel_paths as $laravel_path ) : ?>
					<li><code><?php echo esc_html( $laravel_path ); ?></code></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>

<?php if ( ! empty( $calculator_meta ) ) : ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php esc_html_e( 'Шорткоды калькуляторов', 'ai-calculator' ); ?></h3>
	</div>
	<div class="panel-body">
		<form method="post">
			<?php wp_nonce_field( 'ai_calculator_titles_save', 'ai_calculator_titles_nonce' ); ?>

			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Калькулятор', 'ai-calculator' ); ?></th>
						<th><?php esc_html_e( 'Шорткоды + Заголовок (h2)', 'ai-calculator' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $calculator_meta as $slug => $meta ) : ?>
						<?php
						$stored_title  = isset( $calculator_titles[ $slug ] ) ? (string) $calculator_titles[ $slug ] : '';
						$default_title = isset( $meta['title'] ) ? (string) $meta['title'] : (string) $slug;
						if ( 'chat' === $slug ) {
							$default_title = __( 'Не хотите читать всю статью?', 'ai-calculator' );
						}
						$input_value = '' !== $stored_title ? $stored_title : $default_title;
						?>
						<tr>
							<td>
								<strong><?php echo esc_html( $meta['title'] ); ?></strong>
								<br><code><?php echo esc_html( $slug ); ?></code>
							</td>
							<td>
								<?php if ( ! empty( $meta['shortcodes'] ) ) : ?>
									<ul style="margin:0;">
										<?php foreach ( $meta['shortcodes'] as $shortcode ) : ?>
											<li><code>[<?php echo esc_html( $shortcode ); ?>]</code></li>
										<?php endforeach; ?>
									</ul>
								<?php else : ?>
									&mdash;
								<?php endif; ?>

								<div style="margin-top:10px;">
									<input
										type="text"
										class="regular-text"
										name="ai_calculator_titles[<?php echo esc_attr( $slug ); ?>]"
										value="<?php echo esc_attr( $input_value ); ?>"
										placeholder="<?php echo esc_attr( $default_title ); ?>"
									/>
								</div>

								<?php if ( 'chat' === $slug && ! empty( $chat_label_defaults ) ) : ?>
									<?php
									$chat_fields = array(
										'summary' => __( 'Кнопка: резюме', 'ai-calculator' ),
										'regions' => __( 'Кнопка: регионы', 'ai-calculator' ),
										'cost'    => __( 'Кнопка: стоимость', 'ai-calculator' ),
										'route'   => __( 'Кнопка: маршрут', 'ai-calculator' ),
									);
									?>
									<div style="margin-top:12px;display:grid;gap:8px;">
										<?php foreach ( $chat_fields as $field_key => $field_label ) : ?>
											<?php
											$field_default = isset( $chat_label_defaults[ $field_key ] ) ? (string) $chat_label_defaults[ $field_key ] : '';
											$field_value   = isset( $chat_labels[ $field_key ] ) ? (string) $chat_labels[ $field_key ] : $field_default;
											?>
											<label style="display:block;">
												<span style="display:block;margin-bottom:4px;"><strong><?php echo esc_html( $field_label ); ?></strong></span>
												<input
													type="text"
													class="regular-text"
													name="ai_calculator_chat_labels[<?php echo esc_attr( $field_key ); ?>]"
													value="<?php echo esc_attr( $field_value ); ?>"
													placeholder="<?php echo esc_attr( $field_default ); ?>"
												/>
											</label>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>

							<?php if ( 'ideal_region' === $slug ) : ?>
								<div style="margin-top:12px;">
									<label for="ai-ideal-region-laravel-mfr">
										<strong><?php esc_html_e( 'Отправка в manufacturer Laravel', 'ai-calculator' ); ?></strong>
									</label>
									<input
										type="number"
										min="1"
										step="1"
										class="small-text"
										id="ai-ideal-region-laravel-mfr"
										name="ai_calculator_ideal_region_laravel_manufacturer_id"
										value="<?php echo esc_attr( (string) $ideal_region_laravel_manufacturer_id ); ?>"
										style="margin-left:8px;width:80px;"
									/>
									<p class="description" style="margin-top:6px;">
										<?php esc_html_e( 'ID производителя на lara2.loc для подбора направлений (Япония = 2).', 'ai-calculator' ); ?>
									</p>
								</div>

								<div style="margin-top:16px;display:grid;gap:10px;">
									<p class="description" style="margin:0;">
										<?php esc_html_e( 'Подписи на карточке квиза. Пустое поле = перевод из languages-data (иврит/EN).', 'ai-calculator' ); ?>
									</p>
									<?php
									$ir_fields = array(
										'title'                 => __( 'H2 заголовок: Ваш идеальный регион', 'ai-calculator' ),
										'next'                  => __( 'Кнопка: Далее', 'ai-calculator' ),
										'back'                  => __( 'Кнопка: Назад', 'ai-calculator' ),
										'choose'                => __( 'Селект: Выберите вариант', 'ai-calculator' ),
										'submit'                => __( 'Кнопка финала: Подобрать регион', 'ai-calculator' ),
										'submitting'            => __( 'Пока грузится: Подбираем регион…', 'ai-calculator' ),
										'more'                  => __( 'Кнопка: Подробнее (иврит: לפרטים נוספים)', 'ai-calculator' ),
										'hide'                  => __( 'Кнопка: Скрыть', 'ai-calculator' ),
										'user_goal_placeholder' => __( 'Плейсхолдер инпута: Ваш результат', 'ai-calculator' ),
										'results_title'         => __( 'Заголовок результатов: Мы подобрали…', 'ai-calculator' ),
										'other_variants'        => __( 'Подпись доп. вариантов: Ещё {n} варианта', 'ai-calculator' ),
									);
									foreach ( $ir_fields as $ir_key => $ir_label ) :
										$ir_default = isset( $ideal_region_label_defaults[ $ir_key ] ) ? $ideal_region_label_defaults[ $ir_key ] : '';
										$ir_value   = isset( $ideal_region_labels[ $ir_key ] ) ? $ideal_region_labels[ $ir_key ] : '';
										if ( 'title' === $ir_key && '' === $ir_value ) {
											if ( ! empty( $ideal_region_bg['label'] ) ) {
												$ir_value = (string) $ideal_region_bg['label'];
											} elseif ( ! empty( $calculator_titles['ideal_region'] ) ) {
												$ir_value = (string) $calculator_titles['ideal_region'];
											}
										}
									?>
										<label style="display:block;">
											<span style="display:block;margin-bottom:4px;"><strong><?php echo esc_html( $ir_label ); ?></strong></span>
											<input
												type="text"
												class="regular-text"
												name="ai_calculator_ideal_region_labels[<?php echo esc_attr( $ir_key ); ?>]"
												value="<?php echo esc_attr( $ir_value ); ?>"
												placeholder="<?php echo esc_attr( $ir_default ); ?>"
											/>
										</label>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p style="margin-top:12px;">
				<button type="submit" class="button button-primary">
					<?php esc_html_e( 'Сохранить заголовки калькуляторов', 'ai-calculator' ); ?>
				</button>
			</p>
		</form>
	</div>
</div>
<?php endif; ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php esc_html_e( 'Фон — Ваш идеальный регион', 'ai-calculator' ); ?></h3>
	</div>
	<div class="panel-body">
		<form method="post">
			<?php wp_nonce_field( 'ai_calculator_ideal_region_bg_save', 'ai_calculator_ideal_region_bg_nonce' ); ?>

			<div class="form-group ai-calculator-media-field">
				<label class="control-label" for="ai-ir-bg-image"><?php esc_html_e( 'Фото фона', 'ai-calculator' ); ?></label>
				<div class="ai-calculator-media-field__controls">
					<input
						type="text"
						class="form-control ai-calculator-media-input"
						id="ai-ir-bg-image"
						name="ai_calculator_ideal_region_bg_image"
						value="<?php echo esc_attr( $ideal_region_bg['image'] ); ?>"
					>
					<button type="button" class="button ai-calculator-media-select"><?php esc_html_e( 'Выбрать', 'ai-calculator' ); ?></button>
					<button type="button" class="button ai-calculator-media-clear" <?php disabled( '' === $ideal_region_bg['image'] ); ?> aria-label="<?php esc_attr_e( 'Удалить фото', 'ai-calculator' ); ?>">&times;</button>
				</div>
				<div class="ai-calculator-media-preview">
					<?php if ( '' !== $ideal_region_bg['image'] ) : ?>
						<img src="<?php echo esc_url( $ideal_region_bg['image'] ); ?>" alt="">
					<?php endif; ?>
				</div>
			</div>

			<div class="form-group" style="margin-top:16px;">
				<label class="control-label" for="ai-ir-bg-label"><?php esc_html_e( 'H2 — Ваш идеальный регион', 'ai-calculator' ); ?></label>
				<input
					type="text"
					class="form-control regular-text"
					id="ai-ir-bg-label"
					name="ai_calculator_ideal_region_bg_label"
					value="<?php echo esc_attr( $ideal_region_bg['label'] ); ?>"
					placeholder="<?php esc_attr_e( 'Ваш идеальный регион', 'ai-calculator' ); ?>"
				>
			</div>

			<p style="margin-top:12px;">
				<button type="submit" class="button button-primary">
					<?php esc_html_e( 'Сохранить фон', 'ai-calculator' ); ?>
				</button>
			</p>
		</form>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php esc_html_e( 'Куда отправляется запрос (WordPress)', 'ai-calculator' ); ?></h3>
	</div>
	<div class="panel-body">
		<ul>
			<li><code><?php echo esc_html( $rest_index_path ); ?></code></li>
			<?php foreach ( $rest_paths as $rest_path ) : ?>
				<li><code><?php echo esc_html( $rest_path ); ?></code></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
