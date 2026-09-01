<?php
/**
 * Family Comfort directions calculator.
 *
 * @var array<int, string> $age_options
 * @var array<int, string> $interest_options
 * @var array<int, array<string, mixed>> $cards
 * @var bool $has_data
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="ai-family-comfort fcc-family-directions" aria-labelledby="fcc-family-directions-title">
	<h2 id="fcc-family-directions-title" class="ai-family-comfort__title">
		<?php esc_html_e( 'Family Comfort Calculator', 'family-comfort-calc' ); ?>
	</h2>
	<div class="ai-family-comfort__inner">
		<div class="ai-family-comfort__panel">
			<form class="ai-family-comfort__form" action="#" method="get">
				<div class="ai-family-comfort__field">
					<label for="fcc-family-comfort-age"><?php esc_html_e( 'Возраст детей', 'family-comfort-calc' ); ?></label>
					<select id="fcc-family-comfort-age" name="fcc_age" <?php disabled( ! $has_data ); ?>>
						<?php if ( ! $has_data ) : ?>
							<option value=""><?php esc_html_e( 'Нет данных', 'family-comfort-calc' ); ?></option>
						<?php else : ?>
							<option value="" selected><?php esc_html_e( 'Выберите возраст детей', 'family-comfort-calc' ); ?></option>
							<?php foreach ( $age_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( (string) $value ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<div class="ai-family-comfort__field">
					<label for="fcc-family-comfort-interest"><?php esc_html_e( 'Интересы', 'family-comfort-calc' ); ?></label>
					<select id="fcc-family-comfort-interest" name="fcc_interest" <?php disabled( ! $has_data ); ?>>
						<?php if ( ! $has_data ) : ?>
							<option value=""><?php esc_html_e( 'Нет данных', 'family-comfort-calc' ); ?></option>
						<?php else : ?>
							<option value="" selected><?php esc_html_e( 'Выберите интерес', 'family-comfort-calc' ); ?></option>
							<?php foreach ( $interest_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( (string) $value ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<button class="ai-family-comfort__button" type="button" <?php disabled( ! $has_data ); ?>>
					<?php esc_html_e( 'Подобрать направление', 'family-comfort-calc' ); ?>
				</button>
			</form>
		</div>

		<div class="ai-family-comfort__notch" aria-hidden="true"></div>

		<h3 class="ai-family-comfort__subtitle">
			<?php esc_html_e( 'Рекомендации по направлениям', 'family-comfort-calc' ); ?>
		</h3>

		<p class="ai-family-comfort__empty" id="fcc-family-comfort-empty" hidden>
			<?php esc_html_e( 'Для выбранных параметров пока нет направлений. Назначьте категории и направления в метабоксе Family Comfort на страницах.', 'family-comfort-calc' ); ?>
		</p>

		<div class="ai-family-comfort__slider" id="fcc-family-comfort-slider">
			<div class="ai-family-comfort__slider-shell">
				<button class="ai-family-comfort__slider-btn ai-family-comfort__slider-btn--prev" type="button" data-fc-prev aria-label="<?php esc_attr_e( 'Предыдущие направления', 'family-comfort-calc' ); ?>">
					<svg width="12" height="20" viewBox="0 0 12 20" fill="none" aria-hidden="true"><path d="M2 2L10 10L2 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<div class="ai-family-comfort__slider-viewport" dir="ltr">
					<div class="ai-family-comfort__slider-track" id="fcc-family-comfort-cards"></div>
				</div>
				<button class="ai-family-comfort__slider-btn ai-family-comfort__slider-btn--next" type="button" data-fc-next aria-label="<?php esc_attr_e( 'Следующие направления', 'family-comfort-calc' ); ?>">
					<svg width="12" height="20" viewBox="0 0 12 20" fill="none" aria-hidden="true"><path d="M10 2L2 10L10 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			</div>
		</div>

		<div class="ai-family-comfort__card-pool" id="fcc-family-comfort-card-pool" hidden>
			<?php foreach ( $cards as $card ) : ?>
				<?php
				$age_ids      = ! empty( $card['age_ids'] ) && is_array( $card['age_ids'] ) ? $card['age_ids'] : array();
				$interest_ids = ! empty( $card['interest_ids'] ) && is_array( $card['interest_ids'] ) ? $card['interest_ids'] : array();
				$rating       = isset( $card['rating'] ) ? (float) $card['rating'] : 4.5;
				$rating_label = number_format( $rating, 1, '.', '' );
				$rating_pct   = (int) round( ( $rating / 5 ) * 100 );
				$title        = ! empty( $card['title'] ) ? (string) $card['title'] : '';
				$url          = ! empty( $card['url'] ) ? (string) $card['url'] : '';
				$image        = ! empty( $card['image'] ) ? (string) $card['image'] : '';
				$tags         = ! empty( $card['tags'] ) && is_array( $card['tags'] ) ? $card['tags'] : array();
				?>
				<article
					class="ai-family-comfort__card<?php echo '' !== $url ? ' ai-family-comfort__card--linkable' : ''; ?>"
					hidden
					data-fcc-age="<?php echo esc_attr( implode( ',', array_map( 'strval', $age_ids ) ) ); ?>"
					data-fcc-interest="<?php echo esc_attr( implode( ',', array_map( 'strval', $interest_ids ) ) ); ?>"
					<?php if ( '' !== $url ) : ?>
						data-fcc-url="<?php echo esc_url( $url ); ?>"
					<?php endif; ?>
				>
					<?php if ( '' !== $url ) : ?>
						<a class="ai-family-comfort__card-link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $title ); ?>" tabindex="-1"></a>
					<?php endif; ?>

					<header class="ai-family-comfort__card-head">
						<h4 class="ai-family-comfort__card-title"><?php echo esc_html( $title ); ?></h4>
					</header>

					<?php
					$fcc_tags_visible = 6;
					$tag_count        = ! empty( $tags ) ? count( $tags ) : 0;
					$has_more         = $tag_count > $fcc_tags_visible;
					?>
					<div class="ai-family-comfort__tags ai-family-comfort__tags--slot<?php echo $has_more ? ' ai-family-comfort__tags--collapsible' : ''; ?>">
						<?php if ( ! empty( $tags ) ) : ?>
							<?php foreach ( $tags as $index => $tag ) : ?>
								<?php
								$extra_class = $index >= $fcc_tags_visible ? ' fcc-tag--extra' : '';
								$label       = ! empty( $tag['label'] ) ? (string) $tag['label'] : '';
								?>
								<?php if ( ! empty( $tag['url'] ) ) : ?>
									<a class="fcc-tag<?php echo esc_attr( $extra_class ); ?>" href="<?php echo esc_url( $tag['url'] ); ?>"><?php echo esc_html( $label ); ?></a>
								<?php else : ?>
									<span class="fcc-tag<?php echo esc_attr( $extra_class ); ?>"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if ( $has_more ) : ?>
							<button type="button" class="fcc-tags-more" data-fcc-tags-more>
								<?php esc_html_e( 'ещё', 'family-comfort-calc' ); ?>
							</button>
						<?php endif; ?>
					</div>

					<div class="ai-family-comfort__card-foot">
					<?php if ( '' !== $image ) : ?>
						<div class="ai-family-comfort__card-media">
							<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
						</div>
					<?php endif; ?>

					<div class="ai-family-comfort__rating" aria-label="<?php echo esc_attr( sprintf( __( 'Рейтинг %s из 5', 'family-comfort-calc' ), $rating_label ) ); ?>">
						<small>5.0 /</small>
						<span aria-hidden="true">★</span>
						<strong><?php echo esc_html( $rating_label ); ?></strong>
					</div>
					<div class="ai-family-comfort__bar" aria-hidden="true">
						<span style="width: <?php echo esc_attr( (string) max( 0, min( 100, $rating_pct ) ) ); ?>%;"></span>
					</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
