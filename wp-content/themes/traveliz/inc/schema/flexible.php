<?php
/**
 * Schema for flexible content (s_flexibol_constructor rows).
 * Mirrors ie-flexible-guides: push nodes into @graph, stable #fragment @id, ListItem → @id refs.
 *
 * @package traveliz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'traveliz_schema_process_flexible_layout' ) ) {
	/**
	 * Append schema for one flexible row to $graph. Returns mention refs for the section WebPageElement.
	 *
	 * @param string $layout        acf_fc_layout.
	 * @param array  $row           Row data.
	 * @param string $page_url      Permalink.
	 * @param int    $section_index 1-based section index (#section-N).
	 * @param string $dest_id       #destination @id.
	 * @param string $root_id       #content @id.
	 * @param array  $graph         @graph nodes (by ref).
	 * @param array  $image_cache   Image @id cache (by ref).
	 * @return array|null Array of array( '@id' => full_url ), or null.
	 */
	function traveliz_schema_process_flexible_layout( $layout, array $row, $page_url, $section_index, $dest_id, $root_id, array &$graph, array &$image_cache ) {
		switch ( $layout ) {
			case 's_flexibol_faq':
				return traveliz_schema_process_row_faq( $row, $page_url, $section_index, $graph );

			case 's_flexibol_editor':
				return traveliz_schema_process_row_editor( $row, $page_url, $section_index, $root_id, $graph );

			case 's_flexibol_short_editor':
				return traveliz_schema_process_row_short_editor( $row, $page_url, $section_index, $root_id, $graph );

			case 's_flexibol_video_reviews':
				return traveliz_schema_process_row_video_reviews( $row, $page_url, $section_index, $graph, $image_cache );

			case 's_flexibol_tourist_reviews':
				return traveliz_schema_process_row_tourist_reviews( $row, $page_url, $section_index, $graph );

			case 's_flexibol_seasons_line':
				return traveliz_schema_process_row_seasons_line( $row, $page_url, $section_index, $dest_id, $root_id, $graph );

			case 's_flexibol_regions_comparison':
				return traveliz_schema_process_row_regions_comparison( $row, $page_url, $section_index, $dest_id, $root_id, $graph );

			case 's_flexibol_country_text':
				return traveliz_schema_process_row_country_text( $row, $page_url, $section_index, $root_id, $graph );

			case 's_flexibol_map':
				return null;

			case 's_flexibol_our_experience':
				return traveliz_schema_process_row_our_experience( $row, $page_url, $section_index, $dest_id, $root_id, $graph );

			case 's_flexibol_city_slider':
				return traveliz_schema_process_row_city_slider( $row, $page_url, $section_index, $dest_id, $root_id, $graph );

			case 's_flexibol_attractions_slider':
				return traveliz_schema_process_row_attractions_slider( $row, $page_url, $section_index, $dest_id, $root_id, $graph );

			case 's_flexibol_route_one_day':
				return traveliz_schema_process_row_route_one_day( $row, $page_url, $section_index, $dest_id, $root_id, $graph );

			case 's_flexibol_price_table':
				return traveliz_schema_process_row_price_table( $row, $page_url, $section_index, $graph );

			case 's_flexibol_advice':
				return traveliz_schema_process_row_advice( $row, $page_url, $section_index, $dest_id, $root_id, $graph );

			case 's_flexibol_expert':
				return traveliz_schema_process_row_expert( $row, $page_url, $section_index, $graph, $image_cache );

			case 's_flexibol_active_otd':
				return traveliz_schema_process_row_active_otd( $row, $page_url, $section_index, $dest_id, $root_id, $graph );

			case 's_flexibol_where_to_stay':
				return traveliz_schema_process_row_where_to_stay( $row, $page_url, $section_index, $graph );

			case 's_flexibol_parking':
				return traveliz_schema_process_row_parking( $row, $page_url, $section_index, $graph );

			case 's_flexibol_footer_expert':
				return traveliz_schema_process_row_footer_expert( $row, $page_url, $section_index, $graph, $image_cache );

			default:
				return null;
		}
	}
}

/**
 * Thin adapter for legacy callers.
 *
 * @param string $layout Layout key.
 * @param array  $row    Row.
 * @return array|array<int, array>|null
 */
