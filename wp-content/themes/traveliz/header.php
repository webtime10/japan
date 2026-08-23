<!doctype html>
<?php 
  $current_lang      = function_exists( 'traveliz_pll_current_slug' ) ? traveliz_pll_current_slug() : 'ar';
  $current_direction = function_exists( 'traveliz_pll_is_rtl' ) && traveliz_pll_is_rtl( $current_lang ) ? 'rtl' : 'ltr';
?>
<html lang="<?php echo esc_attr( $current_lang ); ?>" dir="<?php echo esc_attr( $current_direction ); ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100..900&display=swap" rel="stylesheet">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php wp_head(); ?>
</head>
<?php if (str_contains($_SERVER['REQUEST_URI'], '/ar/')): ?>
    <style>
        html[dir="rtl"] .logo2 img {
    position: relative;
    top: -3px;
}

    </style>
<?php endif; ?>
<?php if (str_contains($_SERVER['REQUEST_URI'], '/en/')): ?>
    <style>
      .logo2 img {
        position: relative;
        top: -3px;
}

    </style>
<?php endif; ?>
<body>
<?php wp_body_open(); ?>
<?php 
// Этот код гарантированно берет ID той страницы, которая открыта в браузере
$current_id = get_queried_object_id(); 

// Берем картинку именно для этой страницы по её ID (у каждой страницы своё значение в meta).
$img_main       = get_field( 'header_background', $current_id );
$header_bg_url  = '';
if ( $img_main ) {
	if ( is_array( $img_main ) && ! empty( $img_main['url'] ) ) {
		$header_bg_url = $img_main['url'];
	} elseif ( is_numeric( $img_main ) ) {
		$img_src = wp_get_attachment_image_src( (int) $img_main, 'full' );
		if ( $img_src && ! empty( $img_src[0] ) ) {
			$header_bg_url = $img_src[0];
		}
	} elseif ( is_string( $img_main ) ) {
		$header_bg_url = $img_main;
	}
}

$search_lang      = function_exists( 'traveliz_pll_current_slug' ) ? traveliz_pll_current_slug() : '';
$search_is_rtl_ui = traveliz_pll_is_rtl( $search_lang );
$search_dir_class = $search_is_rtl_ui ? 'search-overlay--rtl' : 'search-overlay--ltr';
?>

<?php if ( is_search() ) : ?>
    <div class="hero-wrapper wrap-main no-bg mysearch">
<?php elseif ( $header_bg_url ) : ?>
    <div style="background-image: url('<?php echo esc_url( $header_bg_url ); ?>');" class="hero-wrapper wrap-main">
<?php else : ?>
    <div class="hero-wrapper wrap-main no-bg">
<?php endif; ?>
<header>
    <div class="container-3">
                <div class="into-header-0">
                   
                    <div class="nav-wrap">
					
					
					
											                    <?php 
    $hatch_white   = get_theme_mod( 'hatch_white_logo' );
    $is_front_page = is_front_page() || is_home();
    $home_url      = esc_url( home_url( '/' ) );
    $logo_url      = traveliz_get_lang_logo_url();

    if ( $is_front_page ) : ?>
                    <span class="logo2"><img width="237" height="auto" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" /></span>
    <?php else : ?>
                    <a class="logo2" href="<?php echo $home_url; ?>"><img width="237" height="auto" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
    <?php endif; ?>
					
					
					
					
					
					
					
				
					
                        <?php
  // Подключаем walker для мега-меню
  require_once get_template_directory() . '/inc/class-mega-menu-walker.php';
  
  ?>

                        <!-- WordPress меню -->
                        <div class="smenu-container">
                            <?php
    $walker = new Mega_Menu_Walker($current_direction);
    wp_nav_menu(array(
      'theme_location' => 'top',
      'container' => false,
      'items_wrap' => '<div class="smenu">%3$s</div>',
      'walker' => $walker,
      'fallback_cb' => false,
    ));
    ?>

