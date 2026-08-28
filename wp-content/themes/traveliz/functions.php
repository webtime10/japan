<?php
require get_template_directory() . '/inc/laravel-api.php';
require get_template_directory() . '/inc/seasons-pogoda-images.php';
require get_template_directory() . '/inc/acf.php';
require get_template_directory() . '/inc/map.php';
require get_template_directory() . '/inc/schema/helpers.php';
require get_template_directory() . '/inc/schema/faq.php';
require get_template_directory() . '/inc/schema/flexible.php';
require get_template_directory() . '/inc/schema/output.php';
require get_template_directory() . '/inc/page-comments.php';

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.7' );
}



function traveliz_scripts() {
    wp_enqueue_style( 'traveliz-style', get_stylesheet_uri(), array(), _S_VERSION );

    wp_enqueue_style(
        'traveliz-heebo',
        'https://fonts.googleapis.com/css2?family=Heebo:wght@200;300;400;500;600;700;800&display=swap',
        array(),
        null
    );

    // Подключаем встроенный jQuery
    wp_enqueue_script( 'jquery' );
    

    //  стили
    wp_enqueue_style( 'traveliz-front-page-style', get_template_directory_uri() . '/css/style.css', array( 'traveliz-style', 'traveliz-heebo' ), _S_VERSION );
    wp_enqueue_style( 'homeblock', get_template_directory_uri() . '/css/homeblock.css', array( 'traveliz-style' ), _S_VERSION );
    
	wp_enqueue_style( 'smartmenus-blue', get_template_directory_uri() . '/css/sm-blue.css', array(), _S_VERSION );
    wp_enqueue_style( 'cursor-style', get_template_directory_uri() . '/css/cursor.css', array(), _S_VERSION );
    // Cropper.js CSS
    wp_enqueue_style( 'cropper-css', 'https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css', array(), '1.6.1' );
    wp_enqueue_style( 'sl-style', get_template_directory_uri() . '/css/slider.css', array(), _S_VERSION );
	wp_enqueue_style( 'sl-rtll', get_template_directory_uri() . '/css/rtl.css', array(), _S_VERSION );
	wp_enqueue_style( 'sl-ltll', get_template_directory_uri() . '/css/ltr.css', array(), _S_VERSION );
	wp_enqueue_style( 'menu_l', get_template_directory_uri() . '/css/menu_l.css', array(), _S_VERSION );
    wp_enqueue_style( 'media-style', get_template_directory_uri() . '/css/madia.css', array( 'traveliz-front-page-style', 'smartmenus-blue', 'cursor-style' ), _S_VERSION );
 
   
    wp_enqueue_script( 'script-c', get_template_directory_uri() . '/js/script_c.js', array('jquery'), _S_VERSION, true );
    
    // Для твоего основного script.js добавляем зависимость от jquery (array('jquery'))
    wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array('jquery'), _S_VERSION, true );
    wp_enqueue_script( 'script-ap', get_template_directory_uri() . '/js/app.js', array(), _S_VERSION, true );
    // Cropper.js
    wp_enqueue_script( 'cropper-js', 'https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js', array(), '1.6.1', true );
    wp_enqueue_script( 'cursor-script', get_template_directory_uri() . '/js/cursor.js', array('jquery', 'cropper-js'), _S_VERSION, true );
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'traveliz_scripts' );

