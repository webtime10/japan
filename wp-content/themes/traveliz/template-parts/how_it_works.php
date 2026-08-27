<?php
$hiw_title = get_field( 'title_how_it_works', 'option' );

$hiw_item_1 = get_field( 'item_1', 'option' );
$hiw_item_2 = get_field( 'item_2', 'option' );
$hiw_item_3 = get_field( 'item_3', 'option' );
$hiw_item_4 = get_field( 'item_4', 'option' );
$hiw_item_5 = get_field( 'item_5', 'option' );
$hiw_item_6 = get_field( 'item_6', 'option' );

$title_form   = get_field( 'title_form_home', 'option' );
$hiw_bg_array = get_field( 'background_image_how_it_works', 'option' );
$bg_bg        = ( is_array( $hiw_bg_array ) && ! empty( $hiw_bg_array['url'] ) ) ? $hiw_bg_array['url'] : '';

// Одна форма на сайт (без he/en/ar).
$cf7_form_id = 'f2e5507';
?>

<section<?php echo $bg_bg ? ' style="background-image: url(' . esc_url( $bg_bg ) . ')"' : ''; ?> class="work">
	<div class="container-8">
		<div class="into-work">
			<?php if ( $hiw_title ) : ?>
				<h2><?php echo esc_html( (string) $hiw_title ); ?></h2>
			<?php endif; ?>
			<div class="wrap-work">
				<div class="left-work">
					<div class="wrap-img-work">
						<p><?php echo esc_html( (string) $hiw_item_1 ); ?></p>
						<img src="<?php echo esc_url( get_template_directory_uri() . '/img/a1.png' ); ?>" alt="" />
					</div>
					<img class="st1" src="<?php echo esc_url( get_template_directory_uri() . '/img/Vector11.png' ); ?>" alt="" />
					<div class="wrap-img-work">
						<p><?php echo esc_html( (string) $hiw_item_2 ); ?></p>
						<img src="<?php echo esc_url( get_template_directory_uri() . '/img/a2.png' ); ?>" alt="" />
					</div>
					<img class="st2" src="<?php echo esc_url( get_template_directory_uri() . '/img/Vector22.png' ); ?>" alt="" />
					<div class="wrap-img-work">
						<p><?php echo esc_html( (string) $hiw_item_3 ); ?></p>
						<img src="<?php echo esc_url( get_template_directory_uri() . '/img/a3.png' ); ?>" alt="" />
					</div>
				</div>

				<div class="center-work">
					<div class="form-wrapper">
						<?php if ( $title_form ) : ?>
							<h3><?php echo esc_html( (string) $title_form ); ?></h3>
						<?php endif; ?>
						<?php
						if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
							wpcf7_enqueue_scripts();
							wpcf7_enqueue_styles();
						}

						$form = function_exists( 'wpcf7_get_contact_form_by_hash' )
							? wpcf7_get_contact_form_by_hash( $cf7_form_id )
							: null;

						if ( $form ) {
							// Напрямую, без вложенного do_shortcode.
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $form->form_html();
						} else {
							echo do_shortcode( '[contact-form-7 id="' . esc_attr( $cf7_form_id ) . '"]' );
						}
						?>
					</div>
				</div>

				<div class="right-work">
					<div class="wrap-img-work-right">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/img/a4.png' ); ?>" alt="" />
						<p><?php echo esc_html( (string) $hiw_item_4 ); ?></p>
					</div>
					<img class="st3" src="<?php echo esc_url( get_template_directory_uri() . '/img/Vector33.png' ); ?>" alt="" />
					<div class="wrap-img-work-right">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/img/a5.png' ); ?>" alt="" />
						<p><?php echo esc_html( (string) $hiw_item_5 ); ?></p>
					</div>
					<img class="st4" src="<?php echo esc_url( get_template_directory_uri() . '/img/Vector44.png' ); ?>" alt="" />
					<div class="wrap-img-work-right st44">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/img/a6.png' ); ?>" alt="" />
						<p><?php echo esc_html( (string) $hiw_item_6 ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
