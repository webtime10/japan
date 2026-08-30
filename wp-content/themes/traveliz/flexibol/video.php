<?php
// Video reviews block for the CURRENT flexible-content row.
// ACF layout: s_flexibol_video_reviews

$layout = get_row_layout();
if ( $layout !== 's_flexibol_video_reviews' ) {
	return;
}

$v1_title = get_sub_field( 's_flexibol_video_1_title' );
$v2_title = get_sub_field( 's_flexibol_video_2_title' );
$v3_title = get_sub_field( 's_flexibol_video_3_title' );

$v1_image = get_sub_field( 's_flexibol_video_1_image' );
$v2_image = get_sub_field( 's_flexibol_video_2_image' );
$v3_image = get_sub_field( 's_flexibol_video_3_image' );

$v1_img_url = ( is_array( $v1_image ) && ! empty( $v1_image['url'] ) ) ? $v1_image['url'] : '';$v2_img_url = ( is_array( $v2_image ) && ! empty( $v2_image['url'] ) ) ? $v2_image['url'] : '';$v3_img_url = ( is_array( $v3_image ) && ! empty( $v3_image['url'] ) ) ? $v3_image['url'] : '';
$embed1  = get_sub_field( 's_flexibol_video_1_embed' );
$embed2  = get_sub_field( 's_flexibol_video_2_embed' );
$embed3  = get_sub_field( 's_flexibol_video_3_embed' );
$short1  = get_sub_field( 's_flexibol_video_1_short' );
$short2  = get_sub_field( 's_flexibol_video_2_short' );
$short3  = get_sub_field( 's_flexibol_video_3_short' );

$video1 = traveliz_resolve_video_embed( $short1, $embed1 );
$video2 = traveliz_resolve_video_embed( $short2, $embed2 );
$video3 = traveliz_resolve_video_embed( $short3, $embed3 );

$extra_text = get_sub_field( 's_flexibol_extra_text' );

$row_ix        = function_exists( 'get_row_index' ) ? (int) get_row_index() : 0;
$modal_suffix  = $row_ix ? '-' . $row_ix : '';$stationary_id = 'video-stationary-panels' . $modal_suffix;

/**
 * @param array{src: string, modal: string} $video
 */
$render_video_click = static function ( array $video ) {
	if ( empty( $video['src'] ) || empty( $video['modal'] ) ) {
		echo '<div class="click image-rew--no-video">&nbsp;</div>';
		return;
	}
	printf(
		'<div class="click" data-video-src="%s" data-video-modal="%s">&nbsp;</div>',
		esc_url( $video['src'] ),
		esc_attr( $video['modal'] )
	);
};
?>