function enqueue_my_region_styles() {
    // Если это НЕ главная страница
    if ( !is_front_page() ) {
        wp_enqueue_style( 'traveliz-vars', get_template_directory_uri() . '/css/vars.css', array(), _S_VERSION );
        wp_enqueue_style( 'region-style', get_template_directory_uri() . '/css/region.css', array( 'traveliz-vars' ), _S_VERSION );
        wp_enqueue_style( 'reviews', get_template_directory_uri() . '/css/reviews.css');
		wp_enqueue_style( 'fontavesome', get_template_directory_uri() . '/css/fontawesome-all.css');
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_my_region_styles' );

function enqueue_media_custom_last() {
    wp_enqueue_style( 'media-custom', get_template_directory_uri() . '/css/media-custom.css', array(), _S_VERSION );
}
add_action( 'wp_enqueue_scripts', 'enqueue_media_custom_last', 999 );



/*
function traveliz_scripts() {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    // --- CSS БАНДЛ ---
    $css_files = [
        'style.css', 
        'css/style.css',
        'css/sm-blue.css',
        'css/cursor.css',
        'css/slider.css',
        'css/madia.css',
    ];

    $css_bundle_rel = 'css/bundle.min.css';
    $css_bundle_path = $dir . '/' . $css_bundle_rel;
    
    $css_version = _S_VERSION;
    foreach ($css_files as $file) {
        $path = $dir . '/' . $file;
        if (file_exists($path)) {
            $css_version = max($css_version, filemtime($path));
        }
    }

    if (!file_exists($css_bundle_path) || filemtime($css_bundle_path) < $css_version) {
        $css_content = '';
        foreach ($css_files as $file) {
            $path = $dir . '/' . $file;
            if (file_exists($path)) {
                $css_content .= file_get_contents($path) . "\n";
            }
        }
        file_put_contents($css_bundle_path, $css_content);
    }

    // Подключаем БЕЗ функции -rtl
    wp_enqueue_style('itinerary-bundle-css', $uri . '/' . $css_bundle_rel, array(), $css_version);


    // --- JS БАНДЛ (jQuery отдельно) ---
    wp_enqueue_script('jquery');

    $js_files = ['js/smartmenu-pure.js', 'js/script_c.js', 'js/script.js', 'js/app.js'];
    $js_bundle_rel = 'js/bundle.min.js';
    $js_bundle_path = $dir . '/' . $js_bundle_rel;

    $js_version = _S_VERSION;
    foreach ($js_files as $file) {
        if (file_exists($dir . '/' . $file)) {
            $js_version = max($js_version, filemtime($dir . '/' . $file));
        }
    }

    if (!file_exists($js_bundle_path) || filemtime($js_bundle_path) < $js_version) {
        $js_content = '';
        foreach ($js_files as $file) {
            if (file_exists($dir . '/' . $file)) {
                $js_content .= ";" . file_get_contents($dir . '/' . $file) . "\n";
            }
        }
        file_put_contents($js_bundle_path, $js_content);
    }

    wp_enqueue_script('itinerary-bundle-js', $uri . '/' . $js_bundle_rel, array('jquery'), $js_version, true);
}
add_action('wp_enqueue_scripts', 'traveliz_scripts');

*/




/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function traveliz_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on traveliz, use a find and replace
		* to change 'traveliz' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'traveliz', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails', array( 'post', 'page', 'cities', 'travelers_stories' ) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(array(
		'top' => 'Верхнее меню',
		'bottom' => 'Меню футера'
		));

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'traveliz_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'traveliz_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function traveliz_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'traveliz_content_width', 640 );
}
add_action( 'after_setup_theme', 'traveliz_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function traveliz_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'traveliz' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'traveliz' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'traveliz_widgets_init' );

/**
 * Enqueue scripts and styles.
 */

/**
 * Custom Menu Walker with Thumbnails
 */
require get_template_directory() . '/inc/class-menu-walker.php';





/**
 * Disable Gutenberg editor
 */
add_filter( 'use_block_editor_for_post', '__return_false', 10 );
add_filter( 'use_block_editor_for_post_type', '__return_false', 10 );

// Remove Gutenberg styles
add_action( 'wp_enqueue_scripts', function() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-block-style' );
}, 100 );


// логотип
// Регистрируем поддержку логотипа и кастомное поле
add_action( 'after_setup_theme', function() {
    add_theme_support( 'custom-logo' );
} );

add_action( 'customize_register', function( $wp_customize ) {
    // Добавляем настройку для Hatch White
    $wp_customize->add_setting( 'hatch_white_logo' );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hatch_white_logo', [
        'label'    => 'Логотип Hatch White',
        'section'  => 'title_tagline', // Появится в "Свойства сайта"
        'settings' => 'hatch_white_logo',
    ] ) );
} );

// ... твой предыдущий код (enqueue_scripts и т.д.)















/**
 * 3. ADMIN FILTERS
 */
add_action( 'restrict_manage_posts', 'traveliz_add_map_filters' );
function traveliz_add_map_filters() {
    global $typenow;
    if ( $typenow == 'interactive_map' ) {
        $taxonomies = array( 'map_region', 'map_category' );
        foreach ( $taxonomies as $tax_slug ) {
            $tax_obj = get_taxonomy( $tax_slug );
            $selected = isset( $_GET[$tax_slug] ) ? $_GET[$tax_slug] : '';
            wp_dropdown_categories( array(
                'show_option_all' => 'All ' . $tax_obj->label,
                'taxonomy'        => $tax_slug,
                'name'            => $tax_slug,
                'orderby'         => 'name',
                'selected'        => $selected,
                'show_count'      => true,
                'hide_empty'      => false,
                'value_field'     => 'slug',
            ) );
        }
    }
}

/**
 * 4. ПОДКЛЮЧАЕМ ПОДДЕРЖКУ POLYLANG (Фикс для пустой страницы настроек)
 */
add_filter( 'pll_get_post_types', 'traveliz_map_polylang_support' );
function traveliz_map_polylang_support( $post_types ) {
    $post_types['interactive_map']     = 'interactive_map';
    $post_types['travelers_stories'] = 'travelers_stories';
    return $post_types;
}

add_filter( 'pll_get_taxonomies', 'traveliz_map_tax_polylang_support' );
function traveliz_map_tax_polylang_support( $taxonomies ) {
    $taxonomies['map_region'] = 'map_region';
    $taxonomies['map_category'] = 'map_category';
    return $taxonomies;
}


// Принудительная синхронизация таксономий для Polylang
// 1. Убираем поддержку Polylang для категорий и регионов карты
add_filter( 'pll_get_taxonomies', function( $taxonomies ) {
    unset( $taxonomies['map_region'] );
    unset( $taxonomies['map_category'] );
    return $taxonomies;
}, 20 );

// 2. Регистрируем названия категорий для перевода через String Translation
add_action('admin_init', function() {
    if (function_exists('pll_register_string')) {
        $categories = get_terms(['taxonomy' => 'map_category', 'hide_empty' => false]);
        foreach ($categories as $cat) {
            pll_register_string('Map Category: ' . $cat->name, $cat->name, 'Interactive Map');
        }
    }
});



