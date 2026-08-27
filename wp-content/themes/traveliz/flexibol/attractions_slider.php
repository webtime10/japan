<?php
/**
 * Flexible Constructor: Attractions slider
 * Layout: s_flexibol_attractions_slider
 */
if ( get_row_layout() !== 's_flexibol_attractions_slider' ) {
	return;
}

$title = get_sub_field( 's_flexibol_attractions_title' );
$dop_text_landmark = get_sub_field( 'dop_text_landmark' );
$background_image = get_sub_field( 's_flexibol_attractions_background_image' );
$shadow_image     = get_sub_field( 's_flexibol_attractions_shadow_image' );

if ( ! function_exists( 'traveliz_attractions_image_url' ) ) {
	function traveliz_attractions_image_url( $img ) {
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

$background_url = traveliz_attractions_image_url( $background_image );
$shadow_url     = traveliz_attractions_image_url( $shadow_image );
?>

<section
	<?php if ( $background_url ) : ?>
		style="background-image: url('<?php echo esc_url( $background_url ); ?>');"
	<?php endif; ?>
	class="landmark"
>
	<div class="container-4">
		<?php if ( ! empty( $title ) ) : ?>
			<h2><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $dop_text_landmark ) ) : ?>
			<div class="dop_text_landmark"><?php echo nl2br( esc_html( $dop_text_landmark ) ); ?></div>
		<?php endif; ?>

		<div class="landpark-into">
		<img src="<?php echo esc_url( $shadow_url ? $shadow_url : ( get_template_directory_uri() . '/img/blue2.webp' ) ); ?>" alt="" class="elips099" />
			<div class="carousel_m shadow_m">
				<div class="carousel-wrapper_m">
					<div class="carousel-items_m caruael_tt">
						<?php if ( have_rows( 's_flexibol_attractions_items' ) ) : ?>
							<?php while ( have_rows( 's_flexibol_attractions_items' ) ) : the_row();
								$a_title = get_sub_field( 's_flexibol_attractions_card_title' );
								$a_img = get_sub_field( 's_flexibol_attractions_image' );
								$a_text = get_sub_field( 's_flexibol_attractions_text' );
								$a_btn_text = get_sub_field( 's_flexibol_attractions_button_text' );
								$a_btn_link = get_sub_field( 's_flexibol_attractions_button_link' );

								$a_img_url = '';
								$a_img_alt = '';
								if ( is_array( $a_img ) ) {
									$a_img_url = isset( $a_img['url'] ) ? (string) $a_img['url'] : '';
									$a_img_alt = isset( $a_img['alt'] ) ? (string) $a_img['alt'] : '';
								} elseif ( is_numeric( $a_img ) ) {
									$a_img_url = (string) wp_get_attachment_image_url( (int) $a_img, 'full' );
								} elseif ( is_string( $a_img ) ) {
									$a_img_url = $a_img;
								}

								if ( ! $a_img_url ) {
									$a_img_url = get_template_directory_uri() . '/img/no.webp';
								}
							?>
								<div class="carousel-block_m">
									<div>
										<a class="attractions-r" href="<?php echo esc_url( $a_btn_link ?: '#' ); ?>">
											<img class="forma11" src="<?php echo esc_url( get_template_directory_uri() . '/img/forma1.webp' ); ?>" alt="">
											<img class="attractions-img" src="<?php echo esc_url( $a_img_url ); ?>" alt="<?php echo esc_attr( $a_img_alt ); ?>" />
										</a>

										<div>
											<?php if ( ! empty( $a_title ) ) : ?>
												<h3><?php echo esc_html( $a_title ); ?></h3>
											<?php endif; ?>

											<?php if ( ! empty( $a_text ) ) : ?>
												<div class="attractions-card-text"><?php
													$a_text_out = (string) $a_text;
													if ( false === strpos( $a_text_out, '<' ) ) {
														$a_text_out = wpautop( $a_text_out );
													}
													echo wp_kses_post( $a_text_out );
												?></div>
											<?php endif; ?>

											<?php if ( ! empty( $a_btn_text ) ) : ?>
												<a href="<?php echo esc_url( $a_btn_link ?: '#' ); ?>">
													<?php echo esc_html( $a_btn_text ); ?>
												</a>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endwhile; ?>
						<?php endif; ?>
					</div>
				</div>

				<div class="wrap-dots-wra">
					<div class="carousel-button-left_m">
						<a href="#"><img width="53" height="53" src="<?php echo esc_url( get_template_directory_uri() . '/img/arrow-l.webp' ); ?>" alt=""></a>
					</div>
					<div class="carousel-button-right_m">
						<a href="#"><img width="53" height="53" src="<?php echo esc_url( get_template_directory_uri() . '/img/arrow-r.webp' ); ?>" alt=""></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

