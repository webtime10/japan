<?php
/**
 * 1. REGISTER TAXONOMIES (Regions & Categories)
 */
function traveliz_register_map_taxonomies() {
    $tax_args = array(
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'public'            => false, // Скрываем от фронтенда
        'publicly_queryable' => false,
        'rewrite'           => false,
    );

    // Regions (Кантоны Швейцарии)
    register_taxonomy( 'map_region', array( 'interactive_map' ), array_merge($tax_args, array(
        'labels' => array(
            'name'          => 'Regions',
            'singular_name' => 'Region',
            'add_new_item'  => 'Add New Region'
        )
    )) );

    // Categories (Природа, Культура и т.д.)
    register_taxonomy( 'map_category', array( 'interactive_map' ), array_merge($tax_args, array(
        'labels' => array(
            'name'          => 'Categories',
            'singular_name' => 'Category',
            'add_new_item'  => 'Add New Category'
        )
    )) );
}
add_action( 'init', 'traveliz_register_map_taxonomies' );


/**
 * 2. REGISTER CUSTOM POST TYPE (Interactive Map)
 */
function traveliz_register_map_cpt() {
    register_post_type( 'interactive_map', array(
        'labels'             => array(
            'name'               => 'Interactive Map',
            'singular_name'      => 'Map Object',
            'add_new'            => 'Add New Object',
            'add_new_item'       => 'Add New Map Object',
        ),
        'public'             => false, // Прячем прямые ссылки
        'show_ui'            => true,
        'show_in_menu'       => true,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'has_archive'        => false,
        'query_var'          => false,
        'show_in_rest'       => true, // Для доступа через JSON
        'menu_icon'          => 'dashicons-location-alt',
        'menu_position'      => 25,
        'supports'           => array( 'title', 'thumbnail' ), 
        'taxonomies'         => array( 'map_region', 'map_category' ),
    ) );
}
add_action( 'init', 'traveliz_register_map_cpt' );

/**
 * Register Travelers Stories Custom Post Type
 */
function traveliz_register_travelers_stories_cpt() {
    register_post_type( 'travelers_stories', array(
        'labels'             => array(
            'name'               => 'Travelers Stories',
            'singular_name'      => 'Traveler Story',
            'add_new'            => 'Add New Story',
            'add_new_item'       => 'Add New Traveler Story',
            'edit_item'          => 'Edit Traveler Story',
            'new_item'           => 'New Traveler Story',
            'view_item'          => 'View Traveler Story',
            'search_items'       => 'Search Travelers Stories',
            'not_found'          => 'No travelers stories found',
            'not_found_in_trash' => 'No travelers stories found in Trash',
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'story', 'with_front' => false ),
        'capability_type'     => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-admin-users',
        'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
        'show_in_rest'       => true,
    ) );
}
add_action( 'init', 'traveliz_register_travelers_stories_cpt' );

/**
 * Featured image (post thumbnail) in admin for Travelers Stories.
 */
function traveliz_travelers_stories_thumbnail_support() {
	add_post_type_support( 'travelers_stories', 'thumbnail' );
}
add_action( 'init', 'traveliz_travelers_stories_thumbnail_support', 11 );

/**
 * Flush rewrite rules once after CPT slug/rules change.
 */
function traveliz_maybe_flush_travelers_stories_rewrites() {
    if ( get_option( 'traveliz_travelers_stories_rewrite_ver' ) === '1' ) {
        return;
    }
    flush_rewrite_rules( false );
    update_option( 'traveliz_travelers_stories_rewrite_ver', '1' );
}
add_action( 'init', 'traveliz_maybe_flush_travelers_stories_rewrites', 99 );

/**
 * Fallback: if /story/slug/ is parsed as a Page path, load travelers_stories instead.
 */
function traveliz_fix_travelers_stories_request( $wp ) {
    if ( empty( $wp->query_vars['pagename'] ) || ! empty( $wp->query_vars['travelers_stories'] ) ) {
        return;
    }

    if ( preg_match( '#^story/([^/]+)/?$#', $wp->query_vars['pagename'], $matches ) ) {
        $slug = $matches[1];
        $wp->query_vars['travelers_stories'] = $slug;
        $wp->query_vars['post_type']         = 'travelers_stories';
        $wp->query_vars['name']              = $slug;
        unset( $wp->query_vars['pagename'] );
    }
}
add_action( 'parse_request', 'traveliz_fix_travelers_stories_request', 5 );