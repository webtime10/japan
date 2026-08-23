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
$stat_temp          = ai_calculator_translate( 'weather_stat_temp' );
$stat_precip        = ai_calculator_translate( 'weather_stat_precip' );
$stat_sunny         = ai_calculator_translate( 'weather_stat_sunny' );
$stat_season        = ai_calculator_translate( 'weather_stat_season' );
?>
<section class="ai-wh" data-ai-wh data-ai-remote-url="<?php echo esc_attr( ai_calculator_remote_url() ); ?>">
	<div class="container-4">
		<div class="ai-wh__inner">
			<h2 class="ai-wh__title"><?php echo esc_html( ai_calculator_translate( 'weather_title' ) ); ?></h2>
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
						<div class="ai-wh__field">
							<label class="ai-wh__label" for="ai-wh-region"><?php echo esc_html( $placeholder_region ); ?></label>
							<div class="ai-wh__select-wrap">
								<select class="ai-wh__select" id="ai-wh-region" name="ai_wh_region" data-ai-wh-region>
									<option value="" <?php selected( (int) $defaults['region'], 0 ); ?>><?php echo esc_html( $placeholder_region ); ?></option>
									<?php foreach ( $regions as $key => $label ) : ?>
										<option value="<?php echo esc_attr( (string) $key ); ?>" <?php selected( (int) $defaults['region'], (int) $key ); ?>>
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
								<strong class="ai-wh__stat-value" data-ai-wh-temp><?php echo esc_html( $stat_temp ); ?></strong>
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
