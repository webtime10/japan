<?php
/**
 * ACF Flexible block: s_flexibol_regions_comparison
 *
 * Renders "Regions comparison" section with repeater columns.
 */

if ( get_row_layout() !== 's_flexibol_regions_comparison' ) {
	return;
}

if ( ! function_exists( 'traveliz_image_url' ) ) {
	function traveliz_image_url( $img ) {
		if ( is_array( $img ) && ! empty( $img['url'] ) ) {
			return $img['url'];
		}
		return '';
	}
}

$section_title = get_sub_field( 's_flexibol_regions_comparison_section_title' );
if ( empty( $section_title ) ) {
	$section_title = 'Regions comparison';
}

$dop_text = get_sub_field( 'comparison_of_regions_dop_text' );

$left_title    = get_sub_field( 's_flexibol_regions_left_title' );
$left_subtitle = get_sub_field( 's_flexibol_regions_left_subtitle' );

$label_weather       = get_sub_field( 's_flexibol_regions_label_weather' );
$label_entertainment = get_sub_field( 's_flexibol_regions_label_entertainment' );
$label_transport     = get_sub_field( 's_flexibol_regions_label_transport' );
$label_kids          = get_sub_field( 's_flexibol_regions_label_kids' );
$label_price         = get_sub_field( 's_flexibol_regions_label_price' );

if ( empty( $left_title ) ) {
	$left_title = 'Choose a region for travel';
}
if ( empty( $left_subtitle ) ) {
	$left_subtitle = 'Compare the best destinations';
}

if ( empty( $label_weather ) ) {
	$label_weather = 'Weather';
}
if ( empty( $label_entertainment ) ) {
	$label_entertainment = 'Entertainment';
}
if ( empty( $label_transport ) ) {
	$label_transport = 'Transport';
}
if ( empty( $label_kids ) ) {
	$label_kids = 'Kids entertainment';
}
if ( empty( $label_price ) ) {
	$label_price = 'Price';
}
?>

<section class='сomparison_of_regions'>
	<div class='container-4'>
		<h2><?php echo wp_kses_post( $section_title ); ?></h2>
		<?php if ( ! empty( $dop_text ) ) : ?>
			<div class="comparison_of_regions_dop_text"><?php echo wp_kses_post( $dop_text ); ?></div>
		<?php endif; ?>

		<div class="cats-wrap_r">
			<div class="cats_r">
				<div class="into-cats_r">
					<div class="cat-item_r">
						<div class="vibor-wrap1 one-v">
							<div class="vibor-reg vibor-reg2">
								<div class="vibor-f">
									<h3><?php echo wp_kses_post( $left_title ); ?></h3>
									<p><?php echo wp_kses_post( $left_subtitle ); ?></p>
								</div>

								<img class="dio" src="<?php echo esc_url( get_template_directory_uri() . '/img/Rectangle-272.png' ); ?>" alt="">
							</div>

							<div class="wrap-pogoda wrap-grid">
								<div class="pogoda"><img src="<?php echo esc_url( get_template_directory_uri() . '/img/image830.webp' ); ?>" alt=""><span><?php echo wp_kses_post( $label_weather ); ?></span></div>
								<div class="razvlechenie"><img src="<?php echo esc_url( get_template_directory_uri() . '/img/image357.webp' ); ?>" alt=""><span><?php echo wp_kses_post( $label_entertainment ); ?></span></div>
								<div class="transpert"><img src="<?php echo esc_url( get_template_directory_uri() . '/img/image1519.webp' ); ?>" alt=""><span><?php echo wp_kses_post( $label_transport ); ?></span></div>
								<div class="detsk-razvl"><img src="<?php echo esc_url( get_template_directory_uri() . '/img/image1292.webp' ); ?>" alt=""><span><?php echo wp_kses_post( $label_kids ); ?></span></div>
								<div class="cena"><img src="<?php echo esc_url( get_template_directory_uri() . '/img/image1025.webp' ); ?>" alt=""><span><?php echo wp_kses_post( $label_price ); ?></span></div>
							</div>
						</div>
					</div>

					<?php if ( have_rows( 's_flexibol_regions_items' ) ) : ?>
						<?php while ( have_rows( 's_flexibol_regions_items' ) ) : the_row(); ?>
							<?php
							$city_name        = get_sub_field( 's_flexibol_region_city_name' );
							$region_image     = get_sub_field( 's_flexibol_region_image' );
							$weather_value    = get_sub_field( 's_flexibol_region_weather' );
							$entertainment    = get_sub_field( 's_flexibol_region_entertainment' );
							$transport        = get_sub_field( 's_flexibol_region_transport' );
							$kids             = get_sub_field( 's_flexibol_region_kids' );
							$price            = get_sub_field( 's_flexibol_region_price' );

							$region_image_url = traveliz_image_url( $region_image );
							if ( $region_image_url === '' ) {
								$region_image_url = get_template_directory_uri() . '/img/nofoto2.png';
							}
							?>

							<?php if ( empty( $city_name ) && empty( $region_image_url ) ) continue; ?>

							<div class="cat-item_r">
								<div class="vibor-wrap">
									<div class="vibor-reg">
										<div class="reg-img-v">
										    <img src="<?php echo esc_url( $region_image_url ); ?>" alt="">
										</div>
										<h3><?php echo wp_kses_post( $city_name ); ?></h3>
									</div>

								<div class="wrap-pogoda wrap-flex">
									<div class="pogoda">
										<span><?php echo wp_kses_post( $weather_value ); ?></span>
									</div>
									<div class="razvlechenie">
										<span><?php echo wp_kses_post( $entertainment ); ?></span>
									</div>
									<div class="transpert">
										<span><?php echo wp_kses_post( $transport ); ?></span>
									</div>
									<div class="detsk-razvl">
										<span><?php echo wp_kses_post( $kids ); ?></span>
									</div>
									<div class="cena">
										<span><?php echo wp_kses_post( $price ); ?></span>
									</div>
								</div>
								</div>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
</section>