/**
 * 5. ПРИНУДИТЕЛЬНЫЙ ВЫБОР ТОЛЬКО ОДНОЙ КАТЕГОРИИ И РЕГИОНА
 */
add_action('admin_footer', function() {
    global $typenow;
    // Применяем только для нашего типа поста
    if ( $typenow == 'interactive_map' ) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Идентификаторы стандартных метабоксов твоих таксономий
                // Формируются как #taxonomy-{slug}
                var taxonomyIds = ['#taxonomy-map_category', '#taxonomy-map_region'];

                $.each(taxonomyIds, function(i, id) {
                    var $container = $(id);

                    // 1. Визуально превращаем чекбоксы в радиокнопки
                    $container.find('input[type="checkbox"]').each(function() {
                        $(this).attr('type', 'radio');
                    });

                    // 2. Логика переключения: если кликнули по одному, снимаем выбор с остальных
                    $container.on('click', 'input[type="radio"]', function() {
                        var isChecked = $(this).prop('checked');
                        $container.find('input[type="radio"]').prop('checked', false);
                        $(this).prop('checked', isChecked);
                    });
                });
            });
        </script>

        <?php
    }
});
/* такасы */
/* */
add_action('admin_head', 'custom_acf_accordion_color');

function custom_acf_accordion_color() {
    echo '<style>
        /* Обращаемся конкретно к твоему аккордеону по data-key */
        .acf-field[data-key="field_69961ada0d291"] .acf-accordion-title {
            background-color: #1e73be !important; /* Синий как на скрине */
            color: #ffffff !important;
            border-bottom: 1px solid #155a92 !important;
        }

        /* Белая стрелочка */
        .acf-field[data-key="field_69961ada0d291"] .acf-accordion-icon {
            color: #ffffff !important;
        }

        /* Цвет при наведении */
        .acf-field[data-key="field_69961ada0d291"] .acf-accordion-title:hover {
            background-color: #155a92 !important;
        }
    </style>';
}

/* */

/* полиланг код для того что задавать ссылки нормальные */


function polylang_slug_unique_slug_in_language( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ){

	// Return slug if it was not changed.
	if ( $original_slug === $slug ) {
		return $slug;
	}

	if ( ! function_exists( 'pll_get_post_language' ) || ! function_exists( 'pll_is_translated_post_type' ) ) {
		return $slug;
	}

	global $wpdb;

	// Get language of a post
	$lang = pll_get_post_language( $post_ID );
	$options = get_option( 'polylang' );

	// return the slug if Polylang does not return post language or has incompatable redirect setting or is not translated post type.
	if ( empty( $lang ) || ! is_array( $options ) || 0 === (int) ( $options['force_lang'] ?? 0 ) || ! pll_is_translated_post_type( $post_type ) ) {
		return $slug;
	}

	// " INNER JOIN $wpdb->term_relationships AS pll_tr ON pll_tr.object_id = ID".
	$join_clause  = polylang_slug_model_post_join_clause();
	// " AND pll_tr.term_taxonomy_id IN (" . implode(',', $languages) . ")".
	$where_clause = polylang_slug_model_post_where_clause( $lang );

	// Polylang does not translate attachements - skip if it is one.
	// @TODO Recheck this with the Polylang settings
	if ( 'attachment' == $post_type ) {

		// Attachment slugs must be unique across all types.
		$check_sql = "SELECT post_name FROM $wpdb->posts $join_clause WHERE post_name = %s AND ID != %d $where_clause LIMIT 1";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $original_slug, $post_ID ) );

	} elseif ( is_post_type_hierarchical( $post_type ) ) {

		// Page slugs must be unique within their own trees. Pages are in a separate
		// namespace than posts so page slugs are allowed to overlap post slugs.
		$check_sql = "SELECT ID FROM $wpdb->posts $join_clause WHERE post_name = %s AND post_type IN ( %s, 'attachment' ) AND ID != %d AND post_parent = %d $where_clause LIMIT 1";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $original_slug, $post_type, $post_ID, $post_parent ) );

	} else {

		// Post slugs must be unique across all posts.
		$check_sql = "SELECT post_name FROM $wpdb->posts $join_clause WHERE post_name = %s AND post_type = %s AND ID != %d $where_clause LIMIT 1";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $original_slug, $post_type, $post_ID ) );

	}

	if ( ! $post_name_check ) {
		return $original_slug;
	}

	return $slug;
}
add_filter( 'wp_unique_post_slug', 'polylang_slug_unique_slug_in_language', 10, 6 );

/**
 * Modify the sql query to include checks for the current language.
 *
 * @since 0.1.0
 *
 * @global wpdb   $wpdb  WordPress database abstraction object.
 *
 * @param  string $query Database query.
 *
 * @return string        The modified query.
 */
