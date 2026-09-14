<?php
/**
 * Weather Calculator — front markup.
 *
 * @var array<string, mixed> $ai_weather From handler::render().
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$months   = isset( $ai_weather['months'] ) ? $ai_weather['months'] : array();
$regions  = isset( $ai_weather['regions'] ) ? $ai_weather['regions'] : array();
$defaults = isset( $ai_weather['defaults'] ) ? $ai_weather['defaults'] : array( 'month' => 0, 'region' => 0 );

$placeholder_month  = ai_calculator_translate( 'weather_select_month' );
$placeholder_region = ai_calculator_translate( 'weather_select_region' );
$stat_precip        = ai_calculator_translate( 'weather_stat_precip' );
$stat_sunny         = ai_calculator_translate( 'weather_stat_sunny' );
$stat_season        = ai_calculator_translate( 'weather_stat_season' );

$calculator_title = ai_calculator_get_custom_title(
	'weather',
	ai_calculator_translate( 'weather_title' )
);

$temp_day_raw     = ai_calculator_translate( 'weather_stat_temp_day' );
$temp_night_raw   = ai_calculator_translate( 'weather_stat_temp_night' );
$temp_pair_is_rtl = function_exists( 'ai_calculator_is_rtl' ) ? ai_calculator_is_rtl() : ( function_exists( 'is_rtl' ) && is_rtl() );
$temp_pair_dir    = $temp_pair_is_rtl ? 'rtl' : 'ltr';

/**
 * Wrap temperature range for BiDi: each value in LTR isolate, joined with " / ".
 * RTL: DOM order min then max → min справа, max слева.
 *
 * @param string $raw Temperature string, e.g. "+12° +8°" or "-20°C".
 * @param bool   $is_rtl Whether the temp pair is RTL.
 * @return string Safe HTML.
 */
