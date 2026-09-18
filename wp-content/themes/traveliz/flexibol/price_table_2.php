<?php
/**
 * Flexible Constructor: Price table 2 (items only, no images, no bottom cards)
 * Layout: s_flexibol_price_table_2
 */
if ( get_row_layout() !== 's_flexibol_price_table_2' ) {
	return;
}

$section_title    = get_sub_field( 's_flexibol_price_table_2_section_title' );
$background_image = get_sub_field( 's_flexibol_price_table_2_background_image' );
$shadow_image     = get_sub_field( 's_flexibol_price_table_2_shadow_image' );
$top_input        = get_sub_field( 's_flexibol_price_table_2_top_input' );

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
?>

<section
	<?php if ( $background_url ) : ?>
		style="background-image: url('<?php echo esc_url( $background_url ); ?>'); background-repeat: no-repeat; background-position: center; background-size: cover;"
	<?php endif; ?>
	class="table-price table-price--v2"
>
	<div class="container-4">
		<img class="elips371" src="<?php echo esc_url( $shadow_url ? $shadow_url : ( get_template_directory_uri() . '/img/Ellipse371.webp' ) ); ?>" alt="">
		<div class="table-into">

			<?php if ( ! empty( $section_title ) ) : ?>
				<h2 class="price-table-title"><?php echo wp_kses_post( $section_title ); ?></h2>
			<?php endif; ?>

			<div class="price-table-wrapper">

				<div class="price-card-main">
					<?php if ( ! empty( $top_input ) ) : ?>
						<div class="price-card-main-header">
							<div class="price-card-main-header-left">
								<span><?php echo wp_kses_post( $top_input ); ?></span>
							</div>
						</div>
					<?php endif; ?>

					<div class="price-card-main-rows">
						<?php
						if ( have_rows( 's_flexibol_price_table_2_items' ) ) :
							while ( have_rows( 's_flexibol_price_table_2_items' ) ) :
								the_row();
								$title   = get_sub_field( 's_flexibol_price_table_2_title' );
								$details = get_sub_field( 's_flexibol_price_table_2_details' );
								$price   = get_sub_field( 's_flexibol_price_table_2_item_price' );
								$night   = get_sub_field( 's_flexibol_price_table_2_item_night' );
								?>
								<div class="price-row">
									<div class="price-row-left">
										<div class="price-row-text">
											<?php if ( ! empty( $title ) ) : ?>
												<div class="price-row-text-title"><span><?php echo wp_kses_post( $title ); ?></span></div>
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
									<div class="price-row-details">
										<?php if ( ! empty( $details ) ) : ?>
											<?php echo nl2br( wp_kses_post( $details ) ); ?>
										<?php endif; ?>
									</div>
								</div>
								<?php
							endwhile;
						endif;
						?>
					</div>
				</div>

			</div>

		</div>
	</div>
</section>
