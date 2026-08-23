<?php
/**
 * Flexible Constructor: Opinion of the expert
 * Layout: s_flexibol_expert
 *
 * Only the main expert photo is editable in ACF; ellipse, quote marks, stat icon,
 * and bottom-card icons are fixed theme assets (same as page layout).
 */
if ( get_row_layout() !== 's_flexibol_expert' ) {
	return;
}

$section_title = get_sub_field( 's_flexibol_expert_section_title' );
$background_image = get_sub_field( 's_flexibol_expert_background_image' );
$shadow_image     = get_sub_field( 's_flexibol_expert_shadow_image' );
$photo         = get_sub_field( 's_flexibol_expert_photo' );
$name          = get_sub_field( 's_flexibol_expert_name' );
$role          = get_sub_field( 's_flexibol_expert_role' );
$stat_strong   = get_sub_field( 's_flexibol_expert_stat_strong' );
$stat_text     = get_sub_field( 's_flexibol_expert_stat_text' );
$quote         = get_sub_field( 's_flexibol_expert_quote' );
$body          = get_sub_field( 's_flexibol_expert_body' );

$item1_title = (string) get_sub_field( 's_flexibol_expert_item_1_title' );
$item1_text  = (string) get_sub_field( 's_flexibol_expert_item_1_text' );
$item2_title = (string) get_sub_field( 's_flexibol_expert_item_2_title' );
$item2_text  = (string) get_sub_field( 's_flexibol_expert_item_2_text' );
$item3_title = (string) get_sub_field( 's_flexibol_expert_item_3_title' );
$item3_text  = (string) get_sub_field( 's_flexibol_expert_item_3_text' );

