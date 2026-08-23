<?php
/**
 * Schema helpers.
 *
 * @package traveliz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'traveliz_schema_clean_text' ) ) {
	/**
	 * Convert HTML/text to a clean single-line string.
	 */
	function traveliz_schema_clean_text( $text ) {
		$value = wp_strip_all_tags( (string) $text, true );
		$value = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$value = preg_replace( '/\s+/u', ' ', $value );
		return trim( (string) $value );
	}
}

if ( ! function_exists( 'traveliz_schema_image_ref' ) ) {
	/**
	 * ImageObject in graph with deduplication (same as ie-flexible-guides).
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $page_url      Canonical page URL.
	 * @param array  $graph         Graph nodes (append).
	 * @param array  $image_cache   Seen @id keys.
	 * @return array|null array( '@id' => ... ) or null.
	 */
	function traveliz_schema_image_ref( $attachment_id, $page_url, array &$graph, array &$image_cache ) {
		$attachment_id = (int) $attachment_id;
		if ( ! $attachment_id ) {
			return null;
		}

		$img_id = $page_url . '#img-' . $attachment_id;
		if ( isset( $image_cache[ $img_id ] ) ) {
			return array( '@id' => $img_id );
		}

		$src = wp_get_attachment_image_url( $attachment_id, 'full' );
		if ( ! $src ) {
			return null;
		}

		$meta   = wp_get_attachment_metadata( $attachment_id );
		$width  = is_array( $meta ) && ! empty( $meta['width'] ) ? (int) $meta['width'] : null;
		$height = is_array( $meta ) && ! empty( $meta['height'] ) ? (int) $meta['height'] : null;

		$node = array(
			'@type' => 'ImageObject',
			'@id'   => $img_id,
			'url'   => esc_url_raw( $src ),
		);

		$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		if ( $alt ) {
			$node['name'] = traveliz_schema_clean_text( $alt );
		}

		$caption = wp_get_attachment_caption( $attachment_id );
		if ( $caption ) {
			$node['caption'] = traveliz_schema_clean_text( $caption );
		}

		if ( $width ) {
			$node['width'] = $width;
		}
		if ( $height ) {
			$node['height'] = $height;
		}

		$image_cache[ $img_id ] = true;
		$graph[]                = $node;

		return array( '@id' => $img_id );
	}
}

if ( ! function_exists( 'traveliz_schema_image_ref_from_array' ) ) {
	/**
	 * Image reference from ACF image array or URL (same pattern as ie-flexible-guides).
	 *
	 * @param mixed  $image_array ACF image field.
	 * @param string $page_url    Canonical page URL.
	 * @param array  $graph       Graph nodes.
	 * @param array  $image_cache Cache.
	 * @return array|null
	 */
	function traveliz_schema_image_ref_from_array( $image_array, $page_url, array &$graph, array &$image_cache ) {
		if ( empty( $image_array ) || ! is_array( $image_array ) ) {
			return null;
		}

		if ( ! empty( $image_array['ID'] ) ) {
			return traveliz_schema_image_ref( (int) $image_array['ID'], $page_url, $graph, $image_cache );
		}

		if ( ! empty( $image_array['url'] ) ) {
			$url_hash = md5( $image_array['url'] );
			$img_id   = $page_url . '#img-' . $url_hash;

			if ( isset( $image_cache[ $img_id ] ) ) {
				return array( '@id' => $img_id );
			}

			$node = array(
				'@type' => 'ImageObject',
				'@id'   => $img_id,
				'url'   => esc_url_raw( $image_array['url'] ),
			);

			if ( ! empty( $image_array['alt'] ) ) {
				$node['name'] = traveliz_schema_clean_text( $image_array['alt'] );
			}

			$image_cache[ $img_id ] = true;
			$graph[]                = $node;

			return array( '@id' => $img_id );
		}

		return null;
	}
}

