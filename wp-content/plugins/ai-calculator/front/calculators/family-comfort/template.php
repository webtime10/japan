<?php
/**
 * Family Comfort Calculator — markup only.
 *
 * @var array<string, mixed> $ai_family_comfort
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$age_options = isset( $ai_family_comfort['age_options'] ) && is_array( $ai_family_comfort['age_options'] )
	? $ai_family_comfort['age_options']
	: array();

$interest_options = isset( $ai_family_comfort['interest_options'] ) && is_array( $ai_family_comfort['interest_options'] )
	? $ai_family_comfort['interest_options']
	: array();

$results = isset( $ai_family_comfort['results'] ) && is_array( $ai_family_comfort['results'] )
	? $ai_family_comfort['results']
	: array();

$cards = array(
	array(
		'title'  => 'Цюрих',
		'text'   => 'Идеален для семей: отличный транспорт и множество интересных мест.',
		'tags'   => array( 'Парки', 'Зоопарк' ),
		'image'  => 'https://images.unsplash.com/photo-1515488764276-beab7607c1e6?auto=format&fit=crop&w=800&q=80',
		'rating' => '4.5',
	),
	array(
		'title'  => 'Интерлакен',
		'text'   => 'Лучший выбор для активного отдыха с детьми, любящими приключения.',
		'tags'   => array( 'Парки', 'Зоопарк' ),
		'image'  => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=800&q=80',
		'rating' => '5',
	),
	array(
		'title'  => 'Люцерн',
		'text'   => 'Подходит для семей: озера и горы, прекрасные активные развлечения.',
		'tags'   => array( 'Парки', 'Зоопарк' ),
		'image'  => 'https://images.unsplash.com/photo-1527668752968-14dc70a27c95?auto=format&fit=crop&w=800&q=80',
		'rating' => '4.8',
	),
);
?>

<section class="ai-family-comfort" aria-labelledby="ai-family-comfort-title">
	<div class="ai-family-comfort__inner">
		<div class="ai-family-comfort__panel">
			<h2 id="ai-family-comfort-title" class="ai-family-comfort__title">
				<?php echo esc_html__( 'Family Comfort Calculator', 'ai-calculator' ); ?>
			</h2>

			<form class="ai-family-comfort__form" action="#" method="get">
				<div class="ai-family-comfort__field">
					<label for="ai-family-comfort-age"><?php echo esc_html__( 'Возраст детей', 'ai-calculator' ); ?></label>
					<select id="ai-family-comfort-age" name="family_age">
						<?php foreach ( $age_options as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="ai-family-comfort__field">
					<label for="ai-family-comfort-interest"><?php echo esc_html__( 'Интересы', 'ai-calculator' ); ?></label>
					<select id="ai-family-comfort-interest" name="family_interest">
						<?php foreach ( $interest_options as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<button class="ai-family-comfort__button" type="button">
					<?php echo esc_html__( 'Подобрать направление', 'ai-calculator' ); ?>
				</button>
			</form>

			<?php if ( ! empty( $results ) ) : ?>
				<div class="ai-family-comfort__age-results" aria-live="polite">
					<?php foreach ( $results as $interest_key => $interest_results ) : ?>
						<?php foreach ( $interest_results as $age_key => $result ) : ?>
							<?php
							$result_title = isset( $result['title'] ) ? (string) $result['title'] : '';
							$result_items = isset( $result['items'] ) && is_array( $result['items'] ) ? $result['items'] : array();
							$is_active    = 'nature' === $interest_key && '5-10' === $age_key;
							?>
							<div
								class="ai-family-comfort__age-result<?php echo $is_active ? ' is-active' : ''; ?>"
								data-family-interest-result="<?php echo esc_attr( $interest_key ); ?>"
								data-family-age-result="<?php echo esc_attr( $age_key ); ?>"
							>
								<div class="ai-family-comfort__age-meta">
									<strong><?php echo esc_html( $age_options[ $age_key ] ?? $age_key ); ?></strong>
									<?php if ( $result_title ) : ?>
										<span><?php echo esc_html( $result_title ); ?></span>
									<?php endif; ?>
								</div>

								<ul>
									<?php foreach ( $result_items as $item ) : ?>
										<li><?php echo esc_html( $item ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="ai-family-comfort__notch" aria-hidden="true"></div>

		<h3 class="ai-family-comfort__subtitle">
			<?php echo esc_html__( 'Рекомендации по направлениям', 'ai-calculator' ); ?>
		</h3>

		<div class="ai-family-comfort__cards">
			<?php foreach ( $cards as $card ) : ?>
				<article class="ai-family-comfort__card">
					<h4><?php echo esc_html( $card['title'] ); ?></h4>
					<p><?php echo esc_html( $card['text'] ); ?></p>

					<div class="ai-family-comfort__tags">
						<?php foreach ( $card['tags'] as $tag ) : ?>
							<span><?php echo esc_html( $tag ); ?></span>
						<?php endforeach; ?>
					</div>

					<img src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>">

					<div class="ai-family-comfort__rating">
						<span aria-hidden="true">★</span>
						<strong><?php echo esc_html( $card['rating'] ); ?></strong>
						<small>/ 5.0</small>
					</div>

					<div class="ai-family-comfort__bar" aria-hidden="true">
						<span style="width: <?php echo esc_attr( min( 100, (float) $card['rating'] / 5 * 100 ) ); ?>%;"></span>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