<div class="wrap-9">
<?php get_template_part( 'template-parts/what_you_will_get_buttons' ); ?>
        <div class="custom-lang-switcher">

           <?php
                // Отображение текущего активного языка с флагом в круге и текстом на иврите
                if (function_exists('pll_the_languages')) {
                    $languages = pll_the_languages(array('raw' => 1));
                    if ($languages && is_array($languages)) {
                        foreach ($languages as $lang) {
                            if (isset($lang['current_lang']) && $lang['current_lang']) {
                                $current_lang = $lang['slug'];
                                $current_lang_flag = isset($lang['flag']) ? $lang['flag'] : '';
                                $current_lang_name = isset($lang['name']) ? $lang['name'] : '';
                                if ($current_lang_flag) {
                                    echo '<div class="current-language-display">';
                                    echo '<div class="lang-flag-circle">';
                                    echo '<img src="' . esc_url($current_lang_flag) . '" alt="" class="current-lang-flag" width="20" height="15" loading="lazy" />';
                                    echo '</div>';
                                    echo '<span class="lang-name-hebrew">' . esc_html( strtoupper( $current_lang ) ) . '</span>';
                                    echo '<span class="lang-arrow" aria-hidden="true"><img src="' . esc_url(get_template_directory_uri() . '/img/array.svg') . '" alt="" class="lang-arrow-img" width="10" height="10" /></span>';
                                    echo '</div>';
                                }
                                break;
                            }
                        }
                    }
                }
                ?>

                    <?php
                    if (function_exists('pll_the_languages')) {
                        // ПРОВЕРЬ ЭТИ ПАРАМЕТРЫ: они принудительно выводят все 4 языка
                        $languages = pll_the_languages(array(
                            'raw'                    => 1,
                            'hide_if_no_translation' => 0, // Показать, даже если страница не переведена
                            'hide_current'           => 0, // Показать текущий язык в списке
                            'display_names_as'       => 'slug'
                        ));

                        if ($languages && is_array($languages)) {
                            echo '<ul class="lang-list">';
                            foreach ($languages as $lang) {
                                $active_class = $lang['current_lang'] ? ' active-lang' : '';
                                echo '<li class="lang-item' . $active_class . '">';
                                echo '<a href="' . esc_url($lang['url']) . '" class="lang-item-link">';
                                if (!empty($lang['flag'])) {
                                    echo '<img src="' . esc_url($lang['flag']) . '" alt="" class="lang-dropdown-flag" width="22" height="16" loading="lazy" />';
                                }
                                echo '<span class="lang-item-label" style="font-weight:' . ( $lang['current_lang'] ? 'bold' : 'normal' ) . ';">' . esc_html( strtoupper( $lang['slug'] ) ) . '</span>';
                                echo '</a></li>';
                            }
                            echo '</ul>';
                        }
                    }
                    ?>
                </div>
</div>

                        </div>

                        <!-- Панели мега-меню из WordPress (создаются через walker) -->
                        <div class="mega-layer">
                            <button type="button" class="left-close-menu-svg" aria-label="<?php esc_attr_e( 'Close menu', 'traveliz' ); ?>">
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/img/free-icon-left-arrow-109618.png' ); ?>" alt="" width="24" height="24" />
                            </button>
                            <?php echo $walker->get_panels_output(); ?>
                        </div>
						
						
						
							 <div class="hamburger-menu-top">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/menu-m.png" alt="">
           </div>
               
					  <button type="button" class="button-lupa-up" aria-label="<?php echo esc_attr( get_theme_translation( 'search_open' ) ); ?>">
                        <img class="lupa" src="<?php echo get_template_directory_uri(); ?>/img/zoom-in.svg" alt="">
           </button> 

           <div id="full-screen-search" class="search-overlay <?php echo esc_attr( $search_dir_class ); ?>" aria-hidden="true">
    <button type="button" class="close-btn" id="search-close-btn" aria-label="<?php echo esc_attr( get_theme_translation( 'search_close' ) ); ?>">&times;</button>
    <div class="search-overlay-content">
        <?php get_search_form(); ?>
    </div>