function polylang_slug_filter_queries( $query ) {
	global $wpdb;

	// Query for posts page, pages, attachments and hierarchical CPT. This is the only possible place to make the change. The SQL query is set in get_page_by_path()
	$is_pages_sql = preg_match(
		"#SELECT ID, post_name, post_parent, post_type FROM {$wpdb->posts} .*#",
		polylang_slug_standardize_query( $query ),
		$matches
	);

	if ( ! $is_pages_sql ) {
		return $query;
	}

	// Check if should contine. Don't add $query polylang_slug_should_run() as $query is a SQL query.
	if ( ! polylang_slug_should_run() ) {
		return $query;
	}

	$lang = pll_current_language();
	// " INNER JOIN $wpdb->term_relationships AS pll_tr ON pll_tr.object_id = ID".
	$join_clause  = polylang_slug_model_post_join_clause();
	// " AND pll_tr.term_taxonomy_id IN (" . implode(',', $languages) . ")".
	$where_clause = polylang_slug_model_post_where_clause( $lang );

	$query = preg_match(
		"#(SELECT .* (?=FROM))(FROM .* (?=WHERE))(?:(WHERE .*(?=ORDER))|(WHERE .*$))(.*)#",
		polylang_slug_standardize_query( $query ),
		$matches
	);

	// Reindex array numerically $matches[3] and $matches[4] are not added together thus leaving a gap. With this $matches[5] moves up to $matches[4]
	$matches = array_values( $matches );

	// SELECT, FROM, INNER JOIN, WHERE, WHERE CLAUSE (additional), ORBER BY (if included)
	$sql_query = $matches[1] . $matches[2] . $join_clause . $matches[3] . $where_clause . $matches[4];

	/**
	 * Disable front end query modification.
	 *
	 * Allows disabling front end query modification if not needed.
	 *
	 * @since 0.2.0
	 *
	 * @param string $sql_query    Database query.
	 * @param array  $matches {
	 *     @type string $matches[1] SELECT SQL Query.
	 *     @type string $matches[2] FROM SQL Query.
	 *     @type string $matches[3] WHERE SQL Query.
	 *     @type string $matches[4] End of SQL Query (Possibly ORDER BY).
	 * }
	 * @param string $join_clause  INNER JOIN Polylang clause.
	 * @param string $where_clause Additional Polylang WHERE clause.
	 */
	return apply_filters( 'polylang_slug_sql_query', $sql_query, $matches, $join_clause, $where_clause );
}
add_filter( 'query', 'polylang_slug_filter_queries' );

/**
 * Extend the WHERE clause of the query.
 *
 * This allows the query to return only the posts of the current language
 *
 * @since 0.1.0
 *
 * @param  string   $where The WHERE clause of the query.
 * @param  WP_Query $query The WP_Query instance (passed by reference).
 *
 * @return string          The WHERE clause of the query.
 */
function polylang_slug_posts_where_filter( $where, $query ) {
	// Check if should contine.
	if ( ! polylang_slug_should_run( $query ) ) {
		return $where;
	}

	$lang = empty( $query->query['lang'] ) ? pll_current_language() : $query->query['lang'];

	// " AND pll_tr.term_taxonomy_id IN (" . implode(',', $languages) . ")"
	$where .= polylang_slug_model_post_where_clause( $lang  );

	return $where;
}
add_filter( 'posts_where', 'polylang_slug_posts_where_filter', 10, 2 );

/**
 * Extend the JOIN clause of the query.
 *
 * This allows the query to return only the posts of the current language
 *
 * @since 0.1.0
 *
 * @param  string   $join  The JOIN clause of the query.
 * @param  WP_Query $query The WP_Query instance (passed by reference).
 *
 * @return string          The JOIN clause of the query.
 */
function polylang_slug_posts_join_filter( $join, $query ) {

	// Check if should contine.
	if ( ! polylang_slug_should_run( $query ) ) {
		return $join;
	}

	// " INNER JOIN $wpdb->term_relationships AS pll_tr ON pll_tr.object_id = ID".
	$join .= polylang_slug_model_post_join_clause();

	return $join;
}
add_filter( 'posts_join', 'polylang_slug_posts_join_filter', 10, 2 );

/**
 * Check if the query needs to be adapted.
 *
 * @since 0.2.0
 *
 * @param  WP_Query $query The WP_Query instance (passed by reference).
 *
 * @return bool
 */
function polylang_slug_should_run( $query = '' ) {

	/**
	 * Disable front end query modification.
	 *
	 * Allows disabling front end query modification if not needed.
	 *
	 * @since 0.2.0
	 *
	 * @param bool     false  Not disabling run.
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 */

	// Do not run in admin or if Polylang is disabled
	$disable = apply_filters( 'polylang_slug_disable', false, $query );
	if ( is_admin() || is_feed() || ! function_exists( 'pll_current_language' ) || $disable ) {
		return false;
	}
	// The lang query should be defined if the URL contains the language
	$lang          = empty( $query->query['lang'] ) ? pll_current_language() : $query->query['lang'];
	// Checks if the post type is translated when doing a custom query with the post type defined
	$is_translated = ! empty( $query->query['post_type'] ) && ! pll_is_translated_post_type( $query->query['post_type'] );

	return ! ( empty( $lang ) || $is_translated );
}

/**
 * Standardize the query.
 *
 * This makes the standardized and simpler to run regex on
 *
 * @since 0.2.0
 *
 * @param  string $query Database query.
 *
 * @return string        The standardized query.
 */
function polylang_slug_standardize_query( $query ) {
	// Strip tabs, newlines and multiple spaces.
	$query = str_replace(
		array( "\t", " \n", "\n", " \r", "\r", "   ", "  " ),
		array( '', ' ', ' ', ' ', ' ', ' ', ' ' ),
		$query
	);
	return trim( $query );
}

/**
 * Fetch the polylang join clause.
 *
 * @since 0.2.0
 *
 * @return string
 */
