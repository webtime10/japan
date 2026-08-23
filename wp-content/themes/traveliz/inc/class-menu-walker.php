<?php
/**
 * Custom Menu Walker with Thumbnails
 *
 * @package traveliz
 */

class Menu_With_Thumbnails_Walker extends Walker_Nav_Menu {

	/**
	 * Start the element output.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )       ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		// Get thumbnail for page
		$thumbnail = '';
		if ( $item->object == 'page' && $item->object_id ) {
			$thumbnail_id = get_post_thumbnail_id( $item->object_id );
			if ( $thumbnail_id ) {
				$thumbnail = get_the_post_thumbnail( $item->object_id, 'thumbnail', array( 'class' => 'menu-item-thumbnail' ) );
			}
		}

		$item_output = isset( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes .'>';
		
		// Text wrapped in span
		$item_output .= '<span class="menu-item-text">' . ( isset( $args->link_before ) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( isset( $args->link_after ) ? $args->link_after : '' ) . '</span>';
		
		// Add thumbnail after link text (on the right)
		if ( $thumbnail ) {
			$item_output .= $thumbnail;
		}
		
		$item_output .= '</a>';
		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