if ( ! function_exists( 'traveliz_schema_add_property' ) ) {
	/**
	 * Append Schema.org PropertyValue (ie-flexible-guides style).
	 *
	 * @param array  $node  Node by ref.
	 * @param string $name  Property label.
	 * @param mixed  $value Raw value.
	 */
	function traveliz_schema_add_property( array &$node, $name, $value ) {
		$value = traveliz_schema_clean_text( $value );
		if ( $value === '' ) {
			return;
		}
		if ( empty( $node['additionalProperty'] ) ) {
			$node['additionalProperty'] = array();
		}
		$node['additionalProperty'][] = array(
			'@type' => 'PropertyValue',
			'name'  => traveliz_schema_clean_text( $name ),
			'value' => $value,
		);
	}
}

if ( ! function_exists( 'traveliz_schema_make_section' ) ) {
	/**
	 * WebPageElement section shell linked to root CreativeWork.
	 *
	 * @param string $id      Full @id URL.
	 * @param string $name    Section title.
	 * @param string $text    Plain text body.
	 * @param string $root_id Root #content @id.
	 * @return array
	 */
	function traveliz_schema_make_section( $id, $name, $text, $root_id ) {
		$node = array(
			'@type'    => 'WebPageElement',
			'@id'      => $id,
			'name'     => traveliz_schema_clean_text( $name ),
			'isPartOf' => array( '@id' => $root_id ),
		);

		$clean_text = traveliz_schema_clean_text( $text );
		if ( $clean_text ) {
			$node['text'] = $clean_text;
		}

		return $node;
	}
}

if ( ! function_exists( 'traveliz_schema_graph_append_item_list_with_nodes' ) ) {
	/**
	 * Push item entities then ItemList with ListItem → @id (list-main pattern).
	 *
	 * @param string $list_id    Full ItemList @id.
	 * @param string $list_name  Name.
	 * @param array  $item_nodes Full nodes with @id and @type.
	 * @param array  $graph      Graph (by ref).
	 * @param string $description Optional list description.
	 * @return string|null List @id or null.
	 */
	function traveliz_schema_graph_append_item_list_with_nodes( $list_id, $list_name, array $item_nodes, array &$graph, $description = null ) {
		$elements = array();
		$pos      = 0;
		foreach ( $item_nodes as $node ) {
			if ( empty( $node['@id'] ) || empty( $node['@type'] ) ) {
				continue;
			}
			++$pos;
			$graph[]    = $node;
			$elements[] = array(
				'@type'    => 'ListItem',
				'position' => $pos,
				'item'     => array( '@id' => $node['@id'] ),
			);
		}
		if ( empty( $elements ) ) {
			return null;
		}

		$list = array(
			'@type'           => 'ItemList',
			'@id'             => $list_id,
			'name'            => traveliz_schema_clean_text( $list_name ),
			'numberOfItems'   => count( $elements ),
			'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
			'itemListElement' => $elements,
		);

		if ( $description !== null && $description !== '' ) {
			$list['description'] = traveliz_schema_clean_text( $description );
		}

		$graph[] = $list;
		return $list_id;
	}
}

