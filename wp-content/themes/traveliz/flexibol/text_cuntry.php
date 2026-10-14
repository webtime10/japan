<?php
/**
 * Flexible: s_flexibol_country_text — текст о стране + фото.
 */
$layout = get_row_layout();
if ( $layout !== 's_flexibol_country_text' ) {
	return;
}

if ( ! function_exists( 'traveliz_country_text_image_url' ) ) {
	/**
	 * Resolve ACF image field (array / ID / URL string) to a usable URL.
	 *
	 * @param mixed $img ACF image value.
	 * @return string
	 */
	function traveliz_country_text_image_url( $img ) {
		if ( is_array( $img ) && ! empty( $img['url'] ) ) {
			return (string) $img['url'];
		}
		if ( is_numeric( $img ) ) {
			$url = wp_get_attachment_image_url( (int) $img, 'full' );
			return $url ? (string) $url : '';
		}
		if ( is_string( $img ) && '' !== trim( $img ) ) {
			return $img;
		}
		return '';
	}
}

$title   = get_sub_field( 's_flexibol_title' );
$text_1  = get_sub_field( 's_flexibol_text' );
$image_1 = get_sub_field( 's_flexibol_image' );
$text_2  = get_sub_field( 's_flexibol_text_2' );
$image_2 = get_sub_field( 's_flexibol_image_2' );

$image_1_url = traveliz_country_text_image_url( $image_1 );
$image_2_url = traveliz_country_text_image_url( $image_2 );

// Show block if there is title, text, or image (image alone used to be skipped when title empty).
if ( empty( $title ) && empty( $text_1 ) && empty( $text_2 ) && $image_1_url === '' ) {
	return;
}

if ( $image_1_url === '' ) {
	$image_1_url = get_template_directory_uri() . '/img/no.webp';
}
?>
<section class="short-text-and-video">
	<div class="container-4">
		<div class="into-text-and-video">
			<div class="into-text-and-1">
				<div class="photo-container-1">
					<img class="forma1" src="<?php echo esc_url( get_template_directory_uri() . '/img/forma1.png' ); ?>" alt="">
					<img class="floating-mountains" src="<?php echo esc_url( $image_1_url ); ?>" alt="">
				</div>
				<div class="text-end">
					<?php if ( ! empty( $title ) ) : ?>
						<h2><?php echo wp_kses_post( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( $text_1 ) : ?>
						<div class="short-text-1">
							<?php echo wp_kses_post( $text_1 ); ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $text_2 ) : ?>
					<div class="text-end-2">
						<?php echo wp_kses_post( $text_2 ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php
