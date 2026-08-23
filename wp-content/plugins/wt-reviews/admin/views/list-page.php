<?php
/**
 * List page template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1 class="wp-heading-inline">WT Reviews</h1>
	<hr class="wp-header-end">
	
	<?php
	// Show success messages
	if ( isset( $_GET['updated'] ) && $_GET['updated'] == '1' ) {
		echo '<div class="notice notice-success is-dismissible"><p>Review updated successfully.</p></div>';
	}
	if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == '1' ) {
		echo '<div class="notice notice-success is-dismissible"><p>Review deleted successfully.</p></div>';
	}
	
	// Проверка подключения к базе
	global $wpdb;
	if ( ! $wpdb ) {
		echo '<div class="notice notice-error"><p><strong>Ошибка подключения к базе данных!</strong> Нет подключения к базе данных WordPress.</p></div>';
		return;
	}
	
	// Проверка данных
	if ( empty( $reviews ) ) {
		echo '<div class="notice notice-info"><p>Нет отзывов для отображения.</p></div>';
		return;
	}
	?>
	
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width: 50px;">№</th>
				<th style="width: 100px;">Photo</th>
				<th style="width: 250px;">Name</th>
				<th style="width: 80px;">Stars</th>
				<th style="width: 100px;">Status</th>
				<th style="width: 150px;">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$counter = 1;
			foreach ( $reviews as $review ) : 
				$is_published = ( $review->keywords == '1' );
			?>
				<tr>
					<td><?php echo $counter++; ?></td>
					<td>
						<?php
						if ( ! empty( $review->reiting ) ) {
							$photo_url = site_url() . '/uploads/' . $review->reiting;
						} else {
							// Используем аватар по умолчанию, если фото нет
							$photo_url = get_template_directory_uri() . '/img/avatar.webp';
						}
						?>
						<img src="<?php echo esc_url( $photo_url ); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 20%;" />
					</td>
					<td>
						<?php 
						// Имя отзыва
						$display_name = ! empty( $review->name ) ? esc_html( $review->name ) : '—';
						echo $display_name;
						?>
					</td>
					<td>
						<?php
						// Рейтинг в виде числа или "No rating"
						$rating = isset( $review->rating ) ? intval( $review->rating ) : 0;
						if ( $rating > 0 ) {
							echo esc_html( $rating ) . '/5';
						} else {
							echo 'No rating';
						}
						?>
					</td>
					<td>
						<?php 
						// Проверяем: опубликовано только если keywords = '1' И есть имя (русское или английское)
						$has_name = ( ! empty( $review->name ) || ! empty( $review->name_en ) );
						$is_published = ( $review->keywords == '1' && $has_name );
						?>
						<?php if ( $is_published ) : ?>
							<span style="color: green; font-size: 18px;">✓</span> Published
						<?php else : ?>
							<span style="color: red; font-size: 18px;">✗</span> Not Published
						<?php endif; ?>
					</td>
					<td>
						<?php
						$edit_url = add_query_arg( array(
							'page' => 'wt-reviews-edit',
							'review_id' => $review->news_id,
						), admin_url( 'admin.php' ) );
						
						$delete_url = wp_nonce_url(
							add_query_arg( array(
								'action' => 'wt_reviews_delete',
								'review_id' => $review->news_id,
							), admin_url( 'admin-post.php' ) ),
							'wt_reviews_delete',
							'wt_reviews_delete_nonce'
						);
						?>
						<a href="<?php echo esc_url( $edit_url ); ?>" class="button button-small">Edit</a>
						<a href="<?php echo esc_url( $delete_url ); ?>" class="button button-small" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
