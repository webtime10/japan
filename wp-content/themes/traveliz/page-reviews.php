<?php
/* Template Name: reviews */

get_header();
?>

<?php
// Reviews page fields
$title_reviews       = get_field( 'title_reviews' );
$text_reviews        = get_field( 'text_reviews' );
$rout_text_reviews   = get_field( 'rout_text_reviews' );
// Google reviews (google reviews2 block)
$google_maps         = get_field( 'google_maps' );
$rewiew_platforma    = get_field( 'rewiew_platforma' );
$button_google       = get_field( 'button_google' );
$button_google_link  = get_field( 'button_google_link' );

$yellow_button_r2 = get_field( 'yellow_button_r2' );
$yellow_button_switcher_r2 = '';
$yellow_button_text_r2 = '';
$yellow_button_link_r2 = '';

if ( $yellow_button_r2 ) {
    $yellow_button_switcher_r2 = $yellow_button_r2['yellow_button_switcher_r2'] ?? '';
    $yellow_button_text_r2 = $yellow_button_r2['yellow_button_text_r2'] ?? '';
    $yellow_button_link_r2 = $yellow_button_r2['yellow_button_link_r2'] ?? '#';
}

// Video reviews fields (gimp-1 block)
$title_video_reviews = get_field( 'title_video_reviews' );
$text_video_reviews = get_field( 'text_videoreviews' );
$yellow_button_text_video = get_field( 'yellow_button_video' );
$yllow_button_link_r2 = get_field( 'yllow_button_link_r2' );

?>
<section class="reviews">

    <div class="container-3">
        <div class="reviews-into">
            <div class="into-r">
                
            <img class="ellips10" src="<?php echo get_template_directory_uri(); ?>/img/Ellipse110.webp" alt="">
            <?php if ( $title_reviews ) : ?>
            <h1><?php echo wp_kses_post( $title_reviews ); ?></h1>
            <?php endif; ?>
            <?php if ( $text_reviews ) : ?>
            <p><?php echo wp_kses_post( $text_reviews ); ?></p>
            <?php endif; ?>
            <div class="wrap-order-rew">
            <?php if ( $yellow_button_text_r2 ) : ?>
                <?php if ( $yellow_button_switcher_r2 !== 'link' ) : ?>
                    <button type="button" data-source="general_request" class="order-mr js-open-popup modal-trigger_wt">
                        <span><?php echo wp_kses_post( $yellow_button_text_r2 ); ?></span>
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"></path>
                        </svg>
                    </button>
                <?php else : ?>
                    <a class="order-mr" href="<?php echo esc_url( $yellow_button_link_r2 ); ?>">
                        <span><?php echo wp_kses_post( $yellow_button_text_r2 ); ?></span>
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            </div>
            <?php if ( $rout_text_reviews ) : ?>
                
            <div class="img-text-rew">
                <img src="<?php echo get_template_directory_uri(); ?>/img/image1865.webp" alt="">
                <p><?php echo wp_kses_post( $rout_text_reviews ); ?></p>
            </div>
            <?php endif; ?>
        </div>
        </div>
    </div>
