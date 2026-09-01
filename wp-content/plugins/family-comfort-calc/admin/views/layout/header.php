<?php
/**
 * Admin layout header.
 *
 * @var string $title
 * @var string $heading_title
 * @var string $route
 * @var string $header_buttons
 * @var array|null $flash
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nav_items = array_merge(
	array( 'dashboard' => __( 'Главная', 'family-comfort-calc' ) ),
	fcc_get_group_types()
);

$route_to_nav = array(
	'dashboard'          => 'dashboard',
	'age_category'       => 'age',
	'interest_category'  => 'interest',
	'direction_category' => 'direction',
);

$active_nav = isset( $route_to_nav[ $route ] ) ? $route_to_nav[ $route ] : 'dashboard';

if ( empty( $heading_title ) && ! empty( $title ) ) {
	$heading_title = $title;
}
?>
<div class="wrap ai-calculator-admin">
	<h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
	<hr class="wp-header-end">

	<nav class="ai-calculator-nav">
		<ul>
			<?php foreach ( $nav_items as $nav_key => $label ) : ?>
				<li class="<?php echo $active_nav === $nav_key ? 'active' : ''; ?>">
					<?php if ( 'dashboard' === $nav_key ) : ?>
						<a href="<?php echo esc_url( FCC_Router::url( 'dashboard', 'index' ) ); ?>"><?php echo esc_html( $label ); ?></a>
					<?php else : ?>
						<a href="<?php echo esc_url( FCC_Router::url( $nav_key . '_category', 'index' ) ); ?>"><?php echo esc_html( $label ); ?></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<?php include FCC_PATH . 'admin/views/partials/flash.php'; ?>

	<div id="content" class="ai-calculator-content">
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
