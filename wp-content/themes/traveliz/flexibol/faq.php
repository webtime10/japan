<?php
/**
 * ACF Flexible block: s_flexibol_faq
 *
 * Renders Questions & Answers (FAQ) from flexible content.
 */

if ( get_row_layout() !== 's_flexibol_faq' ) {
	return;
}

$faq_title = get_sub_field( 's_flexibol_faq_main_title' );
if ( empty( $faq_title ) ) {
	$faq_title = 'Questions and Answers';
}
?>

<section class="faq l">
	<div class="container-6">
		<?php if ( $faq_title ) : ?>
			<h2><?php echo esc_html( $faq_title ); ?></h2>
		<?php endif; ?>

		<div class="into-faq">
			<div class="faq-container" id="faq-wrapper">
				<?php if ( have_rows( 's_flexibol_faq_items' ) ) : ?>
					<?php while ( have_rows( 's_flexibol_faq_items' ) ) : the_row(); ?>
						<?php
						$question = get_sub_field( 's_flexibol_faq_question' );
						$answer   = get_sub_field( 's_flexibol_faq_answer' );
						?>
						<?php if ( empty( $question ) && empty( $answer ) ) continue; ?>

						<div class="faq-item">
							<div class="faq-question">
								<span class="title-text"><?php echo esc_html( $question ); ?></span>
								<div class="status-box"></div>
							</div>
							<div class="faq-answer">
								<div class="answer-inner">
									<?php echo wp_kses_post( $answer ); ?>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

