<?php
/**
 * Edit page template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_edit = $review !== null;
$review_id = $is_edit ? $review->news_id : 0;
$name = $is_edit ? $review->name : '';
$email = $is_edit && isset( $review->email ) ? $review->email : '';
$text = $is_edit ? $review->text : '';
$link = $is_edit && isset( $review->link ) ? $review->link : '';
$rating = $is_edit && isset( $review->rating ) ? intval( $review->rating ) : 0;
// Status: keywords = '1' опубликовано, '0' не опубликовано. По умолчанию '1'
$keywords = $is_edit ? ( $review->keywords == '1' ? '1' : '0' ) : '1';
$photo_filename = $is_edit ? $review->reiting : '';
$photo = $photo_filename ? WT_Reviews::get_photo_url( $photo_filename ) : '';

// Setup WYSIWYG editors
wp_enqueue_editor();
?>

<div class="wrap">
	<h1><?php echo $is_edit ? 'Edit Review' : 'Add New Review'; ?></h1>
	
	<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" enctype="multipart/form-data">
		<?php wp_nonce_field( 'wt_reviews_save', 'wt_reviews_nonce' ); ?>
		<input type="hidden" name="action" value="wt_reviews_save" />
		<input type="hidden" name="review_id" value="<?php echo esc_attr( $review_id ); ?>" />
		
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label>Photo</label>
					</th>
					<td>
						<div class="wt-reviews-photo-display" data-avatar-url="<?php echo esc_url( get_template_directory_uri() . '/img/avatar.webp' ); ?>">
							<?php
							$preview_src = $photo ? $photo : get_template_directory_uri() . '/img/avatar.webp';
							?>
							<img
								id="wt-reviews-photo-preview"
								src="<?php echo esc_url( $preview_src ); ?>"
								style="width: 200px; height: 200px; object-fit: cover; display: block; border-radius: 20%;"
							/>
							<input type="hidden" name="photo_filename" id="wt-reviews-photo-filename" value="<?php echo esc_attr( $photo_filename ); ?>" />
							<input type="hidden" name="delete_photo" id="wt-reviews-delete-photo" value="0" />
							<p class="description">Current photo. Click "DEL" to remove it and use the default avatar.</p>
							<button type="button" class="button button-secondary" id="wt-reviews-delete-photo-btn">DEL</button>
						</div>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="name">Name</label>
					</th>
					<td>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $name ); ?>" class="regular-text" required />
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="email">Email</label>
					</th>
					<td>
						<input type="email" name="email" id="email" value="<?php echo esc_attr( $email ); ?>" class="regular-text" />
					</td>
				</tr>
				
				<!-- Link field removed: link is no longer used in admin -->
				
				<tr>
					<th scope="row">
						<label for="text">Text</label>
					</th>
					<td>
						<?php
						wp_editor(
							$text,
							'text',
							array(
								'textarea_name' => 'text',
								'textarea_rows' => 15,
								'media_buttons' => true,
								'tinymce' => true,
								'quicktags' => true,
							)
						);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Rating</label>
					</th>
					<td>
						<div class="review-rating-admin">
							<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
								<label style="display: inline-block; margin-right: 15px; cursor: pointer;">
									<input type="radio" name="rating" value="<?php echo $i; ?>" <?php checked( $rating, $i ); ?> style="margin-right: 5px;" />
									<?php
									// Выводим звезды
									for ( $j = 1; $j <= 5; $j++ ) {
										if ( $j <= $i ) {
											echo '<span style="color: #ec9801;">★</span>';
										} else {
											echo '<span style="color: #ccc;">☆</span>';
										}
									}
									?>
									<span style="margin-left: 5px;"><?php echo $i; ?></span>
								</label>
							<?php endfor; ?>
							<label style="display: inline-block; margin-right: 15px; cursor: pointer;">
								<input type="radio" name="rating" value="0" <?php checked( $rating, 0 ); ?> style="margin-right: 5px;" />
								<span>No rating</span>
							</label>
						</div>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="keywords">Status</label>
					</th>
					<td>
						<select name="keywords" id="keywords">
							<option value="1" <?php selected( $keywords, '1' ); ?>>Опубликовано</option>
							<option value="0" <?php selected( $keywords, '0' ); ?>>Не опубликовано</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $is_edit ? 'Update Review' : 'Add Review'; ?>" />
			<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'wt-reviews' ), admin_url( 'admin.php' ) ) ); ?>" class="button">Cancel</a>
		</p>
	</form>
</div>