</div>

           <div class="custom-lang-switcher desc-il">

           <?php
                // Отображение текущего активного языка с флагом в круге и текстом на иврите
                if (function_exists('pll_the_languages')) {
                    $languages = pll_the_languages(array('raw' => 1));
                    if ($languages && is_array($languages)) {
                        foreach ($languages as $lang) {
                            if (isset($lang['current_lang']) && $lang['current_lang']) {
                                $current_lang = $lang['slug'];
                                $current_lang_flag = isset($lang['flag']) ? $lang['flag'] : '';
                                $current_lang_name = isset($lang['name']) ? $lang['name'] : '';
                                if ($current_lang_flag) {
                                    echo '<div class="current-language-display">';
                                    echo '<div class="lang-flag-circle">';
                                    echo '<img src="' . esc_url($current_lang_flag) . '" alt="" class="current-lang-flag" width="20" height="15" loading="lazy" />';
                                    echo '</div>';
                                    echo '<span class="lang-name-hebrew">' . esc_html( strtoupper( $current_lang ) ) . '</span>';
                                    echo '<span class="lang-arrow" aria-hidden="true"><img src="' . esc_url(get_template_directory_uri() . '/img/array.svg') . '" alt="" class="lang-arrow-img" width="10" height="10" /></span>';
                                    echo '</div>';
                                }
                                break;
                            }
                        }
                    }
                }
                ?>

                    <?php
                    if (function_exists('pll_the_languages')) {
                        // ПРОВЕРЬ ЭТИ ПАРАМЕТРЫ: они принудительно выводят все 4 языка
                        $languages = pll_the_languages(array(
                            'raw'                    => 1,
                            'hide_if_no_translation' => 0, // Показать, даже если страница не переведена
                            'hide_current'           => 0, // Показать текущий язык в списке
                            'display_names_as'       => 'slug'
                        ));

                        if ($languages && is_array($languages)) {
                            echo '<ul class="lang-list">';
                            foreach ($languages as $lang) {
                                $active_class = $lang['current_lang'] ? ' active-lang' : '';
                                echo '<li class="lang-item' . $active_class . '">';
                                echo '<a href="' . esc_url($lang['url']) . '" class="lang-item-link">';
                                if (!empty($lang['flag'])) {
                                    echo '<img src="' . esc_url($lang['flag']) . '" alt="" class="lang-dropdown-flag" width="22" height="16" loading="lazy" />';
                                }
                                echo '<span class="lang-item-label" style="font-weight:' . ( $lang['current_lang'] ? 'bold' : 'normal' ) . ';">' . esc_html( strtoupper( $lang['slug'] ) ) . '</span>';
                                echo '</a></li>';
                            }
                            echo '</ul>';
                        }
                    }
                    ?>
                </div>

              <div class="wrap-btn-menu-wotasap">
                                  <?php
$green_group  = get_field('green_button_whatsapp_home', 'option');
if ($green_group) :
						$text_green      = $green_group['name_green_button_whatsapp'] ?? '';
						$link_green      = $green_group['link_green_button_whatsapp'];
						$is_popup_green  = $green_group['green_switcher'] ?? false;
						
						if ($is_popup_green !== 'link') : ?>
                         <button data-source="whatsapp" class="button-up modal-trigger_wt">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/watsapp.svg" alt="" />
                                <span><?php echo $text_green; ?></span>
                         </button>
						<?php else : ?>
                          <a href="<?php echo $link_green; ?>" class="button-up">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/watsapp.svg" alt="" />
                                <span><?php echo $text_green; ?></span>
                          </a>
						<?php endif; ?>
					<?php endif; ?>
                 <button class="mobile-menu">
                    <img class="menu-line" src="<?php echo get_template_directory_uri(); ?>/img/menu-mobile.webp" alt="" />
                    <img class="menu-close" src="<?php echo get_template_directory_uri(); ?>/img/close-menu.webp" alt="">
                </button>
                </div>    
            </div>



        </div>

    </div>
</header>


