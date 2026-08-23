<?php
/**
 * Admin layout header (OpenCart-style).
 *
 * @var string $title
 * @var string $heading_title
 * @var string $route
 * @var array|null $flash
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nav_items = array(
	'dashboard'    => 'Панель',
	'language'     => 'Языки',
	'manufacturer' => 'Регионы',
	'category'     => 'Категории',
	'product'      => 'Округа',
	'marker'       => 'Маркеры',
);
?>
<div class="wrap map-plum-admin">
	<h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
	<hr class="wp-header-end">

	<nav class="map-plum-nav">
		<ul>
			<?php foreach ( $nav_items as $nav_route => $label ) : ?>
				<li class="<?php echo $route === $nav_route ? 'active' : ''; ?>">
					<a href="<?php echo esc_url( Map_Plum_Router::url( $nav_route ) ); ?>"><?php echo esc_html( $label ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<?php include MAP_PLUM_PATH . 'admin/views/partials/flash.php'; ?>

	<div id="content" class="map-plum-content">
		<div class="page-header">
			<div class="container-fluid">
				<h1><?php echo esc_html( $heading_title ); ?></h1>
				<div class="pull-right page-header-actions">
					<?php if ( ! empty( $header_buttons ) ) : ?>
						<?php echo $header_buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
