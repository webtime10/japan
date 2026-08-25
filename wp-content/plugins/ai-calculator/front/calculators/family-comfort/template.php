<?php
/**
 * Family Comfort Calculator.
 *
 * @var array<string, mixed> $ai_family_comfort
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attribute_options = isset( $ai_family_comfort['attribute_options'] ) && is_array( $ai_family_comfort['attribute_options'] )
	? $ai_family_comfort['attribute_options']
	: array();

$category_options = isset( $ai_family_comfort['category_options'] ) && is_array( $ai_family_comfort['category_options'] )
	? $ai_family_comfort['category_options']
	: array();

$cards = isset( $ai_family_comfort['cards'] ) && is_array( $ai_family_comfort['cards'] )
	? $ai_family_comfort['cards']
	: array();

$has_db_data = ! empty( $attribute_options ) && ! empty( $category_options );

$calculator_title = ai_calculator_get_custom_title(
	'family_comfort',
	__( 'Family Comfort Calculator', 'ai-calculator' )
);
?>

<section class="ai-family-comfort" aria-labelledby="ai-family-comfort-title">
	<h2 id="ai-family-comfort-title" class="ai-family-comfort__title">
		<?php echo esc_html( (string) $calculator_title ); ?>
	</h2>
	<div class="ai-family-comfort__inner">
		<div class="ai-family-comfort__panel">
			<form class="ai-family-comfort__form" action="#" method="get">
				<div class="ai-family-comfort__field">
					<label for="ai-family-comfort-category"><?php esc_html_e( 'Интересы', 'ai-calculator' ); ?></label>
					<select id="ai-family-comfort-category" name="family_category" <?php disabled( ! $has_db_data ); ?>>
						<?php if ( ! $has_db_data ) : ?>
							<option value=""><?php esc_html_e( 'Нет данных', 'ai-calculator' ); ?></option>
						<?php else : ?>
							<option value="" selected><?php esc_html_e( 'Выберите интерес', 'ai-calculator' ); ?></option>
							<?php foreach ( $category_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<div class="ai-family-comfort__field">
					<label for="ai-family-comfort-age"><?php esc_html_e( 'Возраст детей', 'ai-calculator' ); ?></label>
					<select id="ai-family-comfort-age" name="family_attribute" <?php disabled( ! $has_db_data ); ?>>
						<?php if ( ! $has_db_data ) : ?>
							<option value=""><?php esc_html_e( 'Нет данных', 'ai-calculator' ); ?></option>
						<?php else : ?>
							<option value="" selected><?php esc_html_e( 'Выберите возраст детей', 'ai-calculator' ); ?></option>
							<?php foreach ( $attribute_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<button class="ai-family-comfort__button" type="button" <?php disabled( ! $has_db_data ); ?>>
					<?php echo esc_html__( 'Подобрать направление', 'ai-calculator' ); ?>
				</button>
			</form>
		</div>

		<div class="ai-family-comfort__notch" aria-hidden="true"></div>

		<h3 class="ai-family-comfort__subtitle">
			<?php echo esc_html__( 'Рекомендации по направлениям', 'ai-calculator' ); ?>
		</h3>

		<p class="ai-family-comfort__empty" id="ai-family-comfort-empty" hidden>
			<?php esc_html_e( 'Для выбранных параметров пока нет направлений. Добавьте товары в категорию, заполните карточку в блоке «Калькулятор семейного отдыха» и отметьте атрибуты.', 'ai-calculator' ); ?>
		</p>

		<div class="ai-family-comfort__slider" id="ai-family-comfort-slider">
			<div class="ai-family-comfort__slider-shell">
				<button class="ai-family-comfort__slider-btn ai-family-comfort__slider-btn--prev" type="button" data-fc-prev aria-label="<?php esc_attr_e( 'Предыдущие направления', 'ai-calculator' ); ?>">
					<svg width="12" height="20" viewBox="0 0 12 20" fill="none" aria-hidden="true"><path d="M2 2L10 10L2 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<div class="ai-family-comfort__slider-viewport" dir="ltr">
					<div class="ai-family-comfort__slider-track" id="ai-family-comfort-cards"></div>
				</div>
				<button class="ai-family-comfort__slider-btn ai-family-comfort__slider-btn--next" type="button" data-fc-next aria-label="<?php esc_attr_e( 'Следующие направления', 'ai-calculator' ); ?>">
					<svg width="12" height="20" viewBox="0 0 12 20" fill="none" aria-hidden="true"><path d="M10 2L2 10L10 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			</div>
		</div>

		<div class="ai-family-comfort__card-pool" id="ai-family-comfort-card-pool" hidden>
			<?php foreach ( $cards as $card ) : ?>
				<?php
				$image          = ! empty( $card['image'] ) ? (string) $card['image'] : '';
				$url            = ! empty( $card['url'] ) ? (string) $card['url'] : '';
				$title          = ! empty( $card['title'] ) ? (string) $card['title'] : '';
				$subtitle       = ! empty( $card['subtitle'] ) ? trim( (string) $card['subtitle'] ) : '';
				$tags           = ! empty( $card['tags'] ) && is_array( $card['tags'] ) ? $card['tags'] : array();
				$attribute_ids  = ! empty( $card['attribute_ids'] ) && is_array( $card['attribute_ids'] ) ? $card['attribute_ids'] : array();
				$attribute_attr = implode( ',', array_map( 'strval', $attribute_ids ) );
				$rating         = isset( $card['rating'] ) ? (float) $card['rating'] : 4.5;
				$rating_percent = isset( $card['rating_percent'] ) ? (int) $card['rating_percent'] : (int) round( ( $rating / 5 ) * 100 );
				$rating_label   = number_format( $rating, 1, '.', '' );
				$show_subtitle  = '' !== $subtitle && $subtitle !== $title;
				?>
				<article
					class="ai-family-comfort__card"
					hidden
					data-family-category="<?php echo esc_attr( (string) ( $card['category_id'] ?? '' ) ); ?>"
					data-family-attributes="<?php echo esc_attr( $attribute_attr ); ?>"
				>
					<header class="ai-family-comfort__card-head">
						<?php if ( '' !== $url ) : ?>
							<h4 class="ai-family-comfort__card-title">
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $title ); ?></a>
							</h4>
						<?php else : ?>
							<h4 class="ai-family-comfort__card-title"><?php echo esc_html( $title ); ?></h4>
						<?php endif; ?>

						<?php if ( $show_subtitle ) : ?>
							<p class="ai-family-comfort__card-subtitle"><?php echo esc_html( $subtitle ); ?></p>
						<?php endif; ?>
					</header>

					<?php if ( ! empty( $card['text'] ) ) : ?>
						<p class="ai-family-comfort__card-text"><?php echo esc_html( $card['text'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $tags ) ) : ?>
						<div class="ai-family-comfort__tags">
							<?php foreach ( $tags as $tag ) : ?>
								<span><?php echo esc_html( $tag ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( '' !== $image ) : ?>
						<div class="ai-family-comfort__card-media">
							<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
						</div>
					<?php endif; ?>

					<div class="ai-family-comfort__rating" aria-label="<?php echo esc_attr( sprintf( __( 'Рейтинг %s из 5', 'ai-calculator' ), $rating_label ) ); ?>">
						<small>5.0 /</small>
						<span aria-hidden="true">★</span>
						<strong><?php echo esc_html( $rating_label ); ?></strong>
					</div>
					<div class="ai-family-comfort__bar" aria-hidden="true">
						<span style="width: <?php echo esc_attr( (string) max( 0, min( 100, $rating_percent ) ) ); ?>%;"></span>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
