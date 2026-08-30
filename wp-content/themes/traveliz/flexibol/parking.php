<?php
/**
 * Flexible Constructor: Parking
 * Layout: s_flexibol_parking
 *
 * Ellipse background and parking pin icon come from the theme (layout).
 */
if ( get_row_layout() !== 's_flexibol_parking' ) {
	return;
}

$title       = get_sub_field( 's_flexibol_parking_section_title' );
$background_image = get_sub_field( 's_flexibol_parking_background_image' );
$shadow_image     = get_sub_field( 's_flexibol_parking_shadow_image' );
$subtitle    = get_sub_field( 's_flexibol_parking_subtitle' );
$footer_text = get_sub_field( 's_flexibol_parking_footer_text' );
$t_uri       = get_template_directory_uri();

if ( ! function_exists( 'traveliz_parking_image_url' ) ) {
	function traveliz_parking_image_url( $img ) {
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

$background_url = traveliz_parking_image_url( $background_image );
$shadow_url     = traveliz_parking_image_url( $shadow_image );

$parking_map_btn_svg = '<svg width="11" height="14" viewBox="0 0 11 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 0C4.04184 0.00172024 2.64389 0.581735 1.61282 1.61281C0.581739 2.64389 0.00172369 4.04184 3.45209e-06 5.5C-0.00133722 6.69155 0.387857 7.8507 1.108 8.8C1.108 8.8 1.258 8.9975 1.2825 9.026L5.5 14L9.7195 9.0235C9.7415 8.997 9.892 8.8 9.892 8.8L9.8925 8.7985C10.6122 7.84954 11.0012 6.69098 11 5.5C10.9983 4.04184 10.4183 2.64389 9.38719 1.61281C8.35611 0.581735 6.95817 0.00172024 5.5 0ZM5.5 7.5C5.10444 7.5 4.71776 7.3827 4.38886 7.16294C4.05996 6.94318 3.80362 6.63082 3.65224 6.26537C3.50087 5.89991 3.46126 5.49778 3.53843 5.10982C3.6156 4.72186 3.80608 4.36549 4.08579 4.08579C4.36549 3.80608 4.72186 3.6156 5.10982 3.53843C5.49778 3.46126 5.89992 3.50087 6.26537 3.65224C6.63082 3.80362 6.94318 4.05996 7.16294 4.38886C7.38271 4.71776 7.5 5.10444 7.5 5.5C7.49934 6.03023 7.28842 6.53855 6.91349 6.91348C6.53856 7.28841 6.03023 7.49934 5.5 7.5Z" fill="#626E79"/></svg>';
?>

<section
	<?php if ( $background_url ) : ?>
		style="background-image: url('<?php echo esc_url( $background_url ); ?>'); background-repeat: no-repeat; background-position: center; background-size: cover;"
	<?php endif; ?>
	class="parking"
>
	<div class="container-4">
		<img class="ellipse374" src="<?php echo esc_url( $shadow_url ? $shadow_url : ( $t_uri . '/img/Ellipse374.webp' ) ); ?>" alt="">
		<div class="parking-into">
			<?php if ( ! empty( $title ) ) : ?>
				<h2 class="parking-title"><?php echo wp_kses_post( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $subtitle ) ) : ?>
				<p class="parking-subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
			<?php endif; ?>

			<?php if ( have_rows( 's_flexibol_parking_cards' ) ) : ?>
				<div class="parking-cards">
					<?php
					while ( have_rows( 's_flexibol_parking_cards' ) ) :
						the_row();
						$c_title  = get_sub_field( 's_flexibol_parking_card_title' );
						$c_text   = get_sub_field( 's_flexibol_parking_card_text' );
						$map_link = get_sub_field( 's_flexibol_parking_card_map_link' );
						$btn_lbl  = get_sub_field( 's_flexibol_parking_card_button_label' );
						if ( $btn_lbl === '' || $btn_lbl === null ) {
							$btn_lbl = 'На карте';
						}
						?>
						<div class="parking-card">
							<div class="parking-card-icon">
								<img src="<?php echo esc_url( $t_uri . '/img/parkovка.webp' ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $c_title ) ); ?>">
							</div>
							<?php if ( ! empty( $c_title ) ) : ?>
								<h3 class="parking-card-title"><?php echo wp_kses_post( $c_title ); ?></h3>
							<?php endif; ?>
							<?php if ( ! empty( $c_text ) ) : ?>
								<p class="parking-card-text"><?php echo wp_kses_post( $c_text ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $map_link ) ) : ?>
								<a class="parking-card-button" href="<?php echo esc_url( $map_link ); ?>" target="_blank" rel="noopener noreferrer">
									<span><?php echo wp_kses_post( $btn_lbl ); ?></span>
									<?php echo $parking_map_btn_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG markup. ?>
								</a>
							<?php else : ?>
								<button type="button" class="parking-card-button">
									<span><?php echo wp_kses_post( $btn_lbl ); ?></span>
									<?php echo $parking_map_btn_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG markup. ?>
								</button>
							<?php endif; ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $footer_text ) ) : ?>
				<div class="holiday">
					<p><?php echo wp_kses_post( $footer_text ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
