<?php
/**
 * Block: What we offer («Что мы предлагаем»)
 * Same layout as what_you_will_get cards (title + circular image grid).
 */
if ( ! function_exists( 'get_field' ) ) {
	return;
}

$title = get_field( 'title_what_we_offer', 'option' );
if ( ! $title ) {
	return;
}
?>
<section class="what-will-happen-section what-we-offer-section l">
	<div class="container-4">
		<div class="into-what-will-happen">
			<h2><?php echo wp_kses_post( $title ); ?></h2>

			<div class="what-will-happen-blocks">
				<?php if ( have_rows( 'item_what_we_offer', 'option' ) ) : ?>
					<?php
					while ( have_rows( 'item_what_we_offer', 'option' ) ) :
						the_row();
						$item_img   = get_sub_field( 'item_what_we_offer_img' );
						$item_title = get_sub_field( 'item_what_we_offer_title' );
						$item_text  = get_sub_field( 'item_what_we_offer_text' );

						$item_img_url = '';
						if ( $item_img ) {
							if ( is_array( $item_img ) && ! empty( $item_img['url'] ) ) {
								$item_img_url = $item_img['url'];
							} elseif ( is_string( $item_img ) ) {
								$item_img_url = $item_img;
							}
						}
						?>
						<div class="will-item">
							<?php if ( $item_img_url ) : ?>
								<div>
									<img width="245" height="245" src="<?php echo esc_url( $item_img_url ); ?>" alt="<?php echo $item_title ? esc_attr( wp_strip_all_tags( $item_title ) ) : ''; ?>" />
								</div>
							<?php endif; ?>

							<?php if ( $item_title ) : ?>
								<h3><?php echo wp_kses_post( $item_title ); ?></h3>
							<?php endif; ?>

							<?php if ( $item_text ) : ?>
								<div class="will-item-text"><?php
									$text_out = (string) $item_text;
									if ( false === strpos( $text_out, '<' ) ) {
										$text_out = wpautop( $text_out );
									}
									echo wp_kses_post( $text_out );
								?></div>
							<?php endif; ?>
						</div>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
