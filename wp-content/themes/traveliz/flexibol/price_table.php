<?php
/**
 * Flexible Constructor: Price table
 * Layout: s_flexibol_price_table
 */
if ( get_row_layout() !== 's_flexibol_price_table' ) {
	return;
}

$section_title = get_sub_field( 's_flexibol_price_table_section_title' );
$background_image = get_sub_field( 's_flexibol_price_table_background_image' );
$shadow_image     = get_sub_field( 's_flexibol_price_table_shadow_image' );
$top_input     = get_sub_field( 's_flexibol_price_top_input' );
$top_image     = get_sub_field( 's_flexibol_price_top_image' );

if ( ! function_exists( 'traveliz_price_table_image_url' ) ) {
	function traveliz_price_table_image_url( $img ) {
		if ( is_array( $img ) && ! empty( $img['url'] ) ) {
			return (string) $img['url'];
		}
		if ( is_numeric( $img ) ) {
			return (string) wp_get_attachment_image_url( (int) $img, 'full' );
		}
		if ( is_string( $img ) ) {
			return $img;
		}
		return '';
	}
}

$background_url = traveliz_price_table_image_url( $background_image );
$shadow_url     = traveliz_price_table_image_url( $shadow_image );

$top_img_url = '';
$top_img_alt = '';
if ( is_array( $top_image ) && ! empty( $top_image['url'] ) ) {
	$top_img_url = (string) $top_image['url'];
	$top_img_alt = ! empty( $top_image['alt'] ) ? (string) $top_image['alt'] : '';} elseif ( is_numeric( $top_image ) ) {
	$top_img_url = (string) wp_get_attachment_image_url( (int) $top_image, 'full' );
	$top_img_alt = (string) get_post_meta( (int) $top_image, '_wp_attachment_image_alt', true );
} elseif ( is_string( $top_image ) && $top_image !== '' ) {
	$top_img_url = $top_image;
}
if ( $top_img_url === '' ) {
	$top_img_url = get_template_directory_uri() . '/img/table/progivanie.webp';
}

$bb1 = get_sub_field( 's_flexibol_price_bottom_block_1' );
$bb2 = get_sub_field( 's_flexibol_price_bottom_block_2' );
$bb3 = get_sub_field( 's_flexibol_price_bottom_block_3' );
$bb1 = is_array( $bb1 ) ? $bb1 : array();
$bb2 = is_array( $bb2 ) ? $bb2 : array();
$bb3 = is_array( $bb3 ) ? $bb3 : array();

$bb1_top = isset( $bb1['s_flexibol_price_bb1_top'] ) ? (string) $bb1['s_flexibol_price_bb1_top'] : '';$bb1_mid = isset( $bb1['s_flexibol_price_bb1_middle'] ) ? (string) $bb1['s_flexibol_price_bb1_middle'] : '';$bb1_ex  = isset( $bb1['s_flexibol_price_bb1_extra'] ) ? (string) $bb1['s_flexibol_price_bb1_extra'] : '';$bb1_pr  = isset( $bb1['s_flexibol_price_bb1_button_price'] ) ? (string) $bb1['s_flexibol_price_bb1_button_price'] : '';$bb1_day = isset( $bb1['s_flexibol_price_bb1_button_day'] ) ? (string) $bb1['s_flexibol_price_bb1_button_day'] : '';
$bb2_top = isset( $bb2['s_flexibol_price_bb2_top'] ) ? (string) $bb2['s_flexibol_price_bb2_top'] : '';$bb2_mid = isset( $bb2['s_flexibol_price_bb2_middle'] ) ? (string) $bb2['s_flexibol_price_bb2_middle'] : '';$bb2_ex  = isset( $bb2['s_flexibol_price_bb2_extra'] ) ? (string) $bb2['s_flexibol_price_bb2_extra'] : '';$bb2_pr  = isset( $bb2['s_flexibol_price_bb2_button_price'] ) ? (string) $bb2['s_flexibol_price_bb2_button_price'] : '';$bb2_day = isset( $bb2['s_flexibol_price_bb2_button_day'] ) ? (string) $bb2['s_flexibol_price_bb2_button_day'] : '';
$bb3_top = isset( $bb3['s_flexibol_price_bb3_top'] ) ? (string) $bb3['s_flexibol_price_bb3_top'] : '';$bb3_mid = isset( $bb3['s_flexibol_price_bb3_middle'] ) ? (string) $bb3['s_flexibol_price_bb3_middle'] : '';$bb3_ex  = isset( $bb3['s_flexibol_price_bb3_extra'] ) ? (string) $bb3['s_flexibol_price_bb3_extra'] : '';$bb3_pr  = isset( $bb3['s_flexibol_price_bb3_button_price'] ) ? (string) $bb3['s_flexibol_price_bb3_button_price'] : '';$bb3_day = isset( $bb3['s_flexibol_price_bb3_button_day'] ) ? (string) $bb3['s_flexibol_price_bb3_button_day'] : '';
$uri              = get_template_directory_uri() . '/img/table/';
$price_cards_dir  = function_exists( 'traveliz_pll_is_rtl' ) && traveliz_pll_is_rtl() ? 'rtl' : 'ltr';
?>