function polylang_slug_model_post_join_clause() {
	if ( function_exists( 'PLL' ) ) {
		return PLL()->model->post->join_clause();
	} elseif ( array_key_exists( 'polylang', $GLOBALS ) ) {
		global $polylang;
		return $polylang->model->join_clause( 'post' );
	}
	return '';
}

/**
 * Fetch the polylang where clause.
 *
 * @since 0.2.0
 *
 * @param  string $lang The current language slug.
 *
 * @return string
 */
function polylang_slug_model_post_where_clause( $lang = '' ) {
	if ( function_exists( 'PLL' ) ) {
		return PLL()->model->post->where_clause( $lang );
	} elseif ( array_key_exists( 'polylang', $GLOBALS ) ) {
		global $polylang;
		return $polylang->model->where_clause( $lang, 'post' );
	}
	return '';
}

/**
 * Slug языка по умолчанию. Для одноязычной арабской версии используем ar.
 */
function traveliz_pll_default_slug(): string
{
    if (function_exists('pll_default_language')) {
        $def = pll_default_language('slug');
        if (is_string($def) && $def !== '') {
            return $def;
        }
    }

    return 'ar';
}

/**
 * Текущий язык (Polylang) или дефолт сайта.
 */
function traveliz_pll_current_slug(): string
{
    if (function_exists('pll_current_language')) {
        $cur = pll_current_language();
        if (is_string($cur) && $cur !== '') {
            return $cur;
        }
    }

    return traveliz_pll_default_slug();
}

/**
 * URL логотипа для текущего (или переданного) языка Polylang.
 * Файлы: img/logo_{slug}.png|jpg (he, en, ar …).
 *
 * @param string $lang Slug языка (he, en, ar …). Пусто — текущий язык.
 */
function traveliz_get_lang_logo_url( string $lang = '' ): string {
    if ( $lang === '' ) {
        $lang = traveliz_pll_current_slug();
    }

    $img_dir = get_template_directory() . '/img/';
    $img_uri = get_template_directory_uri() . '/img/';

    $candidates = array(
        'logo_' . $lang . '.png',
        'logo_' . $lang . '.jpg',
        'logo_' . $lang . '.webp',
        'logo_ar.jpg',
        'logo_ar.png',
        'logo_en.jpg',
        'logo_en.png',
    );

    foreach ( $candidates as $file ) {
        if ( is_readable( $img_dir . $file ) ) {
            return $img_uri . $file;
        }
    }

    $hatch = get_theme_mod( 'hatch_white_logo' );
    if ( $hatch ) {
        return (string) $hatch;
    }

    $logo_id = get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
        $logo = wp_get_attachment_image_src( (int) $logo_id, 'full' );
        if ( $logo && ! empty( $logo[0] ) ) {
            return (string) $logo[0];
        }
    }

    if ( is_readable( $img_dir . 'logo.svg' ) ) {
        return $img_uri . 'logo.svg';
    }

    return $img_uri . 'logo_ar.jpg';
}

/**
 * URL второго логотипа (logo2) для текущего или переданного языка Polylang.
 * Файлы: img/logo2_{slug}.svg|png|webp (he, en, ar …).
 *
 * @param string $lang Slug языка (he, en, ar …). Пусто — текущий язык.
 */
function traveliz_get_lang_logo2_url( string $lang = '' ): string {
    if ( $lang === '' ) {
        $lang = traveliz_pll_current_slug();
    }

    $img_dir = get_template_directory() . '/img/';
    $img_uri = get_template_directory_uri() . '/img/';

    $candidates = array(
        'logo2_' . $lang . '.svg',
        'logo2_' . $lang . '.png',
        'logo2_' . $lang . '.webp',
        'logo2_en.svg',
        'logo2_en.png',
    );

    foreach ( $candidates as $file ) {
        if ( is_readable( $img_dir . $file ) ) {
            return $img_uri . $file;
        }
    }

    // Всегда logo2_* — не подменять на logo_* из шапки/футера.
    return $img_uri . 'logo2_' . $lang . '.svg';
}

/**
 * RTL для UI: флаг is_rtl языка в Polylang (Languages → язык → RTL).
 *
 * @param string $lang Slug языка. Пусто — текущий язык Polylang.
 */
function traveliz_pll_is_rtl( string $lang = '' ): bool {
    if ( $lang === '' ) {
        $lang = traveliz_pll_current_slug();
    }

    if ( function_exists( 'PLL' ) ) {
        $pll = PLL();

        if ( $lang !== '' && isset( $pll->model ) && is_object( $pll->model ) && method_exists( $pll->model, 'get_language' ) ) {
            $lang_obj = $pll->model->get_language( $lang );
            if ( $lang_obj && isset( $lang_obj->is_rtl ) ) {
                return (bool) $lang_obj->is_rtl;
            }
        }

        if ( isset( $pll->curlang ) && is_object( $pll->curlang ) && isset( $pll->curlang->is_rtl ) ) {
            return (bool) $pll->curlang->is_rtl;
        }
    }

    if ( in_array( $lang, array( 'ar', 'he', 'fa', 'ur' ), true ) ) {
        return true;
    }

    return is_rtl();
}

/**
 * ID формы CF7: сначала $lang, затем дефолтный язык Polylang, затем первый ключ из $forms.
 *
 * @param array<string, string> $forms slug => cf7 short id
 */
