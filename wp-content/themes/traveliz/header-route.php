
<!doctype html>
<?php
$current_lang      = 'he';
$current_direction = 'rtl';
?>
<html lang="<?php echo esc_attr( $current_lang ); ?>" dir="<?php echo esc_attr( $current_direction ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />



<?php wp_head(); ?>
</head>

<body>
<?php wp_body_open(); ?>
<?php
$search_lang      = 'he';
$search_is_rtl_ui = true;
$search_dir_class = $search_is_rtl_ui ? 'search-overlay--rtl' : 'search-overlay--ltr';
?>
<section class="hero-wrapper" class="wrap-main">
<header>
    <div class="container-3">
        <div class="into-header">
           <div class="hamburger-menu">
               <button class="hamburger-btn" id="hamburger-btn">
                   <span class="hamburger-line"></span>
                   <span class="hamburger-line"></span>
                   <span class="hamburger-line"></span>
               </button>
           </div>
           <button class="button-up">
                <img src="<?php echo get_template_directory_uri(); ?>/img/watsapp.svg" alt="">
                <span>יצירת קשר דרך וואטסאפ</span>
           </button> 
           
           <div class="custom-lang-switcher">

           <?php
                // Отображение текущего активного языка с флагом в круге и текстом на иврите
                if (function_exists('pll_the_languages')) {
                    $languages = pll_the_languages(array('raw' => 1));
                    if ($languages && is_array($languages)) {
                        foreach ($languages as $lang) {
                            if (isset($lang['current_lang']) && $lang['current_lang']) {
                                $current_lang = $lang['slug'];
                                $current_lang_flag = isset($lang['flag']) ? $lang['flag'] : '';                                $current_lang_name = isset($lang['name']) ? $lang['name'] : '';                                if ($current_lang_flag) {
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
                                $active_class = $lang['current_lang'] ? ' active-lang' : '';                                echo '<li class="lang-item' . $active_class . '">';
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
                     <button type="button" class="button-lupa-up" aria-label="<?php echo esc_attr( get_theme_translation( 'search_open' ) ); ?>">
                   <img class="lupa" src="<?php echo get_template_directory_uri(); ?>/img/zoom-in.svg" alt="">
                </button>
                <div id="full-screen-search" class="search-overlay <?php echo esc_attr( $search_dir_class ); ?>" aria-hidden="true">
                    <button type="button" class="close-btn" id="search-close-btn" aria-label="<?php echo esc_attr( get_theme_translation( 'search_close' ) ); ?>">&times;</button>
                    <div class="search-overlay-content">
                        <?php get_search_form(); ?>
                    </div>
                </div>
            <nav id="mobile-nav">
                <button class="mobile-menu-close" id="mobile-menu-close">
                    <span class="close-line"></span>
                    <span class="close-line"></span>
                </button>
                <?php
                // 1. Вывод основного меню (Polylang сам подставит нужное меню для текущего языка)
                wp_nav_menu(
                    array(
                        'theme_location' => 'top',
                        'menu_id'        => 'main-menu',
                        'menu_class'     => 'sm sm-blue',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'walker'         => new Menu_With_Thumbnails_Walker(),
                    )
                );
                ?>
            </nav>
            <img class="main-logo" src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="">
        </div>
    </div>
</header>