<section
	<?php if ( $background_url ) : ?>
		style="background-image: url('<?php echo esc_url( $background_url ); ?>'); background-repeat: no-repeat; background-position: center; background-size: cover;"
	<?php endif; ?>
	class="table-price"
>
	<div class="container-4">
		<img class="elips371" src="<?php echo esc_url( $shadow_url ? $shadow_url : ( get_template_directory_uri() . '/img/Ellipse371.webp' ) ); ?>" alt="">
		<div class="table-into">

			<?php if ( ! empty( $section_title ) ) : ?>
				<h2 class="price-table-title"><?php echo wp_kses_post( $section_title ); ?></h2>
			<?php endif; ?>

			<div class="price-table-wrapper">

				<div class="price-card-main">
					<div class="price-card-main-header">
						<div class="price-card-main-header-left">
							<img src="<?php echo esc_url( $top_img_url ); ?>" alt="<?php echo esc_attr( $top_img_alt ?: wp_strip_all_tags( $top_input ) ); ?>">
							<?php if ( ! empty( $top_input ) ) : ?>
								<span><?php echo wp_kses_post( $top_input ); ?></span>
							<?php endif; ?>
						</div>
					</div>

					<div class="price-card-main-rows">
						<?php
						if ( have_rows( 's_flexibol_price_items' ) ) :
							while ( have_rows( 's_flexibol_price_items' ) ) :
								the_row();
								$img1   = get_sub_field( 's_flexibol_price_image_1' );
								$title  = get_sub_field( 's_flexibol_price_title' );
								$inp1   = get_sub_field( 's_flexibol_price_input' );
								$inp2   = get_sub_field( 's_flexibol_price_input_2' );
								$price  = get_sub_field( 's_flexibol_price_item_price' );
								$night  = get_sub_field( 's_flexibol_price_item_night' );

								$icon_url = '';
								$icon_alt = '';
								if ( is_array( $img1 ) && ! empty( $img1['url'] ) ) {
									$icon_url = (string) $img1['url'];
									$icon_alt = ! empty( $img1['alt'] ) ? (string) $img1['alt'] : (string) $title;
								} elseif ( is_numeric( $img1 ) ) {
									$icon_url = (string) wp_get_attachment_image_url( (int) $img1, 'full' );
									$icon_alt = (string) get_post_meta( (int) $img1, '_wp_attachment_image_alt', true );
								} elseif ( is_string( $img1 ) && $img1 !== '' ) {
									$icon_url = $img1;
								}
								if ( $icon_url === '' ) {
									$icon_url = get_template_directory_uri() . '/img/table/budzetniii_otel.webp';
								}

								$img2      = get_sub_field( 's_flexibol_price_image_2' );
								$star_url  = '';
								$star_alt  = '';
								if ( is_array( $img2 ) && ! empty( $img2['url'] ) ) {
									$star_url = (string) $img2['url'];
									$star_alt = ! empty( $img2['alt'] ) ? (string) $img2['alt'] : '';								} elseif ( is_numeric( $img2 ) ) {
									$star_url = (string) wp_get_attachment_image_url( (int) $img2, 'full' );
									$star_alt = (string) get_post_meta( (int) $img2, '_wp_attachment_image_alt', true );
								} elseif ( is_string( $img2 ) && $img2 !== '' ) {
									$star_url = $img2;
								}
								?>
								<div class="price-row">
									<div class="price-row-left">
										<?php if ( $icon_url !== '' ) : ?>
											<div class="price-row-icon">
												<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $icon_alt ) ); ?>">
											</div>
										<?php endif; ?>
										<div class="price-row-text">
											<?php if ( ! empty( $title ) ) : ?>
												<div class="price-row-text-title"><img class="price-row-star active-mobile" src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $icon_alt ) ); ?>" width="16" height="16" loading="lazy" decoding="async"> <span><?php echo wp_kses_post( $title ); ?></span></div>
											<?php endif; ?>
											<?php if ( ! empty( $inp1 ) || ! empty( $inp2 ) || $star_url !== '' ) : ?>
												<div class="price-row-text-subtitle">
													<?php if ( ! empty( $inp1 ) ) : ?>
														<?php echo wp_kses_post( $inp1 ); ?>
													<?php endif; ?>
													<?php if ( $star_url !== '' ) : ?>
														<img class="price-row-star" src="<?php echo esc_url( $star_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $star_alt ) ); ?>" width="16" height="16" loading="lazy" decoding="async">
													<?php endif; ?>
													<?php if ( ! empty( $inp2 ) ) : ?>
														<?php echo wp_kses_post( $inp2 ); ?>
													<?php endif; ?>
												</div>
											<?php endif; ?>
										
										</div>
									</div>
									<div class="price-row-right">
										<?php if ( ! empty( $price ) || ! empty( $night ) ) : ?>
											<?php if ( ! empty( $price ) ) : ?>
												<?php echo wp_kses_post( $price ); ?>
											<?php endif; ?>
											<?php if ( ! empty( $night ) ) : ?>
												<span class="price-period">/ <?php echo wp_kses_post( $night ); ?></span>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</div>
								<?php
							endwhile;
						endif;
						?>
					</div>
				</div>

				<div class="price-cards-bottom" dir="<?php echo esc_attr( $price_cards_dir ); ?>">

					<!-- Транспорт -->
					<div class="price-card-small">
						<div class="price-card-small-header price-card-small-header--transport">
							<img src="<?php echo esc_url( $uri . 'transport.webp' ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $bb1_top ) ); ?>">
							<?php if ( ! empty( $bb1_top ) ) : ?>
								<span><?php echo wp_kses_post( $bb1_top ); ?></span>
							<?php endif; ?>
						</div>
						<div class="price-card-small-body">
							<div>
								<?php if ( ! empty( $bb1_mid ) ) : ?>
									<div class="price-card-desc-title">
										<img src="<?php echo esc_url( $uri . 'arenda_avto.webp' ); ?>" alt="">
										<span><?php echo wp_kses_post( $bb1_mid ); ?></span>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $bb1_ex ) ) : ?>
									<div class="price-card-desc-subtitle"><?php echo wp_kses_post( $bb1_ex ); ?></div>
								<?php endif; ?>
							</div>
							<?php if ( ! empty( $bb1_pr ) || ! empty( $bb1_day ) ) : ?>
								<div class="price-card-small-price">
									<?php if ( ! empty( $bb1_pr ) ) : ?>
										<?php echo wp_kses_post( $bb1_pr ); ?>
									<?php endif; ?>
									<?php if ( ! empty( $bb1_day ) ) : ?>
										<span class="price-period"><?php echo wp_kses_post( $bb1_day ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<!-- Питание -->
					<div class="price-card-small">
						<div class="price-card-small-header price-card-small-header--food">
							<img src="<?php echo esc_url( $uri . 'pitanie.webp' ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $bb2_top ) ); ?>">
							<?php if ( ! empty( $bb2_top ) ) : ?>
								<span><?php echo wp_kses_post( $bb2_top ); ?></span>
							<?php endif; ?>
						</div>
						<div class="price-card-small-body">
							<div>
								<?php if ( ! empty( $bb2_mid ) ) : ?>
									<div class="price-card-desc-title"><span><?php echo wp_kses_post( $bb2_mid ); ?></span></div>
								<?php endif; ?>
								<?php if ( ! empty( $bb2_ex ) ) : ?>
									<div class="price-card-desc-subtitle"><?php echo wp_kses_post( $bb2_ex ); ?></div>
								<?php endif; ?>
							</div>
							<?php if ( ! empty( $bb2_pr ) || ! empty( $bb2_day ) ) : ?>
								<div class="price-card-small-price">
									<?php if ( ! empty( $bb2_pr ) ) : ?>
										<?php echo wp_kses_post( $bb2_pr ); ?>
									<?php endif; ?>
									<?php if ( ! empty( $bb2_day ) ) : ?>
										<span class="price-period"><?php echo wp_kses_post( $bb2_day ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<!-- Дополнительно -->
					<div class="price-card-small">
						<div class="price-card-small-header price-card-small-header--extra">
							<img src="<?php echo esc_url( $uri . 'dopolnitelno.webp' ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $bb3_top ) ); ?>">
							<?php if ( ! empty( $bb3_top ) ) : ?>
								<span><?php echo wp_kses_post( $bb3_top ); ?></span>
							<?php endif; ?>
						</div>
						<div class="price-card-small-body">
							<div>
								<?php if ( ! empty( $bb3_mid ) ) : ?>
									<div class="price-card-desc-title">
										<img src="<?php echo esc_url( $uri . 'znak_parkovki.webp' ); ?>" alt="">
										<span><?php echo wp_kses_post( $bb3_mid ); ?></span>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $bb3_ex ) ) : ?>
									<div class="price-card-desc-subtitle"><?php echo wp_kses_post( $bb3_ex ); ?></div>
								<?php endif; ?>
							</div>
							<?php if ( ! empty( $bb3_pr ) || ! empty( $bb3_day ) ) : ?>
								<div class="price-card-small-price">
									<?php if ( ! empty( $bb3_pr ) ) : ?>
										<?php echo wp_kses_post( $bb3_pr ); ?>
									<?php endif; ?>
									<?php if ( ! empty( $bb3_day ) ) : ?>
										<span class="price-period"><?php echo wp_kses_post( $bb3_day ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>

				</div>

			</div>

		</div>
	</div>
</section>