function traveliz_resolve_cf7_form_id(array $forms, string $lang): string
{
    if (isset($forms[$lang])) {
        return (string) $forms[$lang];
    }
    $def = traveliz_pll_default_slug();
    if (isset($forms[$def])) {
        return (string) $forms[$def];
    }
    $first = reset($forms);

    return is_string($first) ? $first : '';
}


/* мультиязычность */

/**
 * Slug файла переводов themes/languages-data (ar → he на japan.loc).
 */
function traveliz_language_data_slug( string $lang = '' ): string {
	if ( $lang === '' ) {
		$lang = traveliz_pll_current_slug();
	}

	return ( 'ar' === $lang ) ? 'he' : $lang;
}

/**
 * Polylang slug ar на этом сайте — UI на иврите.
 */
add_filter(
	'locale',
	static function ( $locale ) {
		if ( function_exists( 'pll_current_language' ) && 'ar' === pll_current_language( 'slug' ) ) {
			return 'he_IL';
		}
		return $locale;
	}
);

function get_theme_translation($key) {
    // Текущий язык Polylang (slug из languages-data/*.php)
    $current_lang = traveliz_pll_current_slug();
    $file_lang    = traveliz_language_data_slug( $current_lang );

    // Путь к файлу с переводами
    $file_path = get_template_directory() . "/languages-data/{$file_lang}.php";

    // Загружаем массив из файла (статическая переменная, чтобы не грузить диск дважды)
    static $translations = [];
    static $loaded_lang = '';

    if ($loaded_lang !== $file_lang || empty($translations)) {
        $translations = [];
        $loaded_lang = $file_lang;

        if (file_exists($file_path)) {
            $translations = include $file_path;
        } else {
            $fallback_path = get_template_directory() . '/languages-data/en.php';
            if (file_exists($fallback_path)) {
                $translations = include $fallback_path;
            }
        }
    }

    return isset($translations[$key]) ? $translations[$key] : $key;
}

// Регистрация строк для Polylang (чтобы он видел их в базе, если нужно)
add_action('init', function() {
    if (function_exists('pll_register_string')) {
        // Берем основной файл (например, ua.php) как эталон ключей
        $default_file = get_template_directory() . "/languages-data/ua.php";
        if (file_exists($default_file)) {
            $keys = include $default_file;
            foreach ($keys as $key => $value) {
                pll_register_string($key, $key, 'Theme-Files');
            }
        }
    }
});

/* мультиязычность */

// AJAX обработчики загрузки/сохранения отзывов теперь полностью
// реализованы в плагине wt-reviews (см. WT_Reviews::handle_upload_feedback_image
// и WT_Reviews::handle_submit_feedback_form).
// Старые хуки темы отключены, чтобы данные шли только в админку плагина.
// add_action('wp_ajax_upload_feedback_image', 'handle_upload_feedback_image');
// add_action('wp_ajax_nopriv_upload_feedback_image', 'handle_upload_feedback_image');

function handle_upload_feedback_image() {
	// Проверка nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'feedback_upload_nonce')) {
		wp_send_json_error(array('message' => 'Ошибка безопасности'));
		return;
	}
	
	// Проверка наличия файла
	if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
		wp_send_json_error(array('message' => 'Ошибка загрузки файла'));
		return;
	}
	
	$file = $_FILES['image'];
	
	// Проверка типа файла
	$allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
	if (!in_array($file['type'], $allowed_types)) {
		wp_send_json_error(array('message' => 'Недопустимый тип файла'));
		return;
	}
	
	// Путь к папке uploads в корне сайта
	$upload_dir = ABSPATH . 'uploads/';
	
	// Создаем папку если не существует
	if (!file_exists($upload_dir)) {
		wp_mkdir_p($upload_dir);
	}
	
	// Генерируем уникальное имя файла
	$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
	$file_name = 'feedback_' . time() . '_' . wp_generate_password(8, false) . '.' . $file_extension;
	$target_file = $upload_dir . $file_name;
	
	// Перемещаем файл
	if (move_uploaded_file($file['tmp_name'], $target_file)) {
		// Возвращаем URL файла
		$file_url = site_url() . '/uploads/' . $file_name;
		wp_send_json_success(array(
			'url' => $file_url,
			'filename' => $file_name
		));
	} else {
		wp_send_json_error(array('message' => 'Ошибка при сохранении файла'));
	}
}

// add_action('wp_ajax_submit_feedback_form', 'handle_submit_feedback_form');
// add_action('wp_ajax_nopriv_submit_feedback_form', 'handle_submit_feedback_form');

