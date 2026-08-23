<?php
/**
 * Flexible Constructor: Active leisure (активный отдых)
 * Layout: s_flexibol_active_otd
 */
if ( get_row_layout() !== 's_flexibol_active_otd' ) {
	return;
}

$section_title = get_sub_field( 's_flexibol_active_otd_section_title' );
$bottom_text   = get_sub_field( 's_flexibol_active_otd_bottom_text' );
$t_uri         = get_template_directory_uri();
?>

<section class="active-otd">
	<div class="container-4">
		<div class="active-otd-into">
			<?php if ( ! empty( $section_title ) ) : ?>
				<h2 class="active-otd-title"><?php echo esc_html( $section_title ); ?></h2>
			<?php endif; ?>
		    <?php if ( ! empty( $bottom_text ) ) : ?>
				<div class="active-otd-banner">
					<div class="active-otd-banner-content">
						
						<p><?php echo nl2br( esc_html( $bottom_text ) ); ?></p>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( have_rows( 's_flexibol_active_otd_items' ) ) : ?>
				<div class="active-otd-grid">
					<?php
					$n = 0;
					while ( have_rows( 's_flexibol_active_otd_items' ) ) :
						the_row();
						$n++;
						$c_img   = get_sub_field( 's_flexibol_active_otd_item_image' );
						$c_title = get_sub_field( 's_flexibol_active_otd_item_title' );
						$c_text  = get_sub_field( 's_flexibol_active_otd_item_text' );

						$img_url = '';
						$img_alt = (string) $c_title;
						if ( is_array( $c_img ) && ! empty( $c_img['url'] ) ) {
							$img_url = (string) $c_img['url'];
							if ( ! empty( $c_img['alt'] ) ) {
								$img_alt = (string) $c_img['alt'];
							}
						} elseif ( is_numeric( $c_img ) ) {
							$img_url = (string) wp_get_attachment_image_url( (int) $c_img, 'full' );
							$alt_meta = (string) get_post_meta( (int) $c_img, '_wp_attachment_image_alt', true );
							if ( $alt_meta !== '' ) {
								$img_alt = $alt_meta;
							}
						} elseif ( is_string( $c_img ) && $c_img !== '' ) {
							$img_url = $c_img;
						}
						if ( $img_url === '' ) {
							$img_url = $t_uri . '/img/nofoto2.png';
						}
						?>
						<div class="active-otd-card">
							<div class="active-otd-image">
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
							</div>
							<div class="active-otd-content">
								<?php if ( ! empty( $c_title ) ) : ?>
									<h3 class="active-otd-card-title"><?php echo esc_html( $c_title ); ?></h3>
								<?php endif; ?>
								<?php if ( ! empty( $c_text ) ) : ?>
									<p class="active-otd-card-text"><?php echo nl2br( esc_html( $c_text ) ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

	
		</div>
	</div>
</section>