if ( ! function_exists( 'traveliz_expert_image_url' ) ) {
	function traveliz_expert_image_url( $img ) {
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

$background_url = traveliz_expert_image_url( $background_image );
$shadow_url     = traveliz_expert_image_url( $shadow_image );

$photo_url = '';
$photo_alt = '';
if ( is_array( $photo ) && ! empty( $photo['url'] ) ) {
	$photo_url = (string) $photo['url'];
	$photo_alt = ! empty( $photo['alt'] ) ? (string) $photo['alt'] : (string) $name;
} elseif ( is_numeric( $photo ) ) {
	$photo_url = (string) wp_get_attachment_image_url( (int) $photo, 'full' );
	$photo_alt = (string) get_post_meta( (int) $photo, '_wp_attachment_image_alt', true );
	if ( $photo_alt === '' ) {
		$photo_alt = (string) $name;
	}
} elseif ( is_string( $photo ) && $photo !== '' ) {
	$photo_url = $photo;
}
if ( $photo_url === '' ) {
	$photo_url = get_template_directory_uri() . '/img/expert/expert.webp';
	$photo_alt = $name ? (string) $name : '';
}

$t_uri    = get_template_directory_uri();
$expert_u = $t_uri . '/img/expert/';
// Bottom cards: three fixed slots; icons from theme layout only.
$expert_bottom_icons = array(
	'chto_ponravilos.webp',
	'poleznii_sovet.webp',
	'itog.webp',
);

$has_bottom_cards = ( $item1_title !== '' || $item1_text !== '' || $item2_title !== '' || $item2_text !== '' || $item3_title !== '' || $item3_text !== '' );
?>

<section
	<?php if ( $background_url ) : ?>
		style="background-image: url('<?php echo esc_url( $background_url ); ?>'); background-repeat: no-repeat; background-position: center; background-size: cover;"
	<?php endif; ?>
	class="expert"
>
	<div class="container-4">

		<img class="ellipse372" src="<?php echo esc_url( $shadow_url ? $shadow_url : ( $t_uri . '/img/Ellipse372.webp' ) ); ?>" alt="">
		<div class="expert-into">
			<?php if ( ! empty( $section_title ) ) : ?>
				<h2 class="expert-title"><?php echo esc_html( $section_title ); ?></h2>
			<?php endif; ?>

			<div class="expert-card">
				<div class="expert-main">
					<div class="expert-left">
						<div class="expert-photo">
							<img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( $photo_alt ); ?>">
						</div>
						<div class="expert-person">
							<?php if ( ! empty( $name ) ) : ?>
								<h3 class="expert-name"><?php echo esc_html( $name ); ?></h3>
							<?php endif; ?>
							<?php if ( ! empty( $role ) ) : ?>
								<p class="expert-role"><?php echo esc_html( $role ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $stat_strong ) || ! empty( $stat_text ) ) : ?>
								<p class="expert-stat">
									<img src="<?php echo esc_url( $expert_u . 'image 1666.webp' ); ?>" alt="">
									<span class="marshrut-w">
										<?php if ( ! empty( $stat_strong ) ) : ?>
											<strong><?php echo esc_html( $stat_strong ); ?></strong>
										<?php endif; ?>
										<?php if ( ! empty( $stat_text ) ) : ?>
											<?php echo esc_html( $stat_text ); ?>
										<?php endif; ?>
									</span>
								</p>
							<?php endif; ?>
						</div>
					</div>
					<div class="div-expert">
						<div class="expert-quote-wrap">
							<div class="expert-quote-marks expert-quote-marks--left">
								<img src="<?php echo esc_url( $expert_u . 'kavichka2.webp' ); ?>" alt="">
							</div>
							<?php if ( ! empty( $quote ) ) : ?>
								<div class="expert-quote">
									<p><?php echo nl2br( esc_html( $quote ) ); ?></p>
								</div>
							<?php endif; ?>

							<div class="expert-quote-marks expert-quote-marks--right">
								<img src="<?php echo esc_url( $expert_u . 'kavichka1.webp' ); ?>" alt="">
							</div>
						</div>

						<?php if ( ! empty( $body ) ) : ?>
							<div class="expert-text">
								<p><?php echo nl2br( esc_html( $body ) ); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $has_bottom_cards ) : ?>
					<div class="expert-bottom-cards">

						<?php if ( $item1_title !== '' || $item1_text !== '' ) : ?>
							<div class="expert-bottom-card">
								<div class="expert-bottom-icon">
									<img src="<?php echo esc_url( $expert_u . $expert_bottom_icons[0] ); ?>" alt="<?php echo esc_attr( $item1_title ); ?>">
									<?php if ( $item1_title !== '' ) : ?>
										<h4><?php echo esc_html( $item1_title ); ?></h4>
									<?php endif; ?>
								</div>
								<?php if ( $item1_text !== '' ) : ?>
									<div class="expert-bottom-content">
										<p><?php echo nl2br( esc_html( $item1_text ) ); ?></p>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $item2_title !== '' || $item2_text !== '' ) : ?>
							<div class="expert-bottom-card">
								<div class="expert-bottom-icon">
									<img src="<?php echo esc_url( $expert_u . $expert_bottom_icons[1] ); ?>" alt="<?php echo esc_attr( $item2_title ); ?>">
									<?php if ( $item2_title !== '' ) : ?>
										<h4><?php echo esc_html( $item2_title ); ?></h4>
									<?php endif; ?>
								</div>
								<?php if ( $item2_text !== '' ) : ?>
									<div class="expert-bottom-content">
										<p><?php echo nl2br( esc_html( $item2_text ) ); ?></p>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $item3_title !== '' || $item3_text !== '' ) : ?>
							<div class="expert-bottom-card">
								<div class="expert-bottom-icon">
									<img src="<?php echo esc_url( $expert_u . $expert_bottom_icons[2] ); ?>" alt="<?php echo esc_attr( $item3_title ); ?>">
									<?php if ( $item3_title !== '' ) : ?>
										<h4><?php echo esc_html( $item3_title ); ?></h4>
									<?php endif; ?>
								</div>
								<?php if ( $item3_text !== '' ) : ?>
									<div class="expert-bottom-content">
										<p><?php echo nl2br( esc_html( $item3_text ) ); ?></p>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>
