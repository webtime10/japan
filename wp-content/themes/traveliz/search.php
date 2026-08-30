<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package traveliz
 */

get_header();
$search_is_rtl = traveliz_pll_is_rtl();
?>
<main id="primary" class="search-primary <?php echo $search_is_rtl ? 'search-results-page--rtl' : 'search-results-page--ltr'; ?>" dir="<?php echo $search_is_rtl ? 'rtl' : 'ltr'; ?>">
	<div class="container-4">
		<header class="search-results-header">
			<h1>
				<?php
				printf(
					'%s: %s',
					esc_html( get_theme_translation( 'search_results_title' ) ),
					'<span>' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
			<p class="search-results-query"><?php echo esc_html( get_theme_translation( 'search_results_hint' ) ); ?></p>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="search-results-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'search-result-card' ); ?>>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="search-result-excerpt"><?php echo wp_kses_post( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ?: get_the_content() ), 24 ) ); ?></p>
						<a class="search-result-link" href="<?php the_permalink(); ?>"><?php echo wp_kses_post( wp_parse_url( get_permalink(), PHP_URL_PATH ) ?: get_permalink() ); ?></a>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="search-results-empty">
				<h2><?php echo esc_html( get_theme_translation( 'search_no_results_title' ) ); ?></h2>
				<p><?php echo esc_html( get_theme_translation( 'search_no_results_text' ) ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