function handle_submit_feedback_form() {
	// Проверка nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'feedback_form_nonce')) {
		wp_send_json_error(array('message' => 'Ошибка безопасности'));
		return;
	}
	
	global $wpdb;
	$table_name = 'news';
	
	// Получаем данные из формы
	$name = isset($_POST['name']) ? trim(sanitize_text_field($_POST['name'])) : '';
	$link = isset($_POST['link']) ? esc_url_raw($_POST['link']) : '';
	$text = isset($_POST['text']) ? trim(wp_kses_post($_POST['text'])) : '';
	$photo_filename = isset($_POST['photo_filename']) ? sanitize_text_field($_POST['photo_filename']) : '';
	$is_english = isset($_POST['is_english']) && $_POST['is_english'] == '1';

	// Строгая валидация имени - обязательное поле
	if (empty($name) || strlen($name) < 2) {
		$error_msg = get_theme_translation('feedback_error_name');
		wp_send_json_error(array('message' => $error_msg));
		return;
	}
	
	// Проверка на валидные символы в имени (буквы, пробелы, дефисы, апострофы, точки)
	if (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-'."'".'\.]+$/u', $name)) {
		$error_msg = get_theme_translation('feedback_error_invalid_name');
		wp_send_json_error(array('message' => $error_msg));
		return;
	}
	
	// Валидация текста отзыва
	if (empty($text) || strlen($text) < 10) {
		$error_msg = get_theme_translation('feedback_error_text');
		wp_send_json_error(array('message' => $error_msg));
		return;
	}
	
	// Подготавливаем данные для вставки
	if ($is_english) {
		// Английская версия
		$data = array(
			'name' => '',
			'name_en' => $name,
			'anons' => $text,
			'text' => '',
			'link_en' => $link,
			'link_ru' => '',
			'reiting' => $photo_filename,
			'keywords' => '0', // Не опубликовано по умолчанию
			'date' => current_time('Y-m-d'),
		);
	} else {
		// Русская версия
		$data = array(
			'name' => $name,
			'name_en' => '',
			'text' => $text,
			'anons' => '',
			'link_ru' => $link,
			'link_en' => '',
			'reiting' => $photo_filename,
			'keywords' => '0', // Не опубликовано по умолчанию
			'date' => current_time('Y-m-d'),
		);
	}
	
	// Вставляем в базу данных
	$formats = array_fill(0, count($data), '%s');
	$result = $wpdb->insert($table_name, $data, $formats);
	
	if ($result === false) {
		wp_send_json_error(array('message' => 'Ошибка при сохранении в базу данных: ' . $wpdb->last_error));
		return;
	}
	
	// Проверяем, что имя действительно сохранено
	$saved_name = $is_english ? $data['name_en'] : $data['name'];
	
	if (!empty($saved_name) && strlen(trim($saved_name)) > 0) {
		// Формируем сообщение об успехе с переводом
		$success_msg = get_theme_translation('feedback_success');
		$success_msg = str_replace('{name}', esc_html($saved_name), $success_msg);
		
		wp_send_json_success(array(
			'message' => $success_msg,
			'review_id' => $wpdb->insert_id,
			'saved_name' => $saved_name
		));
	} else {
		$error_msg = get_theme_translation('feedback_error_name');
		wp_send_json_error(array(
			'message' => $error_msg
		));
	}
}

// add_action('wp_enqueue_scripts', 'traveliz_feedback_scripts', 20);
function traveliz_feedback_scripts() {
	// Только на странице reviews
	if (is_page_template('page-reviews.php') || (isset($_SERVER["REQUEST_URI"]) && strpos($_SERVER["REQUEST_URI"], 'reviews') !== false)) {
		// Получаем переводы для текущего языка
		$translations = array(
			'error_name' => get_theme_translation('feedback_error_name'),
			'error_text' => get_theme_translation('feedback_error_text'),
			'error_invalid_name' => get_theme_translation('feedback_error_invalid_name'),
			'uploading' => get_theme_translation('feedback_uploading'),
			'sending' => get_theme_translation('feedback_sending'),
			'success_prefix' => str_replace('{name}', '', get_theme_translation('feedback_success')),
			'success_suffix' => '',
		);
		
		// Обрабатываем сообщение об успехе
		$success_msg = get_theme_translation('feedback_success');
		if (strpos($success_msg, '{name}') !== false) {
			$parts = explode('{name}', $success_msg);
			$translations['success_prefix'] = $parts[0];
			$translations['success_suffix'] = isset($parts[1]) ? $parts[1] : '';
		} else {
			$translations['success_prefix'] = $success_msg;
		}
		
		wp_localize_script('cursor-script', 'feedbackAjax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('feedback_upload_nonce'),
			'form_nonce' => wp_create_nonce('feedback_form_nonce'),
			'translations' => $translations
		));
	}
}


/**
 * URL или iframe из ACF → src для iframe.
 *
 * @param mixed $raw
 * @return string
 */