</section>
</div>


            <section class="reviews-section reviews2 reviewsn">
                <div class="container-3 gimp-1">
					<?php if ( $title_video_reviews ) : ?>
						<h2><?php echo wp_kses_post( $title_video_reviews ); ?></h2>
					<?php endif; ?>
					<?php if ( $text_video_reviews ) : ?>
						<div class="real"><?php echo wp_kses_post( $text_video_reviews ); ?></div>
					<?php endif; ?>

					<div class="video-reviews-block" id="page-reviews-video-slider">
						<div class="reviews-container-into caruael_t reviews2_caruael_t">
							<div class="carousel_m shadow_m">
								<div class="carousel-wrapper_m">
									<div class="carousel-items_m caruael_tt">
										<?php if ( have_rows( 'slider_reviews_video' ) ) : ?>
											<?php
											while ( have_rows( 'slider_reviews_video' ) ) :
												the_row();
												$img_reviews_video   = get_sub_field( 'img_reviews_video' );
												$video_link_rew      = get_sub_field( 'video_link_rew' );
												$video_link_short    = get_sub_field( 'video_link_short' );
												$title_reviews_video = get_sub_field( 'title_reviews_video' );
												$text_reviews_video  = get_sub_field( 'text_reviews_video' );
												$img_url             = '';
												if ( $img_reviews_video ) {
													if ( is_array( $img_reviews_video ) && ! empty( $img_reviews_video['url'] ) ) {
														$img_url = $img_reviews_video['url'];
													} elseif ( is_string( $img_reviews_video ) ) {
														$img_url = $img_reviews_video;
													}
												}

												$video_embed_src   = '';
												$video_modal_type  = '';
												$video_resolved = traveliz_resolve_video_embed( $video_link_short, $video_link_rew );
												$video_embed_src  = $video_resolved['src'];
												$video_modal_type = $video_resolved['modal'];
												?>
												<div class="carousel-block_m">
													<div>
														<?php if ( $video_embed_src && $video_modal_type ) : ?>
															<div
																class="image-rew click"
																role="button"
																tabindex="0"
																data-video-src="<?php echo esc_url( $video_embed_src ); ?>"
																data-video-modal="<?php echo esc_attr( $video_modal_type ); ?>"
															>
																<?php if ( $img_url ) : ?>
																	<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title_reviews_video ? wp_strip_all_tags( $title_reviews_video ) : '' ); ?>">
																<?php endif; ?>
															</div>
														<?php else : ?>
															<div class="image-rew image-rew--no-video">
																<?php if ( $img_url ) : ?>
																	<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title_reviews_video ? wp_strip_all_tags( $title_reviews_video ) : '' ); ?>">
																<?php endif; ?>
															</div>
														<?php endif; ?>
														<div class="sity-t">
															<?php if ( $title_reviews_video ) : ?>
																<h3><?php echo wp_kses_post( $title_reviews_video ); ?></h3>
															<?php endif; ?>
															<?php if ( $text_reviews_video ) : ?>
																<p><?php echo wp_kses_post( $text_reviews_video ); ?></p>
															<?php endif; ?>
														</div>
													</div>
												</div>
											<?php endwhile; ?>
										<?php endif; ?>
									</div>
								</div>
								<div class="wrap-dots-wra">
									<div class="carousel-button-left_m"><a href="#"><img width="53" height="53" src="<?php echo esc_url( get_template_directory_uri() . '/img/arrow-l.webp' ); ?>" alt=""></a></div>
									<div class="carousel-button-right_m"><a href="#"><img width="53" height="53" src="<?php echo esc_url( get_template_directory_uri() . '/img/arrow-r.webp' ); ?>" alt=""></a></div>
								</div>
							</div>
							<div class="wrap-order-t gogle-button-open3">
								<?php if ( $yellow_button_text_video ) : ?>
									<a class="order-mr" href="<?php echo esc_url( $yllow_button_link_r2 ); ?>">
										<span><?php echo wp_kses_post( $yellow_button_text_video ); ?></span>
										<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"></path>
										</svg>
									</a>
								<?php endif; ?>
							</div>
						</div>

						<div class="overlay_vt video-reviews-hello-overlay_vt" id="page-reviews-video-overlay"></div>
						<div class="modal_vt video-reviews-hello-modal_vt video-reviews-hello-modal_vt--default" id="page-reviews-video-modal" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Video', 'traveliz' ); ?>">
							<div class="modal-content_vt video-reviews-hello-content_vt">
								<iframe
									id="page-reviews-video-iframe"
									class="video-reviews-modal-iframe_vt video-reviews-modal-iframe_vt--landscape"
									title="<?php echo esc_attr__( 'Video', 'traveliz' ); ?>"
									allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
									referrerpolicy="strict-origin-when-cross-origin"
									allowfullscreen
								></iframe>
							</div>
							<button type="button" class="modal-close_vt" aria-label="<?php echo esc_attr__( 'Закрыть', 'traveliz' ); ?>"></button>
						</div>
						<div class="modal_vt video-reviews-hello-modal_vt chort video-reviews-hello-modal_vt--short" id="page-reviews-video-modal-short" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Video', 'traveliz' ); ?>">
							<div class="modal-content_vt video-reviews-short-content_vt">
								<button type="button" class="modal-close_vt modal-close_vt--short" aria-label="<?php echo esc_attr__( 'Закрыть', 'traveliz' ); ?>"></button>
								<iframe
									id="page-reviews-video-iframe-short"
									class="video-reviews-modal-iframe_vt video-reviews-modal-iframe_vt--short"
									title="<?php echo esc_attr__( 'Video', 'traveliz' ); ?>"
									allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
									referrerpolicy="strict-origin-when-cross-origin"
									allowfullscreen
								></iframe>
							</div>
						</div>
					</div>
                </div>
            </section>
