<?php
/**
 * Single template for post type: travelers_stories
 * URL slug: /story/post-name/
 *
 * @package traveliz
 */

get_header();

while ( have_posts() ) :
	the_post();

	$route = get_field( 'route' );
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'traveler-story-single' ); ?>>
		<div class="container-4">
			<header class="traveler-story-single__header">
				<h1 class="traveler-story-single__title gip-0"><?php the_title(); ?></h1>
				<?php if ( $route ) : ?>
					<p class="traveler-story-single__route anna_route"><?php echo esc_html( $route ); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="traveler-story-single__thumbnail story-thumbnail">
					<?php the_post_thumbnail( 'large', array( 'class' => 'story-thumbnail__img' ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( get_the_content() ) : ?>
				<div class="traveler-story-single__content story-content">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
		</div>
	</article>

	<?php
endwhile;

get_footer();