if ( ! function_exists( 'traveliz_schema_build_faq_page' ) ) {
	/**
	 * Build FAQPage schema object (for @graph; no per-node @context).
	 *
	 * @param string      $title     FAQ title.
	 * @param array       $items     Raw FAQ items with question/answer keys.
	 * @param string|null $schema_id Optional stable @id (e.g. page_url#faq-2).
	 * @return array|null
	 */
	function traveliz_schema_build_faq_page( $title, array $items, $schema_id = null ) {
		$entities = array();

		foreach ( $items as $item ) {
			$question = traveliz_schema_clean_text( $item['question'] ?? '' );
			$answer   = traveliz_schema_clean_text( $item['answer'] ?? '' );

			if ( $question === '' || $answer === '' ) {
				continue;
			}

			$entities[] = array(
				'@type' => 'Question',
				'name'  => $question,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}

		if ( empty( $entities ) ) {
			return null;
		}

		$schema_name = traveliz_schema_clean_text( $title );
		if ( $schema_name === '' ) {
			$schema_name = 'FAQ';
		}

		$out = array(
			'@type'      => 'FAQPage',
			'name'       => $schema_name,
			'mainEntity' => $entities,
		);
		if ( $schema_id !== null && $schema_id !== '' ) {
			$out['@id'] = $schema_id;
		}
		return $out;
	}
}

if ( ! function_exists( 'traveliz_schema_video_embed_src' ) ) {
	/**
	 * Extract video URL from ACF embed (URL or iframe HTML), same idea as flexibol/video.php.
	 *
	 * @param mixed $raw
	 * @return string
	 */
	function traveliz_schema_video_embed_src( $raw ) {
		$raw = is_string( $raw ) ? trim( $raw ) : '';
		if ( $raw === '' ) {
			return '';
		}
		if ( stripos( $raw, '<iframe' ) !== false ) {
			if ( preg_match( '/src=["\']([^"\']+)["\']/i', $raw, $m ) ) {
				return $m[1];
			}
			return '';
		}
		return $raw;
	}
}

if ( ! function_exists( 'traveliz_schema_build_item_list' ) ) {
	/**
	 * ItemList with ListItem children (Thing items).
	 *
	 * @param string $name List name.
	 * @param array  $list_items Each: array( 'name' => '', 'description' => '', 'url' => '' ).
	 * @return array|null
	 */
	function traveliz_schema_build_item_list( $name, array $list_items ) {
		$elements = array();
		$pos      = 0;
		foreach ( $list_items as $li ) {
			if ( ! is_array( $li ) ) {
				continue;
			}
			$item_name = traveliz_schema_clean_text( $li['name'] ?? '' );
			$item_desc = traveliz_schema_clean_text( $li['description'] ?? '' );
			$item_url  = isset( $li['url'] ) ? esc_url_raw( (string) $li['url'] ) : '';
			if ( $item_name === '' && $item_desc === '' ) {
				continue;
			}
			++$pos;
			$thing = array(
				'@type' => 'Thing',
				'name'  => $item_name !== '' ? $item_name : 'Item ' . (string) $pos,
			);
			if ( $item_desc !== '' ) {
				$thing['description'] = $item_desc;
			}
			if ( $item_url !== '' ) {
				$thing['url'] = $item_url;
			}
			$elements[] = array(
				'@type'    => 'ListItem',
				'position' => $pos,
				'item'     => $thing,
			);
		}
		if ( empty( $elements ) ) {
			return null;
		}
		$list_name = traveliz_schema_clean_text( $name );
		if ( $list_name === '' ) {
			$list_name = 'List';
		}
		return array(
			'@type'             => 'ItemList',
			'name'              => $list_name,
			'itemListElement'   => $elements,
		);
	}
}

if ( ! function_exists( 'traveliz_schema_build_web_page_element' ) ) {
	/**
	 * WebPageElement with optional headline + text.
	 *
	 * @param string $headline
	 * @param string $text
	 * @return array|null
	 */
	function traveliz_schema_build_web_page_element( $headline, $text ) {
		$h = traveliz_schema_clean_text( $headline );
		$t = traveliz_schema_clean_text( $text );
		if ( $t === '' ) {
			return null;
		}
		$node = array(
			'@type' => 'WebPageElement',
			'text'  => $t,
		);
		if ( $h !== '' ) {
			$node['name'] = $h;
		}
		return $node;
	}
}

if ( ! function_exists( 'traveliz_schema_build_person' ) ) {
	/**
	 * Person node for expert-type blocks.
	 *
	 * @param array $fields Keys: name, job_title, image_url, description.
	 * @return array|null
	 */
	function traveliz_schema_build_person( array $fields ) {
		$name = traveliz_schema_clean_text( $fields['name'] ?? '' );
		if ( $name === '' ) {
			return null;
		}
		$node = array(
			'@type' => 'Person',
			'name'  => $name,
		);
		$job = traveliz_schema_clean_text( $fields['job_title'] ?? '' );
		if ( $job !== '' ) {
			$node['jobTitle'] = $job;
		}
		$img = isset( $fields['image_url'] ) ? esc_url_raw( (string) $fields['image_url'] ) : '';
		if ( $img !== '' ) {
			$node['image'] = $img;
		}
		$desc = traveliz_schema_clean_text( $fields['description'] ?? '' );
		if ( $desc !== '' ) {
			$node['description'] = $desc;
		}
		return $node;
	}
}
