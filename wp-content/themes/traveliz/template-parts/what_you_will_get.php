<?php
	// Аргументы из get_template_part( ..., null, array( 'omit_buttons' => true ) ) попадают как переменные.
	$wywg_omit_buttons = isset( $omit_buttons ) && $omit_buttons;
    $title_what_you_will_get = get_field('title_what_you_will_get', 'option');

if($title_what_you_will_get):?>
<section class="what-will-happen-section l">
	<div class="container-4">
		<div class="into-what-will-happen">
			<h2><?php echo $title_what_you_will_get; ?></h2>
			
			<div class="what-will-happen-blocks">
				<?php if ( function_exists( 'have_rows' ) ) : ?>
					<?php if ( have_rows( 'item_what_you_will_get', 'option' ) ) : ?>
						<?php while ( have_rows( 'item_what_you_will_get', 'option' ) ) : the_row(); 
							$item_img = get_sub_field( 'item_what_you_will_img' );
							$item_title = get_sub_field( 'item_what_you_will_title' );
							$item_text = get_sub_field( 'item_what_you_will_text' );
							
							// Получаем URL картинки
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
									<p><?php echo wp_kses_post( $item_text ); ?></p>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>
					<?php elseif ( have_rows( 'item_what_you_will_get' ) ) : ?>
						<?php while ( have_rows( 'item_what_you_will_get' ) ) : the_row(); 
							$item_img = get_sub_field( 'item_what_you_will_img' );
							$item_title = get_sub_field( 'item_what_you_will_title' );
							$item_text = get_sub_field( 'item_what_you_will_text' );
							
							// Получаем URL картинки
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
										<img width="255" height="255" src="<?php echo esc_url( $item_img_url ); ?>" alt="<?php echo $item_title ? esc_attr( wp_strip_all_tags( $item_title ) ) : ''; ?>" />
									</div>
								<?php endif; ?>
								
								<?php if ( $item_title ) : ?>
									<h3><?php echo wp_kses_post( $item_title ); ?></h3>
								<?php endif; ?>
								
								<?php if ( $item_text ) : ?>
									<p><?php echo wp_kses_post( $item_text ); ?></p>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				<?php endif; ?>
<?php
$realnye_otzyvy = get_field('realnye_otzyvy', 'option');
$personal_route = get_field('personal_route', 'option');
$constant_support = get_field('constant_support', 'option');
$prise = get_field('prise', 'option');
$best_value = get_field('best_value', 'option');
$best_value_for_money = get_field('best_value_for_money', 'option');
?>


                <div class="what-will-happen-cta">
				<div class="what-will-happen-price">
					
                       <div class="wrap-phone-webp">
                            <span><?php echo $best_value; ?></span>
                            <img width="136" height="40" class="phon1" src="<?php echo get_template_directory_uri(); ?>/img/Rectangle74.webp" alt="" />
                            <img width="19" height="19" class="star-cta" src="<?php echo get_template_directory_uri(); ?>/img/image1408.webp" alt="" />
                       </div>
					
				</div>
                <div class="ef-0">
                    <div>
						<span class="t-price"><?php echo $best_value_for_money; ?></span>
						<span class="p-price"><?php echo $prise; ?></span>
					</div>
                </div>
				
				<?php if ( ! $wywg_omit_buttons ) : ?>
				<?php get_template_part( 'template-parts/what_you_will_get_buttons' ); ?>
				<?php endif; ?>

		
				<div class="what-will-happen-benefits">
					<div class="benefit-item">
						
<svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_143_630)">
<path d="M17.4173 8.77172V9.50006C17.4163 11.2072 16.8635 12.8683 15.8414 14.2357C14.8192 15.603 13.3824 16.6033 11.7453 17.0873C10.1082 17.5714 8.35848 17.5132 6.75711 16.9216C5.15573 16.33 3.7885 15.2366 2.85933 13.8044C1.93015 12.3722 1.48882 10.6781 1.60115 8.97464C1.71347 7.27117 2.37344 5.64965 3.48262 4.3519C4.59181 3.05416 6.09077 2.14973 7.75597 1.7735C9.42116 1.39728 11.1634 1.56941 12.7227 2.26422" stroke="#48C95F" stroke-width="1.58333" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M17.4167 3.16675L9.5 11.0913L7.125 8.71633" stroke="#48C95F" stroke-width="1.58333" stroke-linecap="round" stroke-linejoin="round"/>
</g>
<defs>
<clipPath id="clip0_143_630">
<rect width="19" height="19" fill="white"/>
</clipPath>
</defs>
</svg>
						<span><?php echo $realnye_otzyvy; ?></span>
					</div>
					<div class="benefit-item">
						
<svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_143_630)">
<path d="M17.4173 8.77172V9.50006C17.4163 11.2072 16.8635 12.8683 15.8414 14.2357C14.8192 15.603 13.3824 16.6033 11.7453 17.0873C10.1082 17.5714 8.35848 17.5132 6.75711 16.9216C5.15573 16.33 3.7885 15.2366 2.85933 13.8044C1.93015 12.3722 1.48882 10.6781 1.60115 8.97464C1.71347 7.27117 2.37344 5.64965 3.48262 4.3519C4.59181 3.05416 6.09077 2.14973 7.75597 1.7735C9.42116 1.39728 11.1634 1.56941 12.7227 2.26422" stroke="#48C95F" stroke-width="1.58333" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M17.4167 3.16675L9.5 11.0913L7.125 8.71633" stroke="#48C95F" stroke-width="1.58333" stroke-linecap="round" stroke-linejoin="round"/>
</g>
<defs>
<clipPath id="clip0_143_630">
<rect width="19" height="19" fill="white"/>
</clipPath>
</defs>
</svg>
						<span><?php echo $personal_route; ?></span>
					</div>
					<div class="benefit-item">
						
<svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_143_630)">
<path d="M17.4173 8.77172V9.50006C17.4163 11.2072 16.8635 12.8683 15.8414 14.2357C14.8192 15.603 13.3824 16.6033 11.7453 17.0873C10.1082 17.5714 8.35848 17.5132 6.75711 16.9216C5.15573 16.33 3.7885 15.2366 2.85933 13.8044C1.93015 12.3722 1.48882 10.6781 1.60115 8.97464C1.71347 7.27117 2.37344 5.64965 3.48262 4.3519C4.59181 3.05416 6.09077 2.14973 7.75597 1.7735C9.42116 1.39728 11.1634 1.56941 12.7227 2.26422" stroke="#48C95F" stroke-width="1.58333" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M17.4167 3.16675L9.5 11.0913L7.125 8.71633" stroke="#48C95F" stroke-width="1.58333" stroke-linecap="round" stroke-linejoin="round"/>
</g>
<defs>
<clipPath id="clip0_143_630">
<rect width="19" height="19" fill="white"/>
</clipPath>
</defs>
</svg>
						<span><?php echo $constant_support; ?></span>
					</div>
				</div>
			</div>   

			</div>
			
			
		</div>
	</div>
</section>
<?php endif; ?>