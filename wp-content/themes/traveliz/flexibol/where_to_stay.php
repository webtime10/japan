<?php
/**
 * Flexible Constructor: Where to stay (google-otel)
 * Layout: s_flexibol_where_to_stay
 */
if ( get_row_layout() !== 's_flexibol_where_to_stay' ) {
	return;
}

$title     = get_sub_field( 's_flexibol_where_stay_section_title' );
$lead_text = get_sub_field( 's_flexibol_where_stay_lead_text' );
// Backward compatibility: older "Subtitle" field name
if ( ( $lead_text === '' || $lead_text === null ) && function_exists( 'get_sub_field' ) ) {
	$legacy = get_sub_field( 's_flexibol_where_stay_subtitle' );
	if ( $legacy !== '' && $legacy !== null ) {
		$lead_text = $legacy;
	}
}
?>

<?php
$otel_cards_dir = function_exists( 'traveliz_pll_is_rtl' ) && traveliz_pll_is_rtl() ? 'rtl' : 'ltr';
?>

<section class="google-otel">
	<div class="container-4">
		<div class="google-otel-into">
			<?php if ( ! empty( $title ) ) : ?>
				<h2 class="google-otel-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $lead_text ) ) : ?>
				<p class="google-otel-subtitle"><?php echo nl2br( esc_html( $lead_text ) ); ?></p>
			<?php endif; ?>

			<?php if ( have_rows( 's_flexibol_where_stay_cards' ) ) : ?>
				<div class="google-otel-cards" dir="<?php echo esc_attr( $otel_cards_dir ); ?>">
					<?php
					while ( have_rows( 's_flexibol_where_stay_cards' ) ) :
						the_row();
						$c_title = get_sub_field( 's_flexibol_where_stay_card_title' );
						$c_text  = get_sub_field( 's_flexibol_where_stay_card_text' );
						if ( ( $c_title === '' || $c_title === null ) && ( $c_text === '' || $c_text === null ) ) {
							continue;
						}
						?>
						<div class="google-otel-card">
							<?php if ( ! empty( $c_title ) ) : ?>
								<h3 class="google-otel-card-title"><?php echo wp_kses( traveliz_hotel_card_title_html( $c_title ), traveliz_hotel_card_title_allowed_html() ); ?></h3>
							<?php endif; ?>
							<?php if ( ! empty( $c_text ) ) : ?>
								<p class="google-otel-card-text"><?php echo nl2br( esc_html( $c_text ) ); ?></p>
							<?php endif; ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