function traveliz_schema_from_flexible_row( $layout, array $row ) {
	$page_url      = function_exists( 'get_permalink' ) ? get_permalink() : '';	$section_index = 1;
	if ( $page_url === false || $page_url === '' ) {
		return null;
	}
	$dest_id     = $page_url . '#destination';
	$root_id     = $page_url . '#content';
	$graph       = array();
	$image_cache = array();

	$mentions = traveliz_schema_process_flexible_layout( $layout, $row, $page_url, $section_index, $dest_id, $root_id, $graph, $image_cache );
	if ( empty( $mentions ) ) {
		return null;
	}

	if ( count( $mentions ) === 1 ) {
		foreach ( $graph as $node ) {
			if ( ! empty( $node['@id'] ) && $node['@id'] === $mentions[0]['@id'] ) {
				return $node;
			}
		}
	}
	return $graph;
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_faq( array $row, $page_url, $section_index, array &$graph ) {
	$title = $row['s_flexibol_faq_main_title'] ?? 'Questions and Answers';
	$items = array();
	$faq_items = $row['s_flexibol_faq_items'] ?? array();
	if ( is_array( $faq_items ) ) {
		foreach ( $faq_items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$items[] = array(
				'question' => $item['s_flexibol_faq_question'] ?? '',
				'answer'   => $item['s_flexibol_faq_answer'] ?? '',
			);
		}
	}
	$faq_id = $page_url . '#faq-' . $section_index;
	$faq    = traveliz_schema_build_faq_page( $title, $items, $faq_id );
	if ( ! $faq ) {
		return null;
	}
	$graph[] = $faq;
	return array( array( '@id' => $faq_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_editor( array $row, $page_url, $section_index, $root_id, array &$graph ) {
	$html = $row['s_flexibol_editor'] ?? '';
	$text = traveliz_schema_clean_text( $html );
	if ( $text === '' ) {
		return null;
	}
	$el_id = $page_url . '#text-' . $section_index;
	$graph[] = array(
		'@type'    => 'WebPageElement',
		'@id'      => $el_id,
		'isPartOf' => array( '@id' => $root_id ),
		'text'     => $text,
	);
	return array( array( '@id' => $el_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_short_editor( array $row, $page_url, $section_index, $root_id, array &$graph ) {
	$html = $row['s_flexibol_short_editor'] ?? '';
	$text = traveliz_schema_clean_text( $html );
	if ( $text === '' ) {
		return null;
	}
	$el_id = $page_url . '#short-text-' . $section_index;
	$graph[] = array(
		'@type'    => 'WebPageElement',
		'@id'      => $el_id,
		'isPartOf' => array( '@id' => $root_id ),
		'text'     => $text,
	);
	return array( array( '@id' => $el_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param array  $graph
 * @param array  $image_cache
 * @return array|null
 */
function traveliz_schema_process_row_video_reviews( array $row, $page_url, $section_index, array &$graph, array &$image_cache ) {
	$pairs = array(
		array(
			'title' => $row['s_flexibol_video_1_title'] ?? '',
			'embed' => $row['s_flexibol_video_1_embed'] ?? '',
			'thumb' => is_array( $row['s_flexibol_video_1_image'] ?? null ) ? ( $row['s_flexibol_video_1_image'] ?? null ) : null,
		),
		array(
			'title' => $row['s_flexibol_video_2_title'] ?? '',
			'embed' => $row['s_flexibol_video_2_embed'] ?? '',
			'thumb' => is_array( $row['s_flexibol_video_2_image'] ?? null ) ? ( $row['s_flexibol_video_2_image'] ?? null ) : null,
		),
		array(
			'title' => $row['s_flexibol_video_3_title'] ?? '',
			'embed' => $row['s_flexibol_video_3_embed'] ?? '',
			'thumb' => is_array( $row['s_flexibol_video_3_image'] ?? null ) ? ( $row['s_flexibol_video_3_image'] ?? null ) : null,
		),
	);

	$list_id = $page_url . '#video-list-' . $section_index;
	$item_nodes = array();
	$n          = 0;

	foreach ( $pairs as $p ) {
		$url = traveliz_schema_video_embed_src( $p['embed'] ?? '' );
		if ( $url === '' ) {
			continue;
		}
		++$n;
		$vid_id = $page_url . '#video-' . $section_index . '-' . $n;
		$name   = traveliz_schema_clean_text( $p['title'] ?? '' );
		if ( $name === '' ) {
			$name = 'Video';
		}
		$video = array(
			'@type'      => 'VideoObject',
			'@id'        => $vid_id,
			'name'       => $name,
			'contentUrl' => esc_url_raw( $url ),
		);

		$thumb_field = $p['thumb'] ?? null;
		if ( is_array( $thumb_field ) && ! empty( $thumb_field['url'] ) ) {
			$video['thumbnailUrl'] = esc_url_raw( (string) $thumb_field['url'] );
		} elseif ( is_array( $thumb_field ) && ! empty( $thumb_field['ID'] ) ) {
			$turl = wp_get_attachment_image_url( (int) $thumb_field['ID'], 'full' );
			if ( $turl ) {
				$video['thumbnailUrl'] = esc_url_raw( $turl );
			}
		}

		$item_nodes[] = $video;
	}

	$list_name     = 'Video reviews';
	$appended_id   = traveliz_schema_graph_append_item_list_with_nodes( $list_id, $list_name, $item_nodes, $graph, null );
	if ( ! $appended_id ) {
		return null;
	}
	return array( array( '@id' => $appended_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_tourist_reviews( array $row, $page_url, $section_index, array &$graph ) {
	$block_title = traveliz_schema_clean_text( $row['s_flexibol_tourist_block_title'] ?? '' );
	$items_raw   = $row['s_flexibol_tourist_items'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}

	$item_nodes = array();
	$pos        = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$author = traveliz_schema_clean_text( $it['s_flexibol_tourist_name'] ?? '' );
		$body   = traveliz_schema_clean_text( $it['s_flexibol_tourist_text'] ?? '' );
		if ( $author === '' && $body === '' ) {
			continue;
		}
		++$pos;
		$rev_id = $page_url . '#review-' . $section_index . '-' . $pos;
		$review = array(
			'@type'        => 'Review',
			'@id'          => $rev_id,
			'reviewBody'   => $body,
			'reviewRating' => array(
				'@type'       => 'Rating',
				'ratingValue' => 5,
				'bestRating'  => 5,
			),
		);
		if ( $author !== '' ) {
			$review['author'] = array(
				'@type' => 'Person',
				'name'  => $author,
			);
		}
		$item_nodes[] = $review;
	}

	$list_id = $page_url . '#reviews-list-' . $section_index;
	$name    = $block_title !== '' ? $block_title : 'Tourist reviews';
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $name, $item_nodes, $graph, null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $dest_id
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_seasons_line( array $row, $page_url, $section_index, $dest_id, $root_id, array &$graph ) {
	$section_title = traveliz_schema_clean_text( $row['s_flexibol_seasons_line_section_title'] ?? '' );
	if ( $section_title === '' ) {
		$section_title = 'Seasons';
	}
	$pod_zag_pogoda = traveliz_schema_clean_text( $row['pod_zag_pogoda'] ?? '' );
	$months = array( 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december' );
	$item_nodes = array();
	$pos        = 0;
	foreach ( $months as $m ) {
		$t = traveliz_schema_clean_text( $row[ 's_flexibol_season_' . $m . '_title' ] ?? '' );
		$s = traveliz_schema_clean_text( $row[ 's_flexibol_season_' . $m . '_subtitle' ] ?? '' );
		$x = traveliz_schema_clean_text( $row[ 's_flexibol_season_' . $m . '_short_text' ] ?? '' );
		$parts = array_filter( array( $t, $s, $x ) );
		if ( empty( $parts ) ) {
			continue;
		}
		++$pos;
		$name = $t !== '' ? $t : ucfirst( $m );
		$desc = implode( ' ', $parts );
		$item_id = $page_url . '#list-' . $section_index . '-item-' . $pos;
		$item_nodes[] = array(
			'@type'            => 'CreativeWork',
			'@id'              => $item_id,
			'name'             => $name,
			'description'      => $desc,
			'containedInPlace' => array( '@id' => $dest_id ),
			'subjectOf'        => array( '@id' => $root_id ),
		);
	}
	$list_id = $page_url . '#list-' . $section_index;
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $section_title, $item_nodes, $graph, $pod_zag_pogoda !== '' ? $pod_zag_pogoda : null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $dest_id
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_regions_comparison( array $row, $page_url, $section_index, $dest_id, $root_id, array &$graph ) {
	$name = traveliz_schema_clean_text( $row['s_flexibol_regions_comparison_section_title'] ?? '' );
	if ( $name === '' ) {
		$name = 'Regions comparison';
	}
	$dop_text = traveliz_schema_clean_text( $row['comparison_of_regions_dop_text'] ?? '' );
	$items_raw = $row['s_flexibol_regions_items'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}
	$item_nodes = array();
	$pos        = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$city = traveliz_schema_clean_text( $it['s_flexibol_region_city_name'] ?? '' );
		$desc = implode(
			' ',
			array_filter(
				array(
					traveliz_schema_clean_text( $it['s_flexibol_region_weather'] ?? '' ),
					traveliz_schema_clean_text( $it['s_flexibol_region_entertainment'] ?? '' ),
					traveliz_schema_clean_text( $it['s_flexibol_region_transport'] ?? '' ),
					traveliz_schema_clean_text( $it['s_flexibol_region_kids'] ?? '' ),
					traveliz_schema_clean_text( $it['s_flexibol_region_price'] ?? '' ),
				)
			)
		);
		if ( $city === '' && $desc === '' ) {
			continue;
		}
		++$pos;
		$item_id = $page_url . '#list-' . $section_index . '-item-' . $pos;
		$item_nodes[] = array(
			'@type'            => 'Place',
			'@id'              => $item_id,
			'name'             => $city !== '' ? $city : 'Region',
			'description'      => $desc,
			'containedInPlace' => array( '@id' => $dest_id ),
			'subjectOf'        => array( '@id' => $root_id ),
		);
	}
	$list_id = $page_url . '#list-' . $section_index;
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $name, $item_nodes, $graph, $dop_text !== '' ? $dop_text : null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_country_text( array $row, $page_url, $section_index, $root_id, array &$graph ) {
	$title = traveliz_schema_clean_text( $row['s_flexibol_title'] ?? '' );
	$parts = array_filter(
		array(
			traveliz_schema_clean_text( $row['s_flexibol_text'] ?? '' ),
			traveliz_schema_clean_text( $row['s_flexibol_text_2'] ?? '' ),
		)
	);
	$text = implode( ' ', $parts );
	if ( $text === '' ) {
		return null;
	}
	$el_id = $page_url . '#country-text-' . $section_index;
	$node  = array(
		'@type'    => 'WebPageElement',
		'@id'      => $el_id,
		'isPartOf' => array( '@id' => $root_id ),
		'text'     => $text,
	);
	if ( $title !== '' ) {
		$node['name'] = $title;
	}
	$graph[] = $node;
	return array( array( '@id' => $el_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $dest_id
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_our_experience( array $row, $page_url, $section_index, $dest_id, $root_id, array &$graph ) {
	$title = traveliz_schema_clean_text( $row['s_flexibol_our_experience_title'] ?? '' );
	if ( $title === '' ) {
		$title = 'Our experience';
	}
	$items_raw = $row['s_flexibol_our_experience_items'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}
	$item_nodes = array();
	$pos        = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$n = traveliz_schema_clean_text( $it['s_flexibol_card_title'] ?? '' );
		$d = traveliz_schema_clean_text( $it['s_flexibol_card_text'] ?? '' );
		if ( $n === '' && $d === '' ) {
			continue;
		}
		++$pos;
		$item_id = $page_url . '#list-' . $section_index . '-item-' . $pos;
		$item_nodes[] = array(
			'@type'            => 'CreativeWork',
			'@id'              => $item_id,
			'name'             => $n !== '' ? $n : 'Card',
			'description'      => $d,
			'containedInPlace' => array( '@id' => $dest_id ),
			'subjectOf'        => array( '@id' => $root_id ),
		);
	}
	$list_id = $page_url . '#list-' . $section_index;
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $title, $item_nodes, $graph, null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $dest_id
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_city_slider( array $row, $page_url, $section_index, $dest_id, $root_id, array &$graph ) {
	$title = traveliz_schema_clean_text( $row['s_flexibol_city_slider_title'] ?? '' );
	if ( $title === '' ) {
		$title = 'Cities';
	}
	$items_raw = $row['s_flexibol_city_slider_items'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}
	$item_nodes = array();
	$pos        = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$n = traveliz_schema_clean_text( $it['s_flexibol_city_slider_item_title'] ?? '' );
		$d = traveliz_schema_clean_text( $it['s_flexibol_city_slider_item_text'] ?? '' );
		if ( $n === '' && $d === '' ) {
			continue;
		}
		++$pos;
		$item_id = $page_url . '#list-' . $section_index . '-item-' . $pos;
		$item_nodes[] = array(
			'@type'            => 'TouristAttraction',
			'@id'              => $item_id,
			'name'             => $n !== '' ? $n : 'City',
			'description'      => $d,
			'containedInPlace' => array( '@id' => $dest_id ),
			'subjectOf'        => array( '@id' => $root_id ),
		);
	}
	$list_id = $page_url . '#list-' . $section_index;
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $title, $item_nodes, $graph, null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $dest_id
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_attractions_slider( array $row, $page_url, $section_index, $dest_id, $root_id, array &$graph ) {
	$title = traveliz_schema_clean_text( $row['s_flexibol_attractions_title'] ?? '' );
	if ( $title === '' ) {
		$title = 'Attractions';
	}
	$dop_text = traveliz_schema_clean_text( $row['dop_text_landmark'] ?? '' );
	$items_raw = $row['s_flexibol_attractions_items'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}
	$item_nodes = array();
	$pos        = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$n = traveliz_schema_clean_text( $it['s_flexibol_attractions_card_title'] ?? '' );
		$d = traveliz_schema_clean_text( $it['s_flexibol_attractions_text'] ?? '' );
		$u = isset( $it['s_flexibol_attractions_button_link'] ) ? esc_url_raw( (string) $it['s_flexibol_attractions_button_link'] ) : '';		if ( $n === '' && $d === '' ) {
			continue;
		}
		++$pos;
		$item_id = $page_url . '#list-' . $section_index . '-item-' . $pos;
		$ta      = array(
			'@type'            => 'TouristAttraction',
			'@id'              => $item_id,
			'name'             => $n !== '' ? $n : 'Attraction',
			'description'      => $d,
			'containedInPlace' => array( '@id' => $dest_id ),
			'subjectOf'        => array( '@id' => $root_id ),
		);
		if ( $u !== '' ) {
			$ta['url'] = $u;
		}
		$item_nodes[] = $ta;
	}
	$list_id = $page_url . '#list-' . $section_index;
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $title, $item_nodes, $graph, $dop_text !== '' ? $dop_text : null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * Route / day itinerary (cf. section_route_itinerary).
 *
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $dest_id
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_route_one_day( array $row, $page_url, $section_index, $dest_id, $root_id, array &$graph ) {
	$name = traveliz_schema_clean_text( $row['s_flexibol_route_section_title'] ?? '' );
	if ( $name === '' ) {
		$name = 'Route';
	}
	$dop_text = traveliz_schema_clean_text( $row['rout_dop_text'] ?? '' );
	$days = $row['s_flexibol_route_days'] ?? array();
	if ( empty( $days ) || ! is_array( $days ) ) {
		return null;
	}

	$trip_id = $page_url . '#tourist-trip-' . $section_index;
	$trip    = array(
		'@type'     => 'TouristTrip',
		'@id'       => $trip_id,
		'name'      => $name,
		'url'       => $page_url,
		'subjectOf' => array( '@id' => $root_id ),
	);
	if ( $dop_text !== '' ) {
		$trip['description'] = $dop_text;
	}
	$graph[] = $trip;

	$refs                = array( array( '@id' => $trip_id ) );
	$day_idx             = 0;
	$item_nodes_for_list = array();

	foreach ( $days as $day ) {
		if ( ! is_array( $day ) ) {
			continue;
		}
		$badge = traveliz_schema_clean_text( $day['s_flexibol_route_day_badge'] ?? '' );
		$sub   = traveliz_schema_clean_text( $day['s_flexibol_route_day_subtitle'] ?? '' );
		$head  = trim( $badge . ( $sub !== '' ? ' — ' . $sub : '' ) );
		$lines = array();
		$timeline = $day['s_flexibol_route_day_timeline'] ?? array();
		if ( is_array( $timeline ) ) {
			foreach ( $timeline as $ev ) {
				if ( ! is_array( $ev ) ) {
					continue;
				}
				$t = traveliz_schema_clean_text( $ev['s_flexibol_route_time'] ?? '' );
				$x = traveliz_schema_clean_text( $ev['s_flexibol_route_text'] ?? '' );
				if ( $t === '' && $x === '' ) {
					continue;
				}
				$lines[] = trim( $t . ( $x !== '' ? ': ' . $x : '' ) );
			}
		}
		$block = trim( $head . ( ! empty( $lines ) ? '. ' . implode( ' ', $lines ) : '' ) );
		if ( $block === '' ) {
			continue;
		}
		++$day_idx;
		$day_id = $page_url . '#trip-day-' . $section_index . '-' . $day_idx;
		$day_node = array(
			'@type'     => 'CreativeWork',
			'@id'       => $day_id,
			'isPartOf'  => array( '@id' => $root_id ),
			'about'     => array( '@id' => $dest_id ),
			'name'      => $head !== '' ? $head : ( 'Day ' . $day_idx ),
			'text'      => $block,
		);
		$graph[]              = $day_node;
		$item_nodes_for_list[] = $day_node;
	}

	if ( empty( $item_nodes_for_list ) ) {
		return $refs;
	}

	$days_list_id = $page_url . '#itinerary-days-' . $section_index;
	$list_el      = array();
	$pos          = 0;
	foreach ( $item_nodes_for_list as $dn ) {
		++$pos;
		$list_el[] = array(
			'@type'    => 'ListItem',
			'position' => $pos,
			'item'     => array( '@id' => $dn['@id'] ),
		);
	}
	$days_list = array(
		'@type'             => 'ItemList',
		'@id'               => $days_list_id,
		'name'              => 'Itinerary days',
		'numberOfItems'     => count( $list_el ),
		'itemListOrder'     => 'https://schema.org/ItemListOrderAscending',
		'itemListElement'   => $list_el,
	);
	if ( $dop_text !== '' ) {
		$days_list['description'] = $dop_text;
	}
	$graph[] = $days_list;
	$refs[]  = array( '@id' => $days_list_id );

	foreach ( $graph as $k => $node ) {
		if ( isset( $node['@id'] ) && $node['@id'] === $trip_id ) {
			$graph[ $k ]['itinerary'] = array( '@id' => $days_list_id );
			break;
		}
	}

	return $refs;
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_price_table( array $row, $page_url, $section_index, array &$graph ) {
	$title = traveliz_schema_clean_text( $row['s_flexibol_price_table_section_title'] ?? '' );
	if ( $title === '' ) {
		$title = 'Prices';
	}
	$items_raw = $row['s_flexibol_price_items'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}

	$offers_refs = array();
	$item_nodes  = array();
	$pos         = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$n = traveliz_schema_clean_text( $it['s_flexibol_price_title'] ?? '' );
		$p = traveliz_schema_clean_text( $it['s_flexibol_price_item_price'] ?? '' );
		$i1 = traveliz_schema_clean_text( $it['s_flexibol_price_input'] ?? '' );
		$i2 = traveliz_schema_clean_text( $it['s_flexibol_price_input_2'] ?? '' );
		$night = traveliz_schema_clean_text( $it['s_flexibol_price_item_night'] ?? '' );
		if ( $n === '' && $p === '' && $i1 === '' && $i2 === '' ) {
			continue;
		}
		++$pos;
		$offer_id = $page_url . '#offer-' . $section_index . '-' . $pos;
		$desc     = trim( implode( ' ', array_filter( array( $i1, $i2, $night ) ) ) );
		$offer    = array(
			'@type' => 'Offer',
			'@id'   => $offer_id,
			'name'  => $n !== '' ? $n : 'Option',
		);
		if ( $desc !== '' ) {
			$offer['description'] = $desc;
		}
		if ( $p !== '' ) {
			$offer['price'] = $p;
		}
		$item_nodes[]  = $offer;
		$offers_refs[] = array( '@id' => $offer_id );
	}

	$list_id = $page_url . '#price-list-' . $section_index;
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $title, $item_nodes, $graph, null ) ) {
		return null;
	}
	return array_merge( array( array( '@id' => $list_id ) ), $offers_refs );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $dest_id
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_advice( array $row, $page_url, $section_index, $dest_id, $root_id, array &$graph ) {
	$title = traveliz_schema_clean_text( $row['s_flexibol_advice_section_title'] ?? '' );
	if ( $title === '' ) {
		$title = 'Advice';
	}
	$items_raw = $row['s_flexibol_advice_items'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}
	$item_nodes = array();
	$pos        = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$n = traveliz_schema_clean_text( $it['s_flexibol_advice_item_title'] ?? '' );
		$d = traveliz_schema_clean_text( $it['s_flexibol_advice_item_text'] ?? '' );
		if ( $n === '' && $d === '' ) {
			continue;
		}
		++$pos;
		$item_id = $page_url . '#list-' . $section_index . '-item-' . $pos;
		$item_nodes[] = array(
			'@type'            => 'CreativeWork',
			'@id'              => $item_id,
			'name'             => $n !== '' ? $n : 'Tip',
			'description'      => $d,
			'containedInPlace' => array( '@id' => $dest_id ),
			'subjectOf'        => array( '@id' => $root_id ),
		);
	}
	$list_id = $page_url . '#list-' . $section_index;
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $title, $item_nodes, $graph, null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param array  $graph
 * @param array  $image_cache
 * @return array|null
 */
function traveliz_schema_process_row_expert( array $row, $page_url, $section_index, array &$graph, array &$image_cache ) {
	$name = traveliz_schema_clean_text( $row['s_flexibol_expert_name'] ?? '' );
	if ( $name === '' ) {
		return null;
	}
	$person_id = $page_url . '#person-' . $section_index;
	$photo     = $row['s_flexibol_expert_photo'] ?? null;
	$img_ref   = null;
	if ( is_array( $photo ) && ! empty( $photo['ID'] ) ) {
		$img_ref = traveliz_schema_image_ref( (int) $photo['ID'], $page_url, $graph, $image_cache );
	} elseif ( is_array( $photo ) && ! empty( $photo['url'] ) ) {
		$img_ref = traveliz_schema_image_ref_from_array( $photo, $page_url, $graph, $image_cache );
	} elseif ( is_numeric( $photo ) ) {
		$img_ref = traveliz_schema_image_ref( (int) $photo, $page_url, $graph, $image_cache );
	}

	$desc = trim(
		implode(
			' ',
			array_filter(
				array(
					traveliz_schema_clean_text( $row['s_flexibol_expert_quote'] ?? '' ),
					traveliz_schema_clean_text( $row['s_flexibol_expert_body'] ?? '' ),
				)
			)
		)
	);

	$node = array(
		'@type' => 'Person',
		'@id'   => $person_id,
		'name'  => $name,
	);
	$job = traveliz_schema_clean_text( $row['s_flexibol_expert_role'] ?? '' );
	if ( $job !== '' ) {
		$node['jobTitle'] = $job;
	}
	if ( $img_ref ) {
		$node['image'] = $img_ref;
	}
	if ( $desc !== '' ) {
		$node['description'] = $desc;
	}
	$graph[] = $node;
	return array( array( '@id' => $person_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param string $dest_id
 * @param string $root_id
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_active_otd( array $row, $page_url, $section_index, $dest_id, $root_id, array &$graph ) {
	$title = traveliz_schema_clean_text( $row['s_flexibol_active_otd_section_title'] ?? '' );
	if ( $title === '' ) {
		$title = 'Active leisure';
	}
	$items_raw = $row['s_flexibol_active_otd_items'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}
	$item_nodes = array();
	$pos        = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$n = traveliz_schema_clean_text( $it['s_flexibol_active_otd_item_title'] ?? '' );
		$d = traveliz_schema_clean_text( $it['s_flexibol_active_otd_item_text'] ?? '' );
		if ( $n === '' && $d === '' ) {
			continue;
		}
		++$pos;
		$item_id = $page_url . '#list-' . $section_index . '-item-' . $pos;
		$item_nodes[] = array(
			'@type'            => 'CreativeWork',
			'@id'              => $item_id,
			'name'             => $n !== '' ? $n : 'Activity',
			'description'      => $d,
			'containedInPlace' => array( '@id' => $dest_id ),
			'subjectOf'        => array( '@id' => $root_id ),
		);
	}
	$list_id = $page_url . '#list-' . $section_index;
	$footer  = traveliz_schema_clean_text( $row['s_flexibol_active_otd_bottom_text'] ?? '' );
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $title, $item_nodes, $graph, $footer !== '' ? $footer : null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_where_to_stay( array $row, $page_url, $section_index, array &$graph ) {
	$title = traveliz_schema_clean_text( $row['s_flexibol_where_stay_section_title'] ?? '' );
	if ( $title === '' ) {
		$title = 'Where to stay';
	}
	$lead = traveliz_schema_clean_text( $row['s_flexibol_where_stay_lead_text'] ?? '' );
	if ( $lead === '' ) {
		$lead = traveliz_schema_clean_text( $row['s_flexibol_where_stay_subtitle'] ?? '' );
	}
	$items_raw = $row['s_flexibol_where_stay_cards'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}
	$item_nodes = array();
	$pos        = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$n = traveliz_schema_clean_text( $it['s_flexibol_where_stay_card_title'] ?? '' );
		$d = traveliz_schema_clean_text( $it['s_flexibol_where_stay_card_text'] ?? '' );
		if ( $n === '' && $d === '' ) {
			continue;
		}
		++$pos;
		$item_id = $page_url . '#list-' . $section_index . '-item-' . $pos;
		$item_nodes[] = array(
			'@type'       => 'LodgingBusiness',
			'@id'         => $item_id,
			'name'        => $n !== '' ? $n : 'Stay',
			'description' => $d,
		);
	}
	$list_id = $page_url . '#list-' . $section_index;
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $title, $item_nodes, $graph, $lead !== '' ? $lead : null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param array  $graph
 * @return array|null
 */
function traveliz_schema_process_row_parking( array $row, $page_url, $section_index, array &$graph ) {
	$title = traveliz_schema_clean_text( $row['s_flexibol_parking_section_title'] ?? '' );
	if ( $title === '' ) {
		$title = 'Parking';
	}
	$sub = traveliz_schema_clean_text( $row['s_flexibol_parking_subtitle'] ?? '' );
	$items_raw = $row['s_flexibol_parking_cards'] ?? array();
	if ( empty( $items_raw ) || ! is_array( $items_raw ) ) {
		return null;
	}
	$item_nodes = array();
	$pos        = 0;
	foreach ( $items_raw as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$n = traveliz_schema_clean_text( $it['s_flexibol_parking_card_title'] ?? '' );
		$d = traveliz_schema_clean_text( $it['s_flexibol_parking_card_text'] ?? '' );
		$map = isset( $it['s_flexibol_parking_card_map_link'] ) ? esc_url_raw( (string) $it['s_flexibol_parking_card_map_link'] ) : '';		if ( $n === '' && $d === '' ) {
			continue;
		}
		++$pos;
		$item_id = $page_url . '#list-' . $section_index . '-item-' . $pos;
		$p       = array(
			'@type'       => 'ParkingFacility',
			'@id'         => $item_id,
			'name'        => $n !== '' ? $n : 'Parking',
			'description' => $d,
		);
		if ( $map !== '' ) {
			$p['hasMap'] = $map;
		}
		$item_nodes[] = $p;
	}
	$list_id = $page_url . '#list-' . $section_index;
	if ( ! traveliz_schema_graph_append_item_list_with_nodes( $list_id, $title, $item_nodes, $graph, $sub !== '' ? $sub : null ) ) {
		return null;
	}
	return array( array( '@id' => $list_id ) );
}

/**
 * @param array  $row
 * @param string $page_url
 * @param int    $section_index
 * @param array  $graph
 * @param array  $image_cache
 * @return array|null
 */
function traveliz_schema_process_row_footer_expert( array $row, $page_url, $section_index, array &$graph, array &$image_cache ) {
	$name = traveliz_schema_clean_text( $row['s_flexibol_footer_expert_title'] ?? '' );
	$text = traveliz_schema_clean_text( $row['s_flexibol_footer_expert_text'] ?? '' );
	$img  = $row['s_flexibol_footer_expert_image'] ?? null;
	if ( $name === '' ) {
		return null;
	}
	$person_id = $page_url . '#person-footer-' . $section_index;
	$img_ref   = null;
	if ( is_array( $img ) && ! empty( $img['ID'] ) ) {
		$img_ref = traveliz_schema_image_ref( (int) $img['ID'], $page_url, $graph, $image_cache );
	} elseif ( is_array( $img ) && ! empty( $img['url'] ) ) {
		$img_ref = traveliz_schema_image_ref_from_array( $img, $page_url, $graph, $image_cache );
	} elseif ( is_numeric( $img ) ) {
		$img_ref = traveliz_schema_image_ref( (int) $img, $page_url, $graph, $image_cache );
	}

	$node = array(
		'@type' => 'Person',
		'@id'   => $person_id,
		'name'  => $name,
	);
	if ( $img_ref ) {
		$node['image'] = $img_ref;
	}
	if ( $text !== '' ) {
		$node['description'] = $text;
	}
	$graph[] = $node;
	return array( array( '@id' => $person_id ) );
}

if ( ! function_exists( 'traveliz_schema_collect_flexible_blocks' ) ) {
	/**
	 * Collect schema nodes from flexible layouts (flat list; mainly for debugging / hooks).
	 *
	 * @param int $post_id Post ID.
	 * @return array<int, array>
	 */
	function traveliz_schema_collect_flexible_blocks( $post_id ) {
		$post_id = (int) $post_id;
		if ( ! $post_id ) {
			return array();
		}
		$page_url = get_permalink( $post_id );
		if ( ! $page_url ) {
			return array();
		}
		$dest_id     = $page_url . '#destination';
		$root_id     = $page_url . '#content';
		$graph       = array();
		$image_cache = array();
		$rows        = get_field( 's_flexibol_constructor', $post_id );
		if ( empty( $rows ) || ! is_array( $rows ) ) {
			return array();
		}
		$section_index = 0;
		$flat          = array();
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$layout = isset( $row['acf_fc_layout'] ) ? (string) $row['acf_fc_layout'] : '';			if ( $layout === '' ) {
				continue;
			}
			$before = count( $graph );
			++$section_index;
			traveliz_schema_process_flexible_layout( $layout, $row, $page_url, $section_index, $dest_id, $root_id, $graph, $image_cache );
			for ( $i = $before; $i < count( $graph ); $i++ ) {
				if ( ! empty( $graph[ $i ]['@type'] ) ) {
					$flat[] = $graph[ $i ];
				}
			}
		}
		return $flat;
	}
}
