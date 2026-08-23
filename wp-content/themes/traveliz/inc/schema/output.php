<?php
/**
 * Schema output.
 *
 * @package traveliz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'traveliz_schema_collect_text_recursive' ) ) {
	/**
	 * Collect text fragments from nested row data.
	 *
	 * @param mixed $value Value to inspect.
	 * @param array $out   Output strings.
	 * @return void
	 */
	function traveliz_schema_collect_text_recursive( $value, array &$out ) {
		if ( is_scalar( $value ) ) {
			$text = traveliz_schema_clean_text( $value );
			if ( $text !== '' ) {
				$out[] = $text;
			}
			return;
		}

		if ( ! is_array( $value ) ) {
			return;
		}

		foreach ( $value as $k => $v ) {
			// Skip layout key and image blobs.
			if ( $k === 'acf_fc_layout' || $k === 'ID' || $k === 'id' || $k === 'url' || $k === 'sizes' ) {
				continue;
			}
			traveliz_schema_collect_text_recursive( $v, $out );
		}
	}
}

if ( ! function_exists( 'traveliz_schema_generate_graph' ) ) {
	/**
	 * Build one graph object similar to original architecture.
	 *
	 * @return array|null
	 */
	function traveliz_schema_generate_graph() {
		if ( ! is_singular() ) {
			return null;
		}

		global $post;

		$post_id = (int) get_queried_object_id();
		if ( ! $post_id ) {
			return null;
		}

		if ( ! $post ) {
			$post = get_post( $post_id );
		}

		$page_url = get_permalink( $post_id );
		if ( ! $page_url ) {
			return null;
		}

		$graph       = array();
		$parts       = array();
		$image_cache = array();

		$dest_id = $page_url . '#destination';
		$root_id = $page_url . '#content';

		$destination = array(
			'@type' => 'TouristDestination',
			'@id'   => $dest_id,
			'name'  => traveliz_schema_clean_text( get_the_title( $post_id ) ),
			'url'   => $page_url,
		);
		$graph[] = $destination;

		$in_language = function_exists( 'pll_current_language' ) ? pll_current_language( 'locale' ) : get_locale();
		if ( ! $in_language ) {
			$in_language = get_locale();
		}

		$featured_id = (int) get_post_thumbnail_id( $post_id );
		$image_ref   = null;
		if ( $featured_id ) {
			$image_ref = traveliz_schema_image_ref( $featured_id, $page_url, $graph, $image_cache );
		}

		$root = array(
			'@type'          => 'CreativeWork',
			'@id'            => $root_id,
			'name'           => traveliz_schema_clean_text( get_the_title( $post_id ) ),
			'url'            => $page_url,
			'inLanguage'     => $in_language,
			'about'          => array( '@id' => $dest_id ),
			'additionalType' => 'https://schema.org/TravelGuide',
		);
		if ( $image_ref ) {
			$root['image'] = $image_ref;
		}

		$intro_text = traveliz_schema_clean_text( get_the_content( null, false, $post_id ) );
		if ( $intro_text !== '' ) {
			$intro_id = $page_url . '#section-intro';
			$graph[]  = traveliz_schema_make_section( $intro_id, 'Introduction', $intro_text, $root_id );
			$parts[]  = array( '@id' => $intro_id );
		}

		$rows = get_field( 's_flexibol_constructor', $post_id );
		if ( is_array( $rows ) ) {
			$section_index = 0;
			foreach ( $rows as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$layout = (string) ( $row['acf_fc_layout'] ?? '' );
				if ( $layout === '' ) {
					continue;
				}

				$section_index++;
				$section_id = $page_url . '#section-' . $section_index;
				$section_name = traveliz_schema_clean_text( $row['heading'] ?? $row['title'] ?? $row['s_flexibol_faq_main_title'] ?? '' );
				if ( $section_name === '' ) {
					$section_name = 'Section ' . $section_index;
				}
				$text_chunks = array();
				traveliz_schema_collect_text_recursive( $row, $text_chunks );
				$section_text = implode( ' ', array_slice( $text_chunks, 0, 12 ) );

				$mention_refs = traveliz_schema_process_flexible_layout( $layout, $row, $page_url, $section_index, $dest_id, $root_id, $graph, $image_cache );

				$section_node = traveliz_schema_make_section( $section_id, $section_name, $section_text, $root_id );

				if ( ! empty( $mention_refs ) ) {
					if ( count( $mention_refs ) === 1 ) {
						$section_node['mentions'] = $mention_refs[0];
					} else {
						$section_node['mentions'] = $mention_refs;
					}
				}

				$graph[] = $section_node;
				$parts[] = array( '@id' => $section_id );
			}
		}

		// Option FAQ block (front page + landing) as separate node in same graph.
		if ( is_front_page() || is_page_template( 'page-lending.php' ) ) {
			$option_faq = traveliz_schema_collect_option_faq();
			if ( is_array( $option_faq ) && ! empty( $option_faq['@type'] ) ) {
				unset( $option_faq['@context'] );
				$option_faq['@id'] = $page_url . '#options-faq';
				$graph[]           = $option_faq;
			}
		}

		if ( ! empty( $parts ) ) {
			$root['hasPart'] = $parts;
		}
		$graph[] = $root;

		if ( empty( $graph ) ) {
			return null;
		}

		return array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);
	}
}

if ( ! function_exists( 'traveliz_schema_output_json_ld' ) ) {
	/**
	 * Print JSON-LD scripts in head.
	 */
	function traveliz_schema_output_json_ld() {
		$schema = traveliz_schema_generate_graph();
		if ( empty( $schema ) || ! is_array( $schema ) ) {
			return;
		}
		?>
		<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?></script>
		<?php
	}
}

add_action( 'wp_head', 'traveliz_schema_output_json_ld', 99 );