$ai_wh_temp_ltr_html = static function ( $raw, $is_rtl = true ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}
	$raw   = str_replace( '/', ' ', $raw );
	$parts = preg_split( '/\s+/u', $raw ) ?: array();
	$parts = array_values( array_filter( $parts, static function ( $t ) {
		return '' !== $t;
	} ) );

	if ( count( $parts ) >= 2 ) {
		$first  = $parts[0];
		$second = $parts[1];
		$n1     = (float) preg_replace( '/[^\d.+-]/', '', $first );
		$n2     = (float) preg_replace( '/[^\d.+-]/', '', $second );
		if ( is_numeric( preg_replace( '/[^\d.+-]/', '', $first ) ) && is_numeric( preg_replace( '/[^\d.+-]/', '', $second ) ) ) {
			$min = ( $n1 <= $n2 ) ? $first : $second;
			$max = ( $n1 <= $n2 ) ? $second : $first;
		} else {
			$max = $first;
			$min = $second;
		}
		$parts = $is_rtl ? array( $min, $max ) : array( $max, $min );
	}

	$html = array();
	foreach ( $parts as $token ) {
		$html[] = '<span class="ai-wh__temp-ltr" dir="ltr" style="unicode-bidi:isolate">' . esc_html( $token ) . '</span>';
	}
	if ( 2 === count( $html ) ) {
		return $html[0] . '<span class="ai-wh__temp-sep" aria-hidden="true">/</span>' . $html[1];
	}
	return implode( '', $html );
};
?>
<section class="ai-wh" data-ai-wh data-ai-remote-url="<?php echo esc_attr( ai_calculator_remote_url() ); ?>">
	<div class="container-4">
		<div class="ai-wh__inner">
			<h2 class="ai-wh__title"><?php echo esc_html( (string) $calculator_title ); ?></h2>
			<input
				type="text"
				class="ai-calculator-hp"
				name="ai_calculator_hp"
				value=""
				tabindex="-1"
				autocomplete="off"
				aria-hidden="true"
				data-ai-hp
			>

			<div class="ai-wh__frame">
				<div class="ai-wh__widget">
					<div class="ai-wh__filters">
						<div class="ai-wh__field">
							<label class="ai-wh__label" for="ai-wh-region"><?php echo esc_html( $placeholder_region ); ?></label>
							<div class="ai-wh__select-wrap">
								<select class="ai-wh__select" id="ai-wh-region" name="ai_wh_region" data-ai-wh-region>
									<option value="" <?php selected( (string) $defaults['region'], '' ); ?>><?php echo esc_html( $placeholder_region ); ?></option>
									<?php foreach ( $regions as $key => $region ) : ?>
										<?php
										$option_label = is_array( $region ) ? (string) ( $region['label'] ?? '' ) : (string) $region;
										$option_value = is_array( $region ) ? (string) ( $region['value'] ?? '' ) : (string) $key;
										if ( $option_value === '' ) {
											$option_value = (string) $key;
										}
										?>
										<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( (string) $defaults['region'], $option_value ); ?>>
											<?php echo esc_html( $option_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="ai-wh__field">
							<label class="ai-wh__label" for="ai-wh-month"><?php echo esc_html( $placeholder_month ); ?></label>
							<div class="ai-wh__select-wrap">
								<select class="ai-wh__select" id="ai-wh-month" name="ai_wh_month" data-ai-wh-month>
									<option value="" <?php selected( (int) $defaults['month'], 0 ); ?>><?php echo esc_html( $placeholder_month ); ?></option>
									<?php foreach ( $months as $key => $label ) : ?>
										<option value="<?php echo esc_attr( (string) $key ); ?>" <?php selected( (int) $defaults['month'], (int) $key ); ?>>
											<?php echo esc_html( $label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="ai-wh__stats" data-ai-wh-stats aria-live="polite">
						<div class="ai-wh__loading" data-ai-wh-loading hidden aria-hidden="true">
							<span class="ai-wh__loading-spinner" aria-hidden="true"></span>
						</div>
						<div class="ai-wh__stat ai-wh__stat--temperature">
							<img class="ai-wh__stat-bg" src="<?php echo esc_url( plugins_url( 'img/weather-calculator/nebo.webp', AI_CALCULATOR_FILE ) ); ?>" alt="" loading="lazy" decoding="async">
							<div class="ai-wh__stat-body">
								<span class="ai-wh__stat-label"><?php echo esc_html( ai_calculator_translate( 'weather_label_avg_temperature' ) ); ?></span>
								<div class="ai-wh__temp-pair" data-ai-wh-temp dir="<?php echo esc_attr( $temp_pair_dir ); ?>">
									<div class="ai-wh__temp-item ai-wh__temp-item--day">
										<span class="ai-wh__temp-caption"><?php echo esc_html( ai_calculator_translate( 'weather_label_temp_day' ) ); ?></span>
										<strong class="ai-wh__temp-range" data-ai-wh-temp-day><?php echo $ai_wh_temp_ltr_html( $temp_day_raw, $temp_pair_is_rtl ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
									</div>
									<div class="ai-wh__temp-item ai-wh__temp-item--night">
										<span class="ai-wh__temp-caption"><?php echo esc_html( ai_calculator_translate( 'weather_label_temp_night' ) ); ?></span>
										<strong class="ai-wh__temp-range" data-ai-wh-temp-night><?php echo $ai_wh_temp_ltr_html( $temp_night_raw, $temp_pair_is_rtl ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
									</div>
								</div>
							</div>
						</div>

						<div class="ai-wh__stat-row">
							<div class="ai-wh__stat ai-wh__stat--precipitation ai-wh__stat--on-dark">
								<img class="ai-wh__stat-bg" src="<?php echo esc_url( plugins_url( 'img/weather-calculator/kapla.webp', AI_CALCULATOR_FILE ) ); ?>" alt="" loading="lazy" decoding="async">
								<div class="ai-wh__stat-body">
									<span class="ai-wh__stat-label"><?php echo esc_html( ai_calculator_translate( 'weather_label_precipitation' ) ); ?></span>
									<strong class="ai-wh__stat-value" data-ai-wh-precip><?php echo esc_html( $stat_precip ); ?></strong>
								</div>
							</div>

							<div class="ai-wh__stat ai-wh__stat--sunny">
								<img class="ai-wh__stat-bg" src="<?php echo esc_url( plugins_url( 'img/weather-calculator/solnce.webp', AI_CALCULATOR_FILE ) ); ?>" alt="" loading="lazy" decoding="async">
								<div class="ai-wh__stat-body">
									<span class="ai-wh__stat-label"><?php echo esc_html( ai_calculator_translate( 'weather_label_sunny_days' ) ); ?></span>
									<strong class="ai-wh__stat-value" data-ai-wh-sunny><?php echo esc_html( $stat_sunny ); ?></strong>
								</div>
							</div>

							<div class="ai-wh__stat ai-wh__stat--season">
								<img class="ai-wh__stat-bg" src="<?php echo esc_url( plugins_url( 'img/weather-calculator/tarva.webp', AI_CALCULATOR_FILE ) ); ?>" alt="" loading="lazy" decoding="async">
								<div class="ai-wh__stat-body">
									<span class="ai-wh__stat-label"><?php echo esc_html( ai_calculator_translate( 'weather_label_active_season' ) ); ?></span>
									<strong class="ai-wh__stat-value" data-ai-wh-season><?php echo esc_html( $stat_season ); ?></strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
