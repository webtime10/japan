<?php
/**
 * Flexible Constructor: Expert advice
 * Layout: s_flexibol_advice
 */
if ( get_row_layout() !== 's_flexibol_advice' ) {
	return;
}

$section_title = get_sub_field( 's_flexibol_advice_section_title' );
?>

<section class="advice">
	<div class="container-4">
		<div class="into-advice">
			<?php if ( ! empty( $section_title ) ) : ?>
				<h2 class="advice-title"><?php echo esc_html( $section_title ); ?></h2>
			<?php endif; ?>
			<div class="advice-grid">
				<?php
				if ( have_rows( 's_flexibol_advice_items' ) ) :
					while ( have_rows( 's_flexibol_advice_items' ) ) :
						the_row();
						$item_img   = get_sub_field( 's_flexibol_advice_item_image' );
						$item_title = get_sub_field( 's_flexibol_advice_item_title' );
						$item_text  = get_sub_field( 's_flexibol_advice_item_text' );

						$img_url = '';
						$img_alt = '';
						if ( is_array( $item_img ) && ! empty( $item_img['url'] ) ) {
							$img_url = (string) $item_img['url'];
							$img_alt = ! empty( $item_img['alt'] ) ? (string) $item_img['alt'] : '';
						} elseif ( is_numeric( $item_img ) ) {
							$img_url = (string) wp_get_attachment_image_url( (int) $item_img, 'full' );
							$img_alt = (string) get_post_meta( (int) $item_img, '_wp_attachment_image_alt', true );
						} elseif ( is_string( $item_img ) && $item_img !== '' ) {
							$img_url = $item_img;
						}
						if ( $img_url === '' ) {
							$img_url = get_template_directory_uri() . '/img/kpmpas2.webp';
						}
						?>
						<div class="advice-item">
							<div class="advice-image">
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
							</div>
							<div class="advice-content">
								<?php if ( ! empty( $item_title ) ) : ?>
									<h3><?php echo esc_html( $item_title ); ?></h3>
								<?php endif; ?>
								<?php if ( ! empty( $item_text ) ) : ?>
									<p><?php echo nl2br( esc_html( $item_text ) ); ?></p>
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
</section>