<div class="video-reviews-block">
	<section class="short-text-and-video2">
		<div class="container-4">
			<div class="into-video-region">
				<div class="cats-wrap">
					<div class="cats">
						<div class="cats-v">

							<div class="cat-item one-slide">
								<div class="wrap-region-v-1">
									<div class="v-1-one video-review-trigger" role="button" tabindex="0" data-date="1">
										<img class="rec rec1" src="<?php echo esc_url( $v1_img_url ? $v1_img_url : ( get_template_directory_uri() . '/img/Rectangle58.webp' ) ); ?>" alt="">
										<?php $render_video_click( $video1 ); ?>
										<div class="video-otziv">
											<?php if ( $v1_title ) : ?>
												<p><?php echo wp_kses_post( $v1_title ); ?></p>
											<?php else : ?>
												<p></p>
											<?php endif; ?>
										</div>
									</div>

									<div class="v-1-two video-review-trigger" role="button" tabindex="0" data-date="2">
										<img class="rec rec2" src="<?php echo esc_url( $v2_img_url ? $v2_img_url : ( get_template_directory_uri() . '/img/Rectangle57.webp' ) ); ?>" alt="">
										<?php $render_video_click( $video2 ); ?>
										<div class="video-otziv">
											<?php if ( $v2_title ) : ?>
												<p><?php echo wp_kses_post( $v2_title ); ?></p>
											<?php else : ?>
												<p></p>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>

							<div class="cat-item two-slide">
								<div class="v-2-one video-review-trigger" role="button" tabindex="0" data-date="3">
									<img class="rec rec3" src="<?php echo esc_url( $v3_img_url ? $v3_img_url : ( get_template_directory_uri() . '/img/Rectangle56.webp' ) ); ?>" alt="">
									<?php $render_video_click( $video3 ); ?>
									<div class="video-otziv">
										<?php if ( $v3_title ) : ?>
											<p><?php echo wp_kses_post( $v3_title ); ?></p>
										<?php else : ?>
											<p></p>
										<?php endif; ?>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>

				<div class="dop-car">
					<?php if ( $extra_text ) : ?>
						<?php echo wp_kses_post( $extra_text ); ?>
					<?php else : ?>
						Смотрите видео, если не хотите читать всю статью
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<div class="video-stationary-panels" id="<?php echo esc_attr( $stationary_id ); ?>" hidden aria-hidden="true">
		<div class="video-stationary-panel" id="video-stationary-panel-1<?php echo esc_attr( $modal_suffix ); ?>" data-date="1">
			<div class="video-stationary-frame video-stationary-frame--data-only" data-iframe-src="<?php echo esc_attr( $video1['src'] ); ?>">
				<?php if ( empty( $video1['src'] ) ) : ?>
					<span class="video-stationary-empty">Video 1</span>
				<?php endif; ?>
			</div>
		</div>

		<div class="video-stationary-panel" id="video-stationary-panel-2<?php echo esc_attr( $modal_suffix ); ?>" data-date="2">
			<div class="video-stationary-frame video-stationary-frame--data-only" data-iframe-src="<?php echo esc_attr( $video2['src'] ); ?>">
				<?php if ( empty( $video2['src'] ) ) : ?>
					<span class="video-stationary-empty">Video 2</span>
				<?php endif; ?>
			</div>
		</div>

		<div class="video-stationary-panel" id="video-stationary-panel-3<?php echo esc_attr( $modal_suffix ); ?>" data-date="3">
			<div class="video-stationary-frame video-stationary-frame--data-only" data-iframe-src="<?php echo esc_attr( $video3['src'] ); ?>">
				<?php if ( empty( $video3['src'] ) ) : ?>
					<span class="video-stationary-empty">Video 3</span>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="overlay_vt video-reviews-hello-overlay_vt" id="video-reviews-hello-overlay<?php echo esc_attr( $modal_suffix ); ?>"></div>

	<div class="modal_vt video-reviews-hello-modal_vt video-reviews-hello-modal_vt--default" id="video-reviews-hello-modal<?php echo esc_attr( $modal_suffix ); ?>" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Video', 'traveliz' ); ?>">
		<div class="modal-content_vt video-reviews-hello-content_vt">
			<iframe
				id="video-reviews-modal-iframe<?php echo esc_attr( $modal_suffix ); ?>"
				class="video-reviews-modal-iframe_vt video-reviews-modal-iframe_vt--landscape"
				title="<?php echo esc_attr__( 'Video', 'traveliz' ); ?>"
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
				referrerpolicy="strict-origin-when-cross-origin"
				allowfullscreen
			></iframe>
		</div>
		<button type="button" class="modal-close_vt" aria-label="<?php echo esc_attr__( 'Закрыть', 'traveliz' ); ?>"></button>
	</div>

	<div class="modal_vt video-reviews-hello-modal_vt chort video-reviews-hello-modal_vt--short" id="video-reviews-hello-modal-short<?php echo esc_attr( $modal_suffix ); ?>" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Video', 'traveliz' ); ?>">
		<div class="modal-content_vt video-reviews-short-content_vt">
			<button type="button" class="modal-close_vt modal-close_vt--short" aria-label="<?php echo esc_attr__( 'Закрыть', 'traveliz' ); ?>"></button>
			<iframe
				id="video-reviews-modal-iframe-short<?php echo esc_attr( $modal_suffix ); ?>"
				class="video-reviews-modal-iframe_vt video-reviews-modal-iframe_vt--short"
				title="<?php echo esc_attr__( 'Video', 'traveliz' ); ?>"
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
				referrerpolicy="strict-origin-when-cross-origin"
				allowfullscreen
			></iframe>
		</div>
	</div>
</div>