function traveliz_video_embed_to_src( $raw ) {
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

/**
 * YouTube / Vimeo / Shorts → embed URL для iframe.
 *
 * @param string $url
 * @return string
 */
function traveliz_youtube_embed_url( $url ) {
	$url = trim( (string) $url );
	if ( $url === '' ) {
		return '';
	}
	$url = traveliz_video_embed_to_src( $url );

	if ( preg_match( '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
		return 'https://www.youtube.com/embed/' . $m[1];
	}
	if ( preg_match( '/player\.vimeo\.com\//', $url ) ) {
		return esc_url_raw( $url );
	}
	if ( preg_match( '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
		return 'https://www.youtube.com/embed/' . $m[1];
	}
	if ( preg_match( '/(?:youtube\.com\/watch\?[^#]*v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
		return 'https://www.youtube.com/embed/' . $m[1];
	}

	return esc_url_raw( $url );
}

/**
 * YouTube ID (11 chars) from watch / youtu.be / embed / shorts URL.
 *
 * @param string $url
 * @return string
 */
function traveliz_youtube_video_id_from_url( $url ) {
	$embed = traveliz_youtube_embed_url( $url );
	if ( $embed !== '' && preg_match( '/embed\/([a-zA-Z0-9_-]{11})/', $embed, $m ) ) {
		return $m[1];
	}

	return '';
}

/** @deprecated Use traveliz_youtube_embed_url() */
function traveliz_reviews_page_youtube_embed_url( $url ) {
	return traveliz_youtube_embed_url( $url );
}

/**
 * Shorts link has priority over regular video link.
 *
 * @param mixed $short_raw
 * @param mixed $regular_raw
 * @return array{src: string, modal: string}
 */
function traveliz_resolve_video_embed( $short_raw, $regular_raw ) {
	if ( is_string( $short_raw ) && trim( $short_raw ) !== '' ) {
		$src = traveliz_youtube_embed_url( $short_raw );
		if ( $src !== '' ) {
			return array(
				'src'   => $src,
				'modal' => 'short',
			);
		}
	}
	if ( is_string( $regular_raw ) && trim( $regular_raw ) !== '' ) {
		$src = traveliz_youtube_embed_url( $regular_raw );
		if ( $src !== '' ) {
			return array(
				'src'   => $src,
				'modal' => 'default',
			);
		}
	}
	return array(
		'src'   => '',
		'modal' => '',
	);
}

/**
 * Wrap Hebrew/Arabic runs in <bdi> for mixed LTR temperature strings.
 * Example: ≈ 23°C ביום / 16°C בלילה
 *          ≈ 23°م نهاراً / 16°م ليلاً
 *
 * @param string $text
 * @return string
 */
function traveliz_isolate_bidi_temp_subtitle( $text ) {
	if ( ! is_string( $text ) || '' === $text ) {
		return '';
	}

	if ( false !== stripos( $text, '<bdi' ) ) {
		return $text;
	}

	$pattern = '/(?:\p{Script=Hebrew}|\p{Script=Arabic})+/u';

	return preg_replace_callback(
		$pattern,
		static function ( $matches ) {
			return '<bdi>' . $matches[0] . '</bdi>';
		},
		$text
	);
}

/**
 * LTR-isolated price amount (e.g. 70–105 ₪).
 *
 * @param string $text
 * @return string
 */
function traveliz_price_amount_html( $text ) {
	if ( ! is_string( $text ) || '' === trim( $text ) ) {
		return '';
	}

	return '<bdi class="price-amount-ltr">' . esc_html( $text ) . '</bdi>';
}

/**
 * RTL-isolated price period label (e.g. לאדם / יום).
 *
 * @param string $text
 * @return string
 */
function traveliz_price_period_html( $text ) {
	if ( ! is_string( $text ) || '' === trim( $text ) ) {
		return '';
	}

	$inner = traveliz_isolate_bidi_temp_subtitle( $text );

	return '<bdi class="price-period-rtl">' . wp_kses( $inner, array( 'bdi' => array() ) ) . '</bdi>';
}

/**
 * LTR-isolated secondary price range (e.g. ¥50,000–53,000).
 *
 * @param string $text
 * @return string
 */
function traveliz_price_period_ltr_html( $text ) {
	if ( ! is_string( $text ) || '' === trim( $text ) ) {
		return '';
	}

	return '<bdi class="price-period-ltr">' . esc_html( $text ) . '</bdi>';
}

/**
 * Split mixed hotel title into local (RTL) + English in parentheses (LTR).
 *
 * @param string $title
 * @return string
 */
function traveliz_hotel_card_title_html( $title ) {
	$title = trim( (string) $title );
	if ( '' === $title ) {
		return '';
	}

	if ( preg_match( '/hotel-title-en|<bdi/i', $title ) ) {
		return wp_kses_post( $title );
	}

	$paren_pos = mb_strpos( $title, '(', 0, 'UTF-8' );
	if ( false !== $paren_pos ) {
		$local = trim( mb_substr( $title, 0, $paren_pos, 'UTF-8' ) );
		$latin = trim( mb_substr( $title, $paren_pos, null, 'UTF-8' ) );

		if ( '' !== $latin && preg_match( '/[A-Za-z]/', $latin ) ) {
			$html = '';
			if ( '' !== $local ) {
				$html .= '<span class="hotel-title-he">' . esc_html( $local ) . '</span> ';
			}
			$html .= '<bdi class="hotel-title-en">' . esc_html( $latin ) . '</bdi>';

			return $html;
		}
	}

	if ( preg_match( '/[A-Za-z]/', $title ) && ! preg_match( '/\p{Script=Hebrew}|\p{Script=Arabic}/u', $title ) ) {
		return '<bdi class="hotel-title-en">' . esc_html( $title ) . '</bdi>';
	}

	return esc_html( $title );
}

/**
 * Allowed tags for hotel card titles.
 *
 * @return array<string, array<string, bool>>
 */
function traveliz_hotel_card_title_allowed_html() {
	return array(
		'span' => array( 'class' => true ),
		'bdi'  => array( 'class' => true ),
	);
}

// Shortcodes
require_once get_template_directory() . '/shortcode/reviews.php';
require_once get_template_directory() . '/shortcode/web_expert.php';
require_once get_template_directory() . '/shortcode/what_we_offer.php';


