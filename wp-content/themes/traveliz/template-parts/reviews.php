<?php
/**
 * Блок отзывов (шорткод [short_reviews]).
 *
 * ACF: на одной группе (часто Options):
 * - Повторитель reviews_slider — только слайды (картинки и т.д. внутри него);
 * - Рядом, НЕ внутри повторителя: reviews_title, reviews_button, reviews_link (текст).
 *
 * Дополнительно страница «Tourist Reviews»: tourist_reviews_subtitle и при отсутствии
 * значений на странице «Reviews» — заголовок/кнопка/ссылка (запасной вариант).
 */

$tourist_reviews_title    = get_field( 'tourist_reviews_title', 'option' );
$tourist_reviews_subtitle = get_field( 'tourist_reviews_subtitle', 'option' );
$tourist_reviews_button   = get_field( 'tourist_reviews_button', 'option' );
$tourist_reviews_link     = get_field( 'tourist_reviews_link', 'option' );

// Поля соседи с reviews_slider (не sub_fields повторителя)
$acf_reviews_title  = get_field( 'reviews_title', 'option' );
$acf_reviews_button = get_field( 'reviews_button', 'option' );
$acf_reviews_link   = get_field( 'reviews_link', 'option' );

// Сначала блок «Reviews» (слайдер), иначе правки там не видны при заполненном «Tourist Reviews».
$block_title    = $acf_reviews_title ? $acf_reviews_title : $tourist_reviews_title;
$block_subtitle = $tourist_reviews_subtitle;
$button_text    = $acf_reviews_button ? $acf_reviews_button : $tourist_reviews_button;
$button_link    = $acf_reviews_link ? $acf_reviews_link : $tourist_reviews_link;

if ( ! $button_link && function_exists( 'get_pages' ) ) {
	$reviews_pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-reviews.php',
			'number'     => 1,
		)
	);
	if ( ! empty( $reviews_pages[0] ) ) {
		$button_link = get_permalink( $reviews_pages[0]->ID );
	}
}

$slider_rows_opt = function_exists( 'get_field' ) ? get_field( 'reviews_slider', 'option' ) : null;
$has_slider_opt  = is_array( $slider_rows_opt ) && count( $slider_rows_opt ) > 0;
$reviews_background       = function_exists( 'get_field' ) ? get_field( 'reviews_background_image', 'option' ) : null;
$reviews_background_url   = '';

if ( is_array( $reviews_background ) && ! empty( $reviews_background['url'] ) ) {
	$reviews_background_url = $reviews_background['url'];
} elseif ( is_numeric( $reviews_background ) ) {
	$reviews_background_url = wp_get_attachment_image_url( (int) $reviews_background, 'full' );
} elseif ( is_string( $reviews_background ) && '' !== trim( $reviews_background ) ) {
	$reviews_background_url = $reviews_background;
}

if ( ! $reviews_background_url ) {
	$reviews_background_id  = (int) get_option( 'wt_reviews_custom_background_image_id', 0 );
	$reviews_background_url = $reviews_background_id ? wp_get_attachment_image_url( $reviews_background_id, 'full' ) : '';
}

$reviews_background_style = $reviews_background_url ? 'background-image: url(' . esc_url( $reviews_background_url ) . '); background-size: cover; background-repeat: no-repeat; background-position: center center;' : '';

if ( ! $block_title && ! $block_subtitle && ! $has_slider_opt && ! $button_text ) {
	$slider_rows_post = function_exists( 'get_field' ) ? get_field( 'reviews_slider' ) : null;
	$has_slider_post  = is_array( $slider_rows_post ) && count( $slider_rows_post ) > 0;
	if ( ! $has_slider_post ) {
		return;
	}
}
?>

<section style="<?php echo esc_attr( $reviews_background_style ); ?>" class="reviews-section custom-rew rrrr">
	<div class="container-4">
		<?php if ( $block_title ) : ?>
			<h2><?php echo esc_html( $block_title ); ?></h2>
		<?php endif; ?>
		<?php if ( $block_subtitle ) : ?>
			<p class="reviews-section-subtitle"><?php echo esc_html( $block_subtitle ); ?></p>
		<?php endif; ?>

		<div class="reviews-container-into caruael_t">
			<div class="carousel_m shadow_m">
				<div class="carousel-wrapper_m">
					<div class="carousel-items_m caruael_tt">
						<?php
						$img_alt = $block_title ? $block_title : '';

						$render_slide = function () use ( $img_alt ) {
							$img = get_sub_field( 'img_reviews' );
							if ( ! $img ) {
								$img = get_sub_field( 'image' );
							}
							$img_url = '';
							if ( $img ) {
								if ( is_array( $img ) && ! empty( $img['url'] ) ) {
									$img_url = $img['url'];
								} elseif ( is_numeric( $img ) ) {
									$img_url = wp_get_attachment_image_url( (int) $img, 'large' );
								} elseif ( is_string( $img ) ) {
									$img_url = $img;
								}
							}

							// Подполя внутри повторителя (имена не reviews_title / reviews_button / reviews_link)
							$slide_title = get_sub_field( 'title_reviews' );
							if ( ! $slide_title ) {
								$slide_title = get_sub_field( 'title' );
							}
							if ( ! $slide_title ) {
								$slide_title = get_sub_field( 'heading' );
							}
							if ( ! $slide_title ) {
								$slide_title = get_sub_field( 'name' );
							}

							$slide_text = get_sub_field( 'content' );
							if ( ! $slide_text ) {
								$slide_text = get_sub_field( 'text' );
							}
							if ( ! $slide_text ) {
								$slide_text = get_sub_field( 'caption' );
							}

							if ( ! $img_url && ! $slide_title && ! $slide_text ) {
								return;
							}

							$alt = $slide_title ? $slide_title : $img_alt;
							?>
							<div class="carousel-block_m">
								<div>
									<?php if ( $img_url ) : ?>
										<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
									<?php endif; ?>
									<?php if ( $slide_title ) : ?>
										<div class="reviews-slide-title"><?php echo esc_html( $slide_title ); ?></div>
									<?php endif; ?>
									<?php if ( $slide_text ) : ?>
										<div class="reviews-slide-text"><?php echo nl2br( esc_html( $slide_text ) ); ?></div>
									<?php endif; ?>
								</div>
							</div>
							<?php
						};

						if ( function_exists( 'have_rows' ) ) :
							if ( have_rows( 'reviews_slider', 'option' ) ) :
								while ( have_rows( 'reviews_slider', 'option' ) ) :
									the_row();
									$render_slide();
								endwhile;
							elseif ( have_rows( 'reviews_slider' ) ) :
								while ( have_rows( 'reviews_slider' ) ) :
									the_row();
									$render_slide();
								endwhile;
							endif;
						endif;
						?>
					</div>
				</div>
				<div class="wrap-dots-wra">
					<div class="carousel-button-left_m"><a href="#"><img width="53" height="53" src="<?php echo esc_url( get_template_directory_uri() . '/img/arrow-l.webp' ); ?>" alt=""></a></div>
					<div class="carousel-button-right_m"><a href="#"><img width="53" height="53" src="<?php echo esc_url( get_template_directory_uri() . '/img/arrow-l.webp' ); ?>" alt=""></a></div>
				</div>
			</div>

			<?php if ( $button_text && $button_link ) : ?>
				<a class="more-all-re" href="<?php echo esc_url( $button_link ); ?>"><?php echo esc_html( $button_text ); ?></a>
			<?php elseif ( $button_text ) : ?>
				<span class="more-all-re"><?php echo esc_html( $button_text ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</section>
