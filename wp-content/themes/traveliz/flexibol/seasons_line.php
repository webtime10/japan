<?php
/**
 * ACF Flexible block: s_flexibol_seasons_line
 *
 * Renders the "slider-pogoda" (seasons line) from ACF.
 */

// We assume `page.php` includes this file only for the correct ACF layout.
// Avoid hard returning here so we can verify rendering even if layout name differs.
get_row_layout();

$section_title = get_sub_field( 's_flexibol_seasons_line_section_title' );
if ( empty( $section_title ) ) {
	$section_title = 'Линейка сезонов';
}

$pod_zag_pogoda = get_sub_field( 'pod_zag_pogoda' );

$background_image = get_sub_field( 's_flexibol_seasons_line_background_image' );
$shadow_image     = get_sub_field( 's_flexibol_seasons_line_shadow_image' );

/**
 * Helper: convert ACF image array to URL.
 *
 * @param mixed $img
 * @return string
 */
if ( ! function_exists( 'traveliz_image_url' ) ) {
	function traveliz_image_url( $img ) {
		if ( is_array( $img ) && ! empty( $img['url'] ) ) {
			return $img['url'];
		}
		return '';
	}
}

$months = array(
	'january',
	'february',
	'march',
	'april',
	'may',
	'june',
	'july',
	'august',
	'september',
	'october',
	'november',
	'december',
);

// Default active slide (0-based index) to match desired initial state.
// According to your observation: initially 3rd block should be active.
$default_active_index = 2;
$background_url       = traveliz_image_url( $background_image );
$shadow_url           = traveliz_image_url( $shadow_image );
$carousel_dir         = function_exists( 'traveliz_pll_is_rtl' ) && traveliz_pll_is_rtl() ? 'rtl' : 'ltr';
?>

<section
	<?php if ( $background_url ) : ?>
		style="background-image: url('<?php echo esc_url( $background_url ); ?>'); background-repeat: no-repeat; background-position: center; background-size: cover;"
	<?php endif; ?>
	class="slider-pogoda"
>
	<div class="container-4">
		<div class="slider-pogoda-dop">
				<img class="Ellipse523" src="<?php echo esc_url( $shadow_url ? $shadow_url : ( get_template_directory_uri() . '/img/Ellipse523.webp' ) ); ?>" alt="">
				<h2><?php echo wp_kses_post( $section_title ); ?></h2>
				<?php if ( ! empty( $pod_zag_pogoda ) ) : ?>
				<div class="pod-zag-pogoda"><?php echo wp_kses_post( $pod_zag_pogoda ); ?></div>
			    <?php endif; ?>
		</div>		
		<div class="into-slider-pogoda">
			
			

			<div class="now-carousel shadow">
				<div class="now-carousel-button-left">
					<a href="#">
						<img width="53" height="53" src="<?php echo esc_url( get_template_directory_uri() . '/img/arrow-l.webp' ); ?>" alt="">
					</a>
				</div>
				<div class="now-carousel-button-right">
					<a href="#">
						<img width="53" height="53" src="<?php echo esc_url( get_template_directory_uri() . '/img/arrow-r.webp' ); ?>" alt="">
					</a>
				</div>

				<div class="now-carousel-wrapper">
					<div class="now-carousel-items" dir="<?php echo esc_attr( $carousel_dir ); ?>">
						<?php foreach ( $months as $idx => $m ) : ?>
							<?php
							$month_title       = get_sub_field( 's_flexibol_season_' . $m . '_title' );
							$month_subtitle    = get_sub_field( 's_flexibol_season_' . $m . '_subtitle' );
							$month_short_text  = get_sub_field( 's_flexibol_season_' . $m . '_short_text' );
							$month_image       = get_sub_field( 's_flexibol_season_' . $m . '_image' );
							$month_weather_icon = get_sub_field( 's_flexibol_season_' . $m . '_weather_icon' );

							$month_image_url   = traveliz_image_url( $month_image );
							$weather_icon_url  = traveliz_image_url( $month_weather_icon );
							?>

							<div class="now-carousel-block<?php echo ( (int) $idx === (int) $default_active_index ) ? ' is-active' : ''; ?>">
								<div class="now-carousel-media">
									<img
										src="<?php echo esc_url( $month_image_url ? $month_image_url : ( get_template_directory_uri() . '/img/nofoto2.png' ) ); ?>"
										alt=""
									/>
								</div>
								<div class="now-carousel-caption">
								<h3 class="now-carousel-title">
									<?php if ( $weather_icon_url ) : ?>
										<img src="<?php echo esc_url( $weather_icon_url ); ?>" alt="">
									<?php endif; ?>
									<span><?php echo wp_kses_post( $month_title ); ?></span>
								</h3>

								<?php if ( ! empty( $month_subtitle ) ) : ?>
									<strong><?php echo wp_kses_post( traveliz_isolate_bidi_temp_subtitle( $month_subtitle ) ); ?></strong>
								<?php endif; ?>

								<?php if ( ! empty( $month_short_text ) ) : ?>
									<p class="now-carousel-text"><?php echo wp_kses_post( $month_short_text ); ?></p>
								<?php endif; ?>
							</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script src="<?php echo get_template_directory_uri(); ?>/js/slider_page.js"></script>
