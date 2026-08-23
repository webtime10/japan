<?php
/**
 * Budget Calculator — front markup.
 *
 * @var array<string, mixed> $ai_budget
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require AI_CALCULATOR_FRONT_PATH . 'inc/template-directives.php';

$picker_lang = ai_calculator_datetimepicker_lang();
$is_rtl      = ! empty( $picker_lang['rtl'] );

$result_data = isset( $ai_budget['catalog_cards'] ) && is_array( $ai_budget['catalog_cards'] )
	? $ai_budget['catalog_cards']
	: array();

$order_modal_meta = ai_calculator_budget_order_modal_meta();
?>
<section class="ai-calculator ai-calculator-budget ai-bg<?php echo $is_rtl ? ' ai-bg--rtl' : ''; ?>" data-ai-bg-step="1"<?php echo $is_rtl ? ' dir="rtl"' : ''; ?>>
	<div class="container-4">
		<h2 class="ai-bg__page-title"><?php echo esc_html( $labels['title'] ); ?></h2>

		<script>
			window.aiCalculatorData = <?php echo wp_json_encode( $result_data, JSON_UNESCAPED_UNICODE ); ?>;
			window.aiCalculatorBudget = window.aiCalculatorBudget || {};
			window.aiCalculatorBudget.labels = <?php echo wp_json_encode( $labels, JSON_UNESCAPED_UNICODE ); ?>;
			window.aiCalculatorBudget.swissRegions = <?php echo wp_json_encode( ai_calculator_budget_swiss_regions(), JSON_UNESCAPED_UNICODE ); ?>;

			console.log(JSON.parse(JSON.stringify(window.aiCalculatorData)));
		</script>

		<style>
			#ai-calculator-budget-app[v-cloak] {
				display: none !important;
			}
		</style>

		<div id="ai-calculator-budget-app" v-cloak style="display:none">
			<input
				type="text"
				class="ai-calculator-hp"
				name="ai_calculator_hp"
				v-model="honeypot"
				tabindex="-1"
				autocomplete="off"
				aria-hidden="true"
			>
			<div class="into-ai-calculator-budget">
				<div class="ai-bg__frame">
					<div class="ai-bg__widget" :class="{ 'ai-bg__widget--result': isResultStep }" :data-step="step">

						<div v-if="!isResultStep" class="ai-bg__progress" aria-hidden="true">
							<div class="ai-bg__progress-meta">
								<span class="ai-bg__progress-step">
									{{ uiLabel('question') }} {{ step }} {{ uiLabel('of') }} {{ questionStepsCount }}
								</span>
								<span class="ai-bg__progress-percent">{{ progressPercent }}%</span>
							</div>
							<div class="ai-bg__progress-track">
								<span class="ai-bg__progress-fill" :style="{ width: progressPercent + '%' }"></span>
							</div>
						</div>

						<div v-if="step === 1 && datesCard" class="ai-bg__step ai-bg__step--dates">
							<h3 class="ai-bg__question">{{ getCardTitle(datesCard) }}</h3>

							<fieldset class="ai-bg__options">
								<legend class="screen-reader-text">{{ getCardBlock(datesCard, 1) }}</legend>

								<div class="ai-bg__option" :class="{ 'ai-bg__option--active': answers.catalog.trip_dates.dateMode === 'approx' }" data-ai-bg-option="approx">
									<label class="ai-bg__option-head">
										<input class="ai-bg__option-input" type="radio" name="ai_bg_date_mode" value="approx" v-model="answers.catalog.trip_dates.dateMode">
										<span class="ai-bg__radio" aria-hidden="true"></span>
										<span class="ai-bg__option-label">{{ getCardBlock(datesCard, 2) }}</span>
									</label>

									<div class="ai-bg__fields">
										<div class="ai-bg__field ai-bg__field--date" data-ai-bg-date-from>
											<button type="button" class="ai-bg__date-trigger calend calend-from" :aria-label="getCardBlock(datesCard, 3)">
												<img src="<?php echo esc_url( ai_calculator_budget_image_url( 'calendar' ) ); ?>" alt="" width="20" height="20" loading="lazy" decoding="async">
											</button>
											<input
												type="text"
												class="ai-bg__date-input ai-bg__date-input--from"
												id="ai-bg-date-from"
												name="ai_bg_date_from"
												data-date-role="from"
												v-model="answers.catalog.trip_dates.dateFrom"
												:placeholder="getCardBlock(datesCard, 3)"
												autocomplete="off"
											>
											<span class="ai-bg__field-chevron" aria-hidden="true">
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</span>
										</div>

										<div class="ai-bg__field ai-bg__field--date" data-ai-bg-date-to>
											<button type="button" class="ai-bg__date-trigger calend calend-to" :aria-label="getCardBlock(datesCard, 4)">
												<img src="<?php echo esc_url( ai_calculator_budget_image_url( 'calendar' ) ); ?>" alt="" width="20" height="20" loading="lazy" decoding="async">
											</button>
											<input
												type="text"
												class="ai-bg__date-input ai-bg__date-input--to"
												id="ai-bg-date-to"
												name="ai_bg_date_to"
												data-date-role="to"
												v-model="answers.catalog.trip_dates.dateTo"
												:placeholder="getCardBlock(datesCard, 4)"
												autocomplete="off"
											>
											<span class="ai-bg__field-chevron" aria-hidden="true">
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</span>
										</div>
									</div>
								</div>

								<div class="ai-bg__option" :class="{ 'ai-bg__option--active': answers.catalog.trip_dates.dateMode === 'month' }" data-ai-bg-option="month">
									<label class="ai-bg__option-head">
										<input class="ai-bg__option-input" type="radio" name="ai_bg_date_mode" value="month" v-model="answers.catalog.trip_dates.dateMode">
										<span class="ai-bg__radio" aria-hidden="true"></span>
										<span class="ai-bg__option-label">{{ getCardBlock(datesCard, 5) }}</span>
									</label>

									<div class="ai-bg__fields">
										<button type="button" class="ai-bg__field" data-ai-bg-month :disabled="answers.catalog.trip_dates.dateMode !== 'month'">
											<span class="ai-bg__field-icon" aria-hidden="true">
												<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M6.667 2.5V4.167M13.333 2.5V4.167M3.333 8.333h13.334M4.167 16.667h11.666c.92 0 1.667-.746 1.667-1.667V5.833c0-.92-.746-1.666-1.667-1.666H4.167c-.92 0-1.667.746-1.667 1.666v9.167c0 .921.746 1.667 1.667 1.667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</span>
											<span class="ai-bg__field-text">{{ getCardProductName(datesCard, 0) }}</span>
											<span class="ai-bg__field-chevron" aria-hidden="true">
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</span>
										</button>

										<div class="ai-bg__field ai-bg__field--input">
											<input
												type="number"
												class="ai-bg__input"
												name="ai_bg_duration_days"
												v-model="answers.catalog.trip_dates.durationDays"
												min="1"
												:placeholder="getCardBlock(datesCard, 6)"
												:disabled="answers.catalog.trip_dates.dateMode !== 'month'"
												data-ai-bg-duration
											>
										</div>
									</div>
								</div>
							</fieldset>

							<div class="ai-bg__actions">
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="step === 2 && travelersCard" class="ai-bg__step ai-bg__step--travelers">
							<h3 class="ai-bg__question">{{ getCardTitle(travelersCard) }}</h3>

							<div class="ai-bg__travelers">
								<label
									v-if="getCardBlock(travelersCard, 1)"
									class="ai-bg__travelers-label"
									:for="'ai-bg-qty-block2-' + travelersCard.category_id"
								>{{ getCardBlock(travelersCard, 1) }}</label>

								<div class="ai-bg__field ai-bg__field--travelers">
									<span class="ai-bg__field-icon" aria-hidden="true">
										<img src="<?php echo esc_url( ai_calculator_budget_image_url( 'users' ) ); ?>" alt="" width="24" height="24" loading="lazy" decoding="async">
									</span>
									<select
										class="ai-bg__select"
										:id="'ai-bg-qty-block2-' + travelersCard.category_id"
										v-model="answers.catalog.travelers.quantity"
										:aria-label="getCardBlock(travelersCard, 1) || getTravelersPlaceholder(travelersCard)"
									>
										<option disabled value="">{{ getTravelersPlaceholder(travelersCard) }}</option>
										<option v-for="n in quantityOptions" :key="'block2-' + n" :value="String(n)">{{ n }}</option>
									</select>
									<span class="ai-bg__field-chevron" aria-hidden="true">
										<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</span>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="step === 3 && childrenCard" class="ai-bg__step ai-bg__step--children">
							<h3 class="ai-bg__question">{{ getCardTitle(childrenCard) }}</h3>

							<div class="ai-bg__children-choice" role="radiogroup" :aria-label="getCardTitle(childrenCard)">
								<label class="ai-bg__option-head">
									<input
										class="ai-bg__option-input"
										type="radio"
										name="ai_bg_has_children"
										value="da"
										v-model="answers.catalog.children.hasChildren"
										@change="onChildrenAnswerChange"
									>
									<span class="ai-bg__radio" aria-hidden="true"></span>
									<span class="ai-bg__option-label">Да</span>
								</label>
								<label class="ai-bg__option-head">
									<input
										class="ai-bg__option-input"
										type="radio"
										name="ai_bg_has_children"
										value="net"
										v-model="answers.catalog.children.hasChildren"
										@change="onChildrenAnswerChange"
									>
									<span class="ai-bg__radio" aria-hidden="true"></span>
									<span class="ai-bg__option-label">Нет</span>
								</label>
							</div>

							<div v-if="shouldShowChildrenSelect()" class="ai-bg__children-fields">
								<div class="ai-bg__children-col">
									<label
										v-if="getCardBlock(childrenCard, 1)"
										class="ai-bg__travelers-label"
										:for="'ai-bg-children-qty-' + childrenCard.category_id"
									>{{ getCardBlock(childrenCard, 1) }}</label>

									<div class="ai-bg__field ai-bg__field--travelers">
										<span class="ai-bg__field-icon" aria-hidden="true">
											<img src="<?php echo esc_url( ai_calculator_budget_image_url( 'users' ) ); ?>" alt="" width="24" height="24" loading="lazy" decoding="async">
										</span>
										<select
											class="ai-bg__select"
											:id="'ai-bg-children-qty-' + childrenCard.category_id"
											v-model="answers.catalog.children.quantity"
											@change="onChildrenQuantityChange"
											:aria-label="getCardBlock(childrenCard, 1) || getChildrenQuantityPlaceholder(childrenCard)"
										>
											<option disabled value="">{{ getChildrenQuantityPlaceholder(childrenCard) }}</option>
											<option v-for="n in quantityOptions" :key="'child-qty-' + n" :value="String(n)">{{ n }}</option>
										</select>
										<span class="ai-bg__field-chevron" aria-hidden="true">
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</span>
									</div>
								</div>

								<div class="ai-bg__children-col ai-bg__children-col--ages">
									<div
										v-for="slot in childrenAgeSlots"
										:key="'child-age-slot-' + slot"
										class="ai-bg__children-age-item"
									>
										<label
											class="ai-bg__travelers-label"
											:for="'ai-bg-children-age-' + slot"
										>{{ getChildOrdinalLabel(slot) }}</label>
										<div class="ai-bg__field ai-bg__field--travelers">
											<select
												class="ai-bg__select"
												:id="'ai-bg-children-age-' + slot"
												:value="getChildAge(slot)"
												@change="setChildAge(slot, $event.target.value)"
												:aria-label="getChildOrdinalLabel(slot)"
											>
												<option disabled value="">{{ getChildOrdinalLabel(slot) }}</option>
												<option v-for="n in ageOptions" :key="'child-age-' + slot + '-' + n" :value="String(n)">{{ n }}</option>
											</select>
											<span class="ai-bg__field-chevron" aria-hidden="true">
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</span>
										</div>
									</div>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="step === 4 && regionCard" class="ai-bg__step ai-bg__step--region">
							<h3 class="ai-bg__question">{{ getCardTitle(regionCard) }}</h3>
							<p v-if="getCardBlock(regionCard, 3)" class="ai-bg__question-hint">{{ getCardBlock(regionCard, 3) }}</p>

							<div class="ai-bg__region">
								<label
									v-if="getCardBlock(regionCard, 1)"
									class="ai-bg__travelers-label"
									:for="'ai-bg-region-' + regionCard.category_id"
								>{{ getCardBlock(regionCard, 1) }}</label>

								<div class="ai-bg__field ai-bg__field--travelers">
									<select
										class="ai-bg__select"
										:id="'ai-bg-region-' + regionCard.category_id"
										v-model="answers.catalog.region.region"
										:aria-label="getCardBlock(regionCard, 1) || getRegionPlaceholder(regionCard)"
									>
										<option disabled value="">{{ getRegionPlaceholder(regionCard) }}</option>
										<option
											v-for="item in swissRegionsList"
											:key="'region-' + item.value"
											:value="item.value"
										>{{ item.label }}</option>
									</select>
									<span class="ai-bg__field-chevron" aria-hidden="true">
										<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</span>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="step === 5 && housingCard" class="ai-bg__step ai-bg__step--housing">
							<h3 class="ai-bg__question">{{ getHousingQuestion(housingCard) }}</h3>

							<div class="ai-bg__housing-options" role="radiogroup" :aria-label="getHousingQuestion(housingCard)">
								<div class="ai-bg__housing-grid">
									<label
										v-for="option in getHousingOptions(housingCard)"
										:key="'housing-' + housingCard.category_id + '-' + option.value"
										class="ai-bg__housing-card"
										:class="{ 'ai-bg__housing-card--active': answers.catalog.housing.housingType === option.value }"
									>
										<input
											class="ai-bg__housing-input"
											type="radio"
											name="ai_bg_housing"
											:value="option.value"
											v-model="answers.catalog.housing.housingType"
										>
										<span class="ai-bg__housing-photo">
											<img class="ai-bg__housing-image" :src="option.image" :alt="option.label" width="320" height="220" loading="lazy" decoding="async">
											<span class="ai-bg__housing-radio" aria-hidden="true"></span>
										</span>
										<span class="ai-bg__housing-label">{{ option.label }}</span>
									</label>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="step === 6 && comfortLevelCard" class="ai-bg__step ai-bg__step--comfort">
							<h3 class="ai-bg__question">{{ getComfortLevelQuestion(comfortLevelCard) }}</h3>

							<div class="ai-bg__housing-options" role="radiogroup" :aria-label="getComfortLevelQuestion(comfortLevelCard)">
								<div class="ai-bg__housing-grid ai-bg__housing-grid--3">
									<label
										v-for="option in getComfortLevelOptions(comfortLevelCard)"
										:key="'comfort-' + comfortLevelCard.category_id + '-' + option.value"
										class="ai-bg__housing-card"
										:class="{ 'ai-bg__housing-card--active': answers.catalog.comfort.comfortLevel === option.value }"
									>
										<input
											class="ai-bg__housing-input"
											type="radio"
											name="ai_bg_comfort"
											:value="option.value"
											v-model="answers.catalog.comfort.comfortLevel"
										>
										<span class="ai-bg__housing-photo">
											<img class="ai-bg__housing-image" :src="option.image" :alt="option.label" width="273" height="202" loading="lazy" decoding="async">
											<span class="ai-bg__housing-radio" aria-hidden="true"></span>
										</span>
										<span class="ai-bg__housing-label">{{ option.label }}</span>
									</label>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-if="step === 7 && entertainmentCard" class="ai-bg__step ai-bg__step--entertainment">
							<h3 class="ai-bg__question">{{ getEntertainmentQuestion(entertainmentCard) }}</h3>

							<div class="ai-bg__housing-options" role="radiogroup" :aria-label="getEntertainmentQuestion(entertainmentCard)">
								<div class="ai-bg__housing-grid ai-bg__housing-grid--3">
									<label
										v-for="option in getEntertainmentOptions(entertainmentCard)"
										:key="'entertainment-' + entertainmentCard.category_id + '-' + option.value"
										class="ai-bg__housing-card"
										:class="{ 'ai-bg__housing-card--active': answers.catalog.entertainment.entertainmentLevel === option.value }"
									>
										<input
											class="ai-bg__housing-input"
											type="radio"
											name="ai_bg_entertainment"
											:value="option.value"
											v-model="answers.catalog.entertainment.entertainmentLevel"
										>
										<span class="ai-bg__housing-photo">
											<img class="ai-bg__housing-image" :src="option.image" :alt="option.label" width="273" height="202" loading="lazy" decoding="async">
											<span class="ai-bg__housing-radio" aria-hidden="true"></span>
										</span>
										<span class="ai-bg__housing-label">{{ option.label }}</span>
									</label>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="step === 8 && diningCard" class="ai-bg__step ai-bg__step--dining">
							<h3 class="ai-bg__question">{{ getDiningQuestion(diningCard) }}</h3>

							<div class="ai-bg__housing-options" role="radiogroup" :aria-label="getDiningQuestion(diningCard)">
								<div class="ai-bg__housing-grid ai-bg__housing-grid--3">
									<label
										v-for="option in getDiningOptions(diningCard)"
										:key="'dining-' + diningCard.category_id + '-' + option.value"
										class="ai-bg__housing-card"
										:class="{ 'ai-bg__housing-card--active': answers.catalog.dining.diningLevel === option.value }"
									>
										<input
											class="ai-bg__housing-input"
											type="radio"
											name="ai_bg_dining"
											:value="option.value"
											v-model="answers.catalog.dining.diningLevel"
										>
										<span class="ai-bg__housing-photo">
											<img class="ai-bg__housing-image" :src="option.image" :alt="option.label" width="273" height="202" loading="lazy" decoding="async">
											<span class="ai-bg__housing-radio" aria-hidden="true"></span>
										</span>
										<span class="ai-bg__housing-label">{{ option.label }}</span>
									</label>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="step === 9 && carRentalCard" class="ai-bg__step ai-bg__step--car-rental">
							<h3 class="ai-bg__question">{{ getCarRentalQuestion(carRentalCard) }}</h3>

							<div class="ai-bg__housing-options" role="radiogroup" :aria-label="getCarRentalQuestion(carRentalCard)">
								<div class="ai-bg__housing-grid">
									<label
										v-for="option in getCarRentalOptions(carRentalCard)"
										:key="'car-rental-' + carRentalCard.category_id + '-' + option.value"
										class="ai-bg__housing-card"
										:class="{ 'ai-bg__housing-card--active': answers.catalog.car_rental.carRental === option.value }"
									>
										<input
											class="ai-bg__housing-input"
											type="radio"
											name="ai_bg_car_rental"
											:value="option.value"
											v-model="answers.catalog.car_rental.carRental"
										>
										<span class="ai-bg__housing-photo">
											<img class="ai-bg__housing-image" :src="option.image" :alt="option.label" width="320" height="220" loading="lazy" decoding="async">
											<span class="ai-bg__housing-radio" aria-hidden="true"></span>
										</span>
										<span class="ai-bg__housing-label">{{ option.label }}</span>
									</label>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="step === 10 && carClassCard" class="ai-bg__step ai-bg__step--car-class">
							<h3 class="ai-bg__question">{{ getCarClassQuestion(carClassCard) }}</h3>

							<div class="ai-bg__housing-options" role="radiogroup" :aria-label="getCarClassQuestion(carClassCard)">
								<div class="ai-bg__housing-grid ai-bg__housing-grid--3">
									<label
										v-for="option in getCarClassOptions(carClassCard)"
										:key="'car-class-' + carClassCard.category_id + '-' + option.value"
										class="ai-bg__housing-card"
										:class="{ 'ai-bg__housing-card--active': answers.catalog.car_class.carClass === option.value }"
									>
										<input
											class="ai-bg__housing-input"
											type="radio"
											name="ai_bg_car_class"
											:value="option.value"
											v-model="answers.catalog.car_class.carClass"
										>
										<span class="ai-bg__housing-photo">
											<img class="ai-bg__housing-image" :src="option.image" :alt="option.label" width="273" height="202" loading="lazy" decoding="async">
											<span class="ai-bg__housing-radio" aria-hidden="true"></span>
										</span>
										<span class="ai-bg__housing-label">{{ option.label }}</span>
									</label>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="step === 11 && budgetPriorityCard" class="ai-bg__step ai-bg__step--budget-priority">
							<h3 class="ai-bg__question">{{ getBudgetPriorityQuestion(budgetPriorityCard) }}</h3>

							<div class="ai-bg__housing-options" role="radiogroup" :aria-label="getBudgetPriorityQuestion(budgetPriorityCard)">
								<div class="ai-bg__housing-grid ai-bg__housing-grid--3">
									<label
										v-for="option in getBudgetPriorityOptions(budgetPriorityCard)"
										:key="'budget-priority-' + budgetPriorityCard.category_id + '-' + option.value"
										class="ai-bg__housing-card"
										:class="{ 'ai-bg__housing-card--active': answers.catalog.budget_priority.budgetPriority === option.value }"
									>
										<input
											class="ai-bg__housing-input"
											type="radio"
											name="ai_bg_budget_priority"
											:value="option.value"
											v-model="answers.catalog.budget_priority.budgetPriority"
										>
										<span class="ai-bg__housing-photo">
											<img class="ai-bg__housing-image" :src="option.image" :alt="option.label" width="273" height="202" loading="lazy" decoding="async">
											<span class="ai-bg__housing-radio" aria-hidden="true"></span>
										</span>
										<span class="ai-bg__housing-label">{{ option.label }}</span>
									</label>
								</div>
							</div>

							<div class="ai-bg__actions ai-bg__actions--split">
								<button type="button" class="ai-bg__prev" @click="prevStep">
									{{ uiLabel('back') }}
								</button>
								<button type="button" class="ai-bg__next" @click="nextStep">
									{{ uiLabel('next') }}
								</button>
							</div>
						</div>

						<div v-else-if="isResultStep" class="ai-bg__step ai-bg__step--result">
							<div class="ai-bg__result">
								<div class="ai-bg__result-head">
									<img
										class="ai-bg__result-money"
										:src="getBudgetImage('dengy')"
										alt=""
										width="56"
										height="56"
										loading="lazy"
										decoding="async"
									>
									<div class="ai-bg__result-head-text">
										<h3 class="ai-bg__result-title">{{ getResultTitle(resultCard) }}</h3>
										<span class="item_res_m">
											<span
												v-for="(metaPart, metaIndex) in getResultMetaParts()"
												:key="'result-meta-' + metaIndex"
												class="item_res_m__part"
											>{{ metaPart }}<span v-if="metaIndex < getResultMetaParts().length - 1">, </span></span>
										</span>
									</div>
								</div>

								<div class="ai-bg__result-grid">
									<div
										v-for="(row, index) in getResultRows(resultCard)"
										:key="'result-row-' + resultCard.category_id + '-' + index"
										class="ai-bg__result-cell"
									>
										<img class="ai-bg__result-cell-icon" :src="row.image" :alt="row.label" width="48" height="48" loading="lazy" decoding="async">
										<span class="ai-bg__result-cell-label">{{ row.label }}</span>
										<span class="ai-bg__result-cell-price" :class="row.priceClass">{{ row.price || '170$' }}</span>
									</div>
								</div>

								<div class="ai-bg__result-totals">
									<div v-if="getResultBaseTotal()" class="item_total_base">Сумма: {{ getResultBaseTotal() }}</div>
									<div v-if="getPriorityAdjustmentText()" class="item_total_adjustment">Коррекция приоритета: {{ getPriorityAdjustmentText() }}</div>
									<div class="item_total">Итоговая: {{ getResultTotal() }}</div>
								</div>
								<p v-if="submitError" class="text-danger small mb-0">{{ submitError }}</p>
								<p v-if="isSubmitting" class="text-muted small mb-0">Отправка ответов…</p>

								<div class="ai-bg__actions ai-bg__actions--result">
									<button type="button" class="ai-bg__order-route" @click="openOrderModal">
										<span>{{ orderRouteLabel }}</span>
										<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
											<path d="M4 12L12 4M12 4H6M12 4V10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</button>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>

			<div
				v-if="isSubmitting"
				class="ai-bg__submit-overlay"
				role="status"
				aria-live="polite"
				dir="rtl"
			>
				<div class="ai-bg__submit-box">
					<span class="ai-bg__submit-spinner" aria-hidden="true"></span>
					<span class="ai-bg__submit-title">מחשב את התקציב</span>
					<span class="ai-bg__submit-text">אנא המתן, הנתונים נשלחים ומעובדים</span>
				</div>
			</div>

			<teleport to="body">
				<div
					v-show="isOrderModalOpen"
					ref="orderOverlay"
					class="ai-bg__overlay"
					@click="closeOrderModal"
					aria-hidden="true"
				></div>
				<div
					v-show="isOrderModalOpen"
					ref="orderModal"
					class="ai-bg__modal"
					role="dialog"
					aria-modal="true"
					:aria-label="orderRouteLabel"
					@click.stop
				>
					<div class="modal-content_wt">
						<div class="form-wrapper-2">
							<?php if ( ! empty( $order_modal_meta['image_url'] ) ) : ?>
								<img
									src="<?php echo esc_url( $order_modal_meta['image_url'] ); ?>"
									alt="<?php echo esc_attr( $order_modal_meta['image_alt'] ); ?>"
									class="map-post-image"
									loading="lazy"
									decoding="async"
								>
							<?php endif; ?>

							<?php if ( ! empty( $order_modal_meta['title'] ) ) : ?>
								<h3><?php echo esc_html( $order_modal_meta['title'] ); ?></h3>
							<?php endif; ?>

							<?php if ( ! empty( $order_modal_meta['text'] ) ) : ?>
								<p><?php echo esc_html( $order_modal_meta['text'] ); ?></p>
							<?php endif; ?>

							<div class="ai-bg__modal-form">
								<?php echo ai_calculator_budget_order_modal_form_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</div>
					</div>
					<button
						type="button"
						class="modal-close_wt"
						:aria-label="uiLabel('close')"
						@click="closeOrderModal"
					></button>
				</div>
			</teleport>
		</div>
	</div>
</section>
