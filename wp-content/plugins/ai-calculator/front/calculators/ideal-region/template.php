<?php
/**
 * Your Ideal Region — front markup.
 *
 * @var array<string, mixed> $ai_ideal_region
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$calculator_title = ai_calculator_get_custom_title(
	'ideal_region',
	'Your Ideal Region'
);

$catalog_cards = isset( $ai_ideal_region['catalog_cards'] ) && is_array( $ai_ideal_region['catalog_cards'] )
	? $ai_ideal_region['catalog_cards']
	: array();

$labels = isset( $ai_ideal_region['labels'] ) && is_array( $ai_ideal_region['labels'] )
	? $ai_ideal_region['labels']
	: ( function_exists( 'ai_calculator_ideal_region_ui_labels' )
		? ai_calculator_ideal_region_ui_labels()
		: array(
			'question' => 'Question',
			'of'       => 'of',
			'next'     => 'Next',
			'back'     => 'Back',
		) );

$max_step = isset( $ai_ideal_region['max_step'] ) ? max( 1, (int) $ai_ideal_region['max_step'] ) : 1;

$ideal_region_bg = function_exists( 'ai_calculator_get_ideal_region_background' )
	? ai_calculator_get_ideal_region_background()
	: array( 'image' => '', 'label' => '' );
$bg_image = isset( $ideal_region_bg['image'] ) ? (string) $ideal_region_bg['image'] : '';

// Заголовок: labels.title → фон → custom title.
$bg_title = '';
if ( ! empty( $labels['title'] ) ) {
	$bg_title = trim( (string) $labels['title'] );
}
if ( '' === $bg_title && ! empty( $ideal_region_bg['label'] ) ) {
	$bg_title = trim( (string) $ideal_region_bg['label'] );
}
if ( '' === $bg_title ) {
	$bg_title = trim( (string) $calculator_title );
}
$show_hero = ( '' !== $bg_image || '' !== $bg_title );

$section_style = '';
if ( '' !== $bg_image ) {
	$section_style = "background-image:url('" . esc_url( $bg_image ) . "')";
}
?>
<section
	class="ai-calculator ai-calculator-ideal-region ai-ir<?php echo '' !== $bg_image ? ' ai-ir--has-bg' : ''; ?>"
	data-ai-ir
	<?php if ( '' !== $section_style ) : ?>
		style="<?php echo esc_attr( $section_style ); ?>"
	<?php endif; ?>
>
	<?php if ( $show_hero ) : ?>
		<div class="ai-ir__hero">
			<div class="container-4">
				<h2 class="ai-ir__hero-title"><?php echo esc_html( $bg_title ); ?></h2>
			</div>
		</div>
	<?php endif; ?>

	<img class="elips52" src="<?php echo esc_url( get_template_directory_uri() . '/img/Ellipse52.webp' ); ?>" alt="">

	<div class="container-4">
		<?php if ( ! $show_hero ) : ?>
			<h2 class="ai-ir__page-title"><?php echo esc_html( (string) $calculator_title ); ?></h2>
		<?php endif; ?>

		<script>
			window.aiCalculatorIdealRegionData = <?php echo wp_json_encode( $catalog_cards, JSON_UNESCAPED_UNICODE ); ?>;
			window.aiCalculatorIdealRegion = window.aiCalculatorIdealRegion || {};
			window.aiCalculatorIdealRegion.labels = <?php echo wp_json_encode( $labels, JSON_UNESCAPED_UNICODE ); ?>;
			window.aiCalculatorIdealRegion.maxStep = <?php echo (int) $max_step; ?>;
			window.aiCalculatorIdealRegion.manufacturerId = <?php echo (int) ( $ai_ideal_region['manufacturer_id'] ?? 5 ); ?>;
			window.aiCalculatorIdealRegion.ajaxUrl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
			window.aiCalculatorIdealRegion.laravelBase = <?php echo wp_json_encode(
				class_exists( 'AI_Calculator_Settings' )
					? (string) AI_Calculator_Settings::get_laravel_origin_url()
					: ''
			); ?>;
			window.aiCalculatorIdealRegion.nonce = <?php echo wp_json_encode( wp_create_nonce( 'ai_calculator_ideal_region' ) ); ?>;
			window.aiCalculatorIdealRegion.polylangSlug = <?php echo wp_json_encode(
				function_exists( 'ai_calculator_polylang_slug' )
					? (string) ai_calculator_polylang_slug()
					: ( function_exists( 'pll_current_language' ) ? (string) pll_current_language( 'slug' ) : 'he' )
			); ?>;
		</script>

		<style>
			#ai-calculator-ideal-region-app[v-cloak] {
				display: none !important;
			}
			/* до 1023 (и 600): 2 в ряд */
			@media (max-width: 1023px) {
				#ai-calculator-ideal-region-app .ai-ir__options-grid {
					display: grid !important;
					grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
					gap: 16px 12px !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--1 {
					grid-template-columns: minmax(0, 1fr) !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__option-card {
					width: auto !important;
					max-width: none !important;
					flex: none !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__option-label {
					font-size: 14px !important;
				}
			}
			/* 1024–1279: 3 в ряд */
			@media (min-width: 1024px) and (max-width: 1279px) {
				#ai-calculator-ideal-region-app .ai-ir__options-grid {
					display: grid !important;
					grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
					gap: 16px 16px !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--1 {
					grid-template-columns: minmax(0, 1fr) !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--2 {
					grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__option-card {
					width: auto !important;
					max-width: none !important;
					flex: none !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--6 {
					grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
					gap: 12px 10px !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--6 .ai-ir__option-card {
					max-width: none !important;
				}
			}
			/* десктоп с 1280: 4 в ряд */
			@media (min-width: 1280px) {
				#ai-calculator-ideal-region-app .ai-ir__options-grid {
					display: grid !important;
					grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
					gap: 16px 20px !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--1 {
					grid-template-columns: minmax(0, 1fr) !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--2 {
					grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--3 {
					grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__option-card {
					width: auto !important;
					max-width: 273px !important;
					flex: none !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--6 {
					grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
					gap: 12px 10px !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--6 .ai-ir__option-card {
					max-width: none !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--step-4 {
					display: flex !important;
					flex-wrap: wrap !important;
					justify-content: center !important;
					gap: 16px 20px !important;
				}
				#ai-calculator-ideal-region-app .ai-ir__options-grid--step-4 .ai-ir__option-card {
					flex: 0 0 auto !important;
					width: calc((100% - 60px) / 4) !important;
					max-width: 273px !important;
				}
			}
		</style>

		<div id="ai-calculator-ideal-region-app" v-cloak style="display:none">
			<div class="ai-ir__widget" :class="{ 'ai-ir__widget--loading': isSubmitting }" :data-step="showResults ? 'results' : step">
				<div class="ai-ir__loading" v-show="isSubmitting" aria-live="polite">
					<span class="ai-ir__loading-spinner" aria-hidden="true"></span>
					<span class="ai-ir__loading-text">{{ uiLabel('submitting', 'Подбираем регион…') }}</span>
				</div>

				<div class="ai-ir__progress" aria-hidden="true" v-if="!showResults">
					<div class="ai-ir__progress-meta">
						<span class="ai-ir__progress-step">
							{{ stepQuestionLabel }} {{ step }} {{ uiLabel('of', 'of') }} {{ totalSteps }}
						</span>
						<span class="ai-ir__progress-percent">{{ progressPercent }}%</span>
					</div>
					<div class="ai-ir__progress-track">
						<span class="ai-ir__progress-fill" :style="{ width: progressPercent + '%' }"></span>
					</div>
				</div>

				<template v-if="!showResults">
					<div v-if="currentStepOptions.length" class="ai-ir__step ai-ir__step--cards">
						<div class="ai-ir__question-row">
							<h3 class="ai-ir__question">{{ stepQuestion }}</h3>
							<span v-if="stepSelectionHint" class="ai-ir__question-hint">{{ stepSelectionHint }}</span>
						</div>
						<div v-if="isAnySelectStep" class="ai-ir__select-wrap">
							<div class="ai-ir__field" :class="{ 'is-filled': !!answers[step] }">
								<select
									class="ai-ir__select"
									:id="'ai-ir-step-' + step"
									:name="'ai_ir_step_' + step"
									:value="answers[step] || ''"
									:aria-label="stepQuestion"
									@change="onSelectChange"
								>
									<option disabled value="">{{ uiLabel('choose', 'Выберите вариант') }}</option>
									<option
										v-for="option in currentStepOptions"
										:key="'select-' + step + '-' + option.value"
										:value="option.value"
									>{{ option.label }}</option>
								</select>
								<span class="ai-ir__field-chevron" aria-hidden="true">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
							</div>
						</div>
						<fieldset v-else class="ai-ir__options" :aria-label="stepQuestion">
							<div
								class="ai-ir__options-grid"
								:class="[
									'ai-ir__options-grid--' + currentStepOptions.length,
									step === 4 ? 'ai-ir__options-grid--step-4' : ''
								]"
							>
								<label
									v-for="option in currentStepOptions"
									:key="'step-' + step + '-' + option.value"
									class="ai-ir__option-card"
									:class="{ 'is-checked': isOptionSelected(option.value) }"
								>
									<input
										class="ai-ir__option-input"
										:type="isMultiStep ? 'checkbox' : 'radio'"
										:name="'ai_ir_step_' + step + (isMultiStep ? '[]' : '')"
										:value="option.value"
										:checked="isOptionSelected(option.value)"
										@change="onOptionChange(option, $event)"
									>
									<span class="ai-ir__option-photo">
										<img
											v-if="option.image"
											class="ai-ir__option-image"
											:src="option.image"
											:alt="option.label"
											width="273"
											height="360"
											loading="lazy"
											decoding="async"
										>
										<span class="ai-ir__option-radio" aria-hidden="true"></span>
									</span>
									<span class="ai-ir__option-label">{{ option.label }}</span>
								</label>
							</div>
						</fieldset>
					<div class="ai-ir__actions" :class="{ 'ai-ir__actions--split': step > 1 }">
						<button v-if="step > 1" type="button" class="ai-ir__prev" @click="prevStep" :disabled="isSubmitting">{{ uiLabel('back', 'Назад') }}</button>
						<button v-if="step < maxStep" type="button" class="ai-ir__next" @click="nextStep">{{ uiLabel('next', 'Далее') }}</button>
						<button
							v-if="step === maxStep"
							type="button"
							class="ai-ir__next ai-ir__submit"
							@click="submitAnswers"
							:disabled="isSubmitting"
						>
							{{ isSubmitting ? uiLabel('submitting', 'Подбираем регион…') : uiLabel('submit', 'Подобрать регион') }}
						</button>
					</div>
					<p v-if="step === maxStep && submitError" class="ai-ir__submit-msg ai-ir__submit-msg--error">{{ submitError }}</p>
				</div>

				<div v-else class="ai-ir__step ai-ir__step--empty">
						<h3 class="ai-ir__question">{{ stepQuestion }}</h3>
						<p class="ai-ir__placeholder">{{ uiLabel('no_data', 'Нет вариантов для этого шага.') }}</p>
						<div class="ai-ir__actions" :class="{ 'ai-ir__actions--split': step > 1 }">
							<button v-if="step > 1" type="button" class="ai-ir__prev" @click="prevStep" :disabled="isSubmitting">{{ uiLabel('back', 'Назад') }}</button>
							<button v-if="step < maxStep" type="button" class="ai-ir__next" @click="nextStep">{{ uiLabel('next', 'Далее') }}</button>
							<button
								v-if="step === maxStep"
								type="button"
								class="ai-ir__next ai-ir__submit"
								@click="submitAnswers"
								:disabled="isSubmitting"
							>
								{{ isSubmitting ? uiLabel('submitting', 'Подбираем регион…') : uiLabel('submit', 'Подобрать регион') }}
							</button>
						</div>
						<p v-if="step === maxStep && submitError" class="ai-ir__submit-msg ai-ir__submit-msg--error">{{ submitError }}</p>
					</div>
				</template>

			<div v-else class="ai-ir__step ai-ir__step--results">
				<div class="ai-ir__results-header">
					<h3 class="ai-ir__user-goal-text">{{ uiLabel('user_goal_placeholder', 'Ваш результат') }}</h3>
					<h4 class="ai-ir__results-title">
						{{ uiLabel('results_title', 'Мы подобрали для вас лучшие регионы') }}
					</h4>
				</div>

				<div v-if="matchedRegions.length" class="ai-ir__results">

					<article class="ai-ir__result-card ai-ir__result-card--main">
						<div class="ai-ir__result-card-inner">
							<div v-if="matchedRegions[0].image" class="ai-ir__result-card-photo">
								<img :src="matchedRegions[0].image" :alt="matchedRegions[0].name" loading="lazy">
							</div>
							<div class="ai-ir__result-card-body">
							<div class="ai-ir__result-head">
								<h4 class="ai-ir__result-name">{{ matchedRegions[0].name }}</h4>
								<span class="ai-ir__result-badge ai-ir__result-badge--green">
									{{ matchedRegions[0].match_percent }}%
									<span class="ai-ir__result-badge-label">{{ uiLabel('match', 'совпадение') }}</span>
								</span>
							</div>
								<div v-if="matchedRegions[0].description_html" class="ai-ir__result-text" dir="auto">
									<div class="ai-ir__result-text-preview" v-html="descriptionHtmlPreview(matchedRegions[0])"></div>
									<div v-show="isRegionExpanded(matchedRegions[0])" class="ai-ir__result-text-more" v-html="descriptionHtmlMore(matchedRegions[0])"></div>
									<button v-if="descriptionHtmlHasMore(matchedRegions[0])" type="button" class="ai-ir__result-more-btn" @click="toggleRegionDescription(matchedRegions[0])">
										{{ isRegionExpanded(matchedRegions[0]) ? uiLabel('hide', 'Скрыть') : uiLabel('more', 'Подробнее') }}
									</button>
								</div>
								<p v-else-if="matchedRegions[0].description" class="ai-ir__result-text">{{ matchedRegions[0].description }}</p>
							</div>
						</div>
					</article>

					<div v-if="matchedRegions.length > 1" class="ai-ir__results-secondary">
						<p class="ai-ir__results-secondary-label">{{ otherVariantsLabel }}</p>
						<div class="ai-ir__results-secondary-grid">
							<article
								v-for="(region, index) in matchedRegions.slice(1)"
								:key="'region-' + (region.category_id || region.name)"
								class="ai-ir__result-card ai-ir__result-card--small"
							>
								<div class="ai-ir__result-card-inner">
									<div v-if="region.image" class="ai-ir__result-card-photo">
										<img :src="region.image" :alt="region.name" loading="lazy">
									</div>
									<div class="ai-ir__result-card-body">
										<div class="ai-ir__result-head">
											<h4 class="ai-ir__result-name">{{ region.name }}</h4>
											<span class="ai-ir__result-badge ai-ir__result-badge--purple">
												{{ region.match_percent }}%
											</span>
										</div>
										<div v-if="region.description_html" class="ai-ir__result-text ai-ir__result-text--small" dir="auto">
											<div class="ai-ir__result-text-preview" v-html="descriptionHtmlPreview(region)"></div>
											<div v-show="isRegionExpanded(region)" class="ai-ir__result-text-more" v-html="descriptionHtmlMore(region)"></div>
											<button v-if="descriptionHtmlHasMore(region)" type="button" class="ai-ir__result-more-btn" @click="toggleRegionDescription(region)">
												{{ isRegionExpanded(region) ? uiLabel('hide', 'Скрыть') : uiLabel('more', 'Подробнее') }}
											</button>
										</div>
										<p v-else-if="region.description" class="ai-ir__result-text ai-ir__result-text--small">{{ region.description }}</p>
									</div>
								</div>
							</article>
						</div>
					</div>

				</div>
				<p v-else class="ai-ir__placeholder">{{ uiLabel('no_regions', 'Не удалось подобрать регионы.') }}</p>

					<div class="ai-ir__actions">
						<button type="button" class="ai-ir__prev" @click="showResults = false">{{ uiLabel('back', 'Назад') }}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
