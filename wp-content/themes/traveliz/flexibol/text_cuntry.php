
<?php
// Country text block for the CURRENT flexible-content row.
// Output is controlled by outer loop in page.php.
$layout = get_row_layout();
if ( $layout === 's_flexibol_country_text' ) :
	$title = get_sub_field( 's_flexibol_title' );
	if ( ! empty( $title ) ) :
		$text_1 = get_sub_field( 's_flexibol_text' );
		$image_1 = get_sub_field( 's_flexibol_image' );
		$text_2  = get_sub_field( 's_flexibol_text_2' );
		$image_2 = get_sub_field( 's_flexibol_image_2' );

		$image_1_url = ( is_array( $image_1 ) && ! empty( $image_1['url'] ) ) ? $image_1['url'] : '';
		$image_2_url = ( is_array( $image_2 ) && ! empty( $image_2['url'] ) ) ? $image_2['url'] : '';
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
	endif; // ! empty($title)
endif; // layout check
?>