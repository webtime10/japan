<?php
/**
 * Settings page template.
 *
 * @var int    $background_image_id
 * @var string $background_image_url
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1 class="wp-heading-inline">WT Reviews Settings</h1>
	<hr class="wp-header-end">

	<?php if ( isset( $_GET['updated'] ) && '1' === $_GET['updated'] ) : ?>
		<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'wt_reviews_save_settings', 'wt_reviews_settings_nonce' ); ?>
		<input type="hidden" name="action" value="wt_reviews_save_settings">

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row">
					<label for="wt-reviews-background-image-id">Reviews background image</label>
				</th>
				<td>
					<input type="hidden" id="wt-reviews-background-image-id" name="background_image_id" value="<?php echo esc_attr( (string) $background_image_id ); ?>">

					<div id="wt-reviews-background-preview" style="margin-bottom: 12px;">
						<?php if ( $background_image_url ) : ?>
							<img src="<?php echo esc_url( $background_image_url ); ?>" alt="" style="max-width: 360px; height: auto; display: block;">
						<?php endif; ?>
					</div>

					<button type="button" class="button" id="wt-reviews-select-background">Select image</button>
					<button type="button" class="button" id="wt-reviews-remove-background" <?php disabled( ! $background_image_url ); ?>>Remove image</button>
					<p class="description">This image is used as background for <code>.reviews-section.custom-rew</code>.</p>
				</td>
			</tr>
		</table>

		<?php submit_button( 'Save settings' ); ?>
	</form>
</div>

<script>
(function($) {
	'use strict';

	$(function() {
		var frame;
		var $input = $('#wt-reviews-background-image-id');
		var $preview = $('#wt-reviews-background-preview');
		var $remove = $('#wt-reviews-remove-background');

		$('#wt-reviews-select-background').on('click', function(e) {
			e.preventDefault();

			if (frame) {
				frame.open();
				return;
			}

			frame = wp.media({
				title: 'Select reviews background image',
				button: {
					text: 'Use this image'
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});

			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				var previewUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

				$input.val(attachment.id);
				$preview.html('<img src="' + previewUrl + '" alt="" style="max-width: 360px; height: auto; display: block;">');
				$remove.prop('disabled', false);
			});

			frame.open();
		});

		$remove.on('click', function(e) {
			e.preventDefault();
			$input.val('');
			$preview.empty();
			$remove.prop('disabled', true);
		});
	});
})(jQuery);
</script>
