<?php
/**
 * Flexible Constructor: Multi-day route
 * Layout: s_flexibol_route_one_day
 */
if ( get_row_layout() !== 's_flexibol_route_one_day' ) {
	return;
}

$section_title = get_sub_field( 's_flexibol_route_section_title' );
$rout_dop_text = get_sub_field( 'rout_dop_text' );
?>

<section class="rout-new">
	<div class="container-4">
		<div class="itinerary-container">
			<?php if ( ! empty( $section_title ) ) : ?>
				<h2 class="main-title"><?php echo esc_html( $section_title ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $rout_dop_text ) ) : ?>
				<div class="rout_dop_text"><?php echo nl2br( esc_html( $rout_dop_text ) ); ?></div>
			<?php endif; ?>

			<?php if ( have_rows( 's_flexibol_route_days' ) ) : ?>
				<?php
				while ( have_rows( 's_flexibol_route_days' ) ) :
					the_row();
					$day_badge    = get_sub_field( 's_flexibol_route_day_badge' );
					$day_subtitle = get_sub_field( 's_flexibol_route_day_subtitle' );
					?>
				<div class="grid-layout route-one-day-grid">
					<div class="timeline-column">
						<?php if ( ! empty( $day_badge ) || ! empty( $day_subtitle ) ) : ?>
						<div class="day-header">
							<?php if ( ! empty( $day_badge ) ) : ?>
								<span class="day-badge y"><?php echo esc_html( $day_badge ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $day_subtitle ) ) : ?>
								<span class="day-title"><?php echo esc_html( $day_subtitle ); ?></span>
							<?php endif; ?>
						</div>
						<?php endif; ?>

						<div class="timeline-list">
							<?php if ( have_rows( 's_flexibol_route_day_timeline' ) ) : ?>
								<?php
								while ( have_rows( 's_flexibol_route_day_timeline' ) ) :
									the_row();
									$item_time = get_sub_field( 's_flexibol_route_time' );
									$item_text = get_sub_field( 's_flexibol_route_text' );
									?>
								<div class="timeline-item">
									<?php if ( ! empty( $item_time ) ) : ?>
										<h4 class="event-time-title"><?php echo wp_kses_post( $item_time ); ?></h4>
									<?php endif; ?>
									<?php if ( ! empty( $item_text ) ) : ?>
										<p class="event-description"><?php echo nl2br( esc_html( $item_text ) ); ?></p>
									<?php endif; ?>
								</div>
								<?php endwhile; ?>
							<?php endif; ?>
						</div>
					</div>

					<div class="images-column">
						<?php if ( have_rows( 's_flexibol_route_day_photos' ) ) : ?>
							<?php
							$photo_index = 0;
							while ( have_rows( 's_flexibol_route_day_photos' ) ) :
								the_row();
								$photo_index++;
								$photo = get_sub_field( 's_flexibol_route_photo' );
								if ( is_array( $photo ) ) {
									$photo_url = isset( $photo['url'] ) ? (string) $photo['url'] : '';
									$photo_alt = isset( $photo['alt'] ) ? (string) $photo['alt'] : '';
								} else {
									$photo_url = is_string( $photo ) ? $photo : '';
									$photo_alt = '';
								}
								if ( empty( $photo_url ) && $photo_index === 1 ) {
									$photo_url = get_template_directory_uri() . '/img/nofoto2.png';
								}
								if ( empty( $photo_url ) ) {
									continue;
								}
								?>
								<img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( $photo_alt ); ?>">
							<?php endwhile; ?>
						<?php endif; ?>
					</div>
				</div>
				<?php endwhile; ?>
			<?php endif; ?>

		</div>
	</div>
</section>