<?php get_template_part('template-parts/reviews'); ?>



            <section class="reviews-section google reviews2">
                <div class="container-3">
                  <?php if ( $google_maps ) : ?>
                  <h2><?php echo wp_kses_post( $google_maps ); ?></h2>
                  <?php endif; ?>
                  <?php if ( $rewiew_platforma ) : ?>
                  <div class="real"><?php echo wp_kses_post( $rewiew_platforma ); ?></div>
                  <?php endif; ?>
                    <div class="reviews-container-into caruael_t reviews2_caruael_t googl2">
                        <div class="carousel_m shadow_m">
                            <div class="carousel-wrapper_m">
                                <div class="carousel-items_m caruael_tt">
                                    <?php if ( have_rows( 'google_slider' ) ) : ?>
                                        <?php while ( have_rows( 'google_slider' ) ) : the_row(); ?>
                                            <?php
                                            $google_img = get_sub_field( 'google_img' );
                                            $google_rew_title = get_sub_field( 'google_rew_title' );
                                            $google_img_star = get_sub_field( 'google_img_star' );
                                            $google_rew_text = get_sub_field( 'google_rew_text' );
                                            
                                            // Обработка изображения
                                            $img_url = '';
                                            if ( $google_img ) {
                                                if ( is_array( $google_img ) && ! empty( $google_img['url'] ) ) {
                                                    $img_url = $google_img['url'];
                                                } elseif ( is_string( $google_img ) ) {
                                                    $img_url = $google_img;
                                                }
                                            }
                                            
                                            // Обработка изображения звезд
                                            $star_url = '';
                                            if ( $google_img_star ) {
                                                if ( is_array( $google_img_star ) && ! empty( $google_img_star['url'] ) ) {
                                                    $star_url = $google_img_star['url'];
                                                } elseif ( is_string( $google_img_star ) ) {
                                                    $star_url = $google_img_star;
                                                }
                                            }
                                            ?>
                                            <div class="carousel-block_m">
                                                <div class="gogle3">
                                                    <div class="image-rew2 googl3">
                                                        <div class="foto-otzv">
                                                            <?php if ( $img_url ) : ?>
                                                                <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $google_rew_title ? wp_strip_all_tags( $google_rew_title ) : '' ); ?>">
                                                            <?php endif; ?>
                                                            <?php if ( $google_rew_title ) : ?>
                                                                <span class="mg"><?php echo wp_kses_post( $google_rew_title ); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if ( $star_url ) : ?>
                                                            <div class="star-2">
                                                                <img src="<?php echo esc_url( $star_url ); ?>" alt="">
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if ( $google_rew_text ) : ?>
                                                        <div class="sity-t">
                                                            <?php echo wp_kses_post( $google_rew_text ); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/img/goole.webp" alt="" />
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="wrap-dots-wra">
                                <div class="carousel-button-left_m"><a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-l.webp" alt=""></a></div>
                                <div class="carousel-button-right_m"><a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-r.webp" alt=""></a></div>
                            </div>
                        </div>
                        <div class="wrap-order-t">
                            <a class="order-mr gogle-button gogle-button-open" href="<?php echo esc_url( $button_google_link ); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/google.webp" alt="" />
                                <span><?php echo wp_kses_post( $button_google ); ?></span>
                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

<?php get_template_part('template-parts/tourist_reviews'); ?>


<?php get_footer(); ?>
