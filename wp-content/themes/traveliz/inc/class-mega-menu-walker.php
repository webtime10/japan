<?php
/**
 * Mega Menu Walker for WordPress
 * Creates mega menu structure with panels and sections
 *
 * @package traveliz
 */

class Mega_Menu_Walker extends Walker_Nav_Menu {
    
    private $current_direction;
    private $panels_output = '';
    private $current_panel_id = '';
    private $section_items = array();
    private $current_panel_image = '';
    private $current_panel_link = '';
    private $section_count = 0;
    private $image_inserted = false;
    
    public function __construct($direction = 'ltr') {
        $this->current_direction = $direction;
    }
    
    /**
     * Start the list before the elements are added.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        // Не создаем здесь ничего
    }
    
    /**
     * End the list after the elements are added.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        // Не закрываем секции здесь - они закрываются в start_el при создании новой секции
        // или в end_el при закрытии всего меню
    }
    
    /**
     * Start the element output.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';
        
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // Проверяем, есть ли подменю
        $has_children = in_array( 'menu-item-has-children', $classes );
        
        // Для главного меню (depth 0)
        if ( $depth === 0 ) {
            $panel_id = sanitize_title( $item->title );
            
            // Если есть подменю, добавляем data-panel
            $data_panel = $has_children ? ' data-panel="' . esc_attr( $panel_id ) . '"' : '';
            
            $output .= $indent . '<div' . $data_panel . '>';
            
            $attributes = '';
            $attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : ' href="#"';
            $attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
            $attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
            
            // Получаем миниатюру (Featured Image) страницы для главного меню
            $thumbnail = '';
            if ( $item->object_id ) {
                $thumbnail_id = get_post_thumbnail_id( $item->object_id );
                if ( $thumbnail_id ) {
                    $thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
                    if ( $thumbnail_url && isset( $thumbnail_url[0] ) ) {
                        $thumbnail = '<img src="' . esc_url( $thumbnail_url[0] ) . '" alt="' . esc_attr( $item->title ) . '" class="main-menu-thumbnail">';
                    }
                }
            }
            
            $item_output = '<a' . $attributes . '>';
            
            // Для RTL: порядок - миниатюра → название → caret
            if ( $this->current_direction === 'rtl' ) {
                // 1. Миниатюра (если есть)
                if ( $thumbnail ) {
                    $item_output .= $thumbnail;
                }
                // 2. Название страницы в span
                $item_output .= '<span class="menu-item-text">' . esc_html( $item->title ) . '</span>';
                // 3. Caret (если есть подменю)
                if ( $has_children ) {
                    $item_output .= '<span class="caret2" aria-hidden="true">▾</span>';
                }
            }
            // Для LTR: порядок - caret → название → миниатюра
            else {
                // 1. Caret (если есть подменю)
                if ( $has_children ) {
                    $item_output .= '<span class="caret2" aria-hidden="true">▾</span> ';
                }
                // 2. Название страницы в span
                $item_output .= '<span class="menu-item-text">' . esc_html( $item->title ) . '</span>';
                // 3. Миниатюра (если есть)
                if ( $thumbnail ) {
                    $item_output .= ' ' . $thumbnail;
                }
            }
            
            $item_output .= '</a>';
            
            $output .= $item_output;
            
            // Если есть подменю, начинаем собирать панель
            if ( $has_children ) {
                $this->current_panel_id = $panel_id;
                $this->section_items = array();
                $this->section_count = 0;
                $this->image_inserted = false;
                
                // Получаем картинку из ACF поля menu_image для этой страницы
                $panel_image = '';
                $panel_link = ''; // Ссылка для картинки из ACF поля link_menu
                
                if ( $item->object_id && function_exists( 'get_field' ) ) {
                    // Получаем картинку
                    $acf_image = get_field( 'menu_image', $item->object_id );
                    
                    if ( $acf_image ) {
                        // Если ACF поле содержит ID изображения (число)
                        if ( is_numeric( $acf_image ) ) {
                            $image_url = wp_get_attachment_image_src( $acf_image, 'full' );
                            if ( $image_url && isset( $image_url[0] ) ) {
                                $panel_image = $image_url[0];
                            }
                        }
                        // Если ACF поле содержит массив (объект изображения)
                        elseif ( is_array( $acf_image ) ) {
                            if ( isset( $acf_image['url'] ) ) {
                                $panel_image = $acf_image['url'];
                            } elseif ( isset( $acf_image['ID'] ) ) {
                                $image_url = wp_get_attachment_image_src( $acf_image['ID'], 'full' );
                                if ( $image_url && isset( $image_url[0] ) ) {
                                    $panel_image = $image_url[0];
                                }
                            }
                        }
                        // Если ACF поле содержит URL строку
                        elseif ( is_string( $acf_image ) && filter_var( $acf_image, FILTER_VALIDATE_URL ) ) {
                            $panel_image = $acf_image;
                        }
                    }
                    
                    // Получаем ссылку из ACF поля link_menu
                    $acf_link = get_field( 'link_menu', $item->object_id );
                    if ( $acf_link && !empty( $acf_link ) ) {
                        $panel_link = esc_url( $acf_link );
                    } else {
                        // Если ссылки нет, используем URL страницы или #
                        $panel_link = ! empty( $item->url ) ? esc_url( $item->url ) : '#';
                    }
                } else {
                    // Если ACF не доступен, используем URL страницы
                    $panel_link = ! empty( $item->url ) ? esc_url( $item->url ) : '#';
                }
                
                // Сохраняем картинку и ссылку для использования в end_el
                $this->current_panel_image = $panel_image;
                $this->current_panel_link = $panel_link;
                
                $this->panels_output .= $n . '<div class="mega" id="panel-' . esc_attr( $panel_id ) . '" role="region" aria-label="Меню: ' . esc_attr( $item->title ) . '">';
                $this->panels_output .= $n . '<div class="mega-grid">';
                
                // Картинка не добавляется здесь - она будет добавлена в конце (справа) для RTL и LTR
            }
        } 
        // Для подменю (depth 1) - создаем секции
        elseif ( $depth === 1 ) {
            // Группируем по 5 пунктов в секцию
            if ( empty( $this->section_items ) || count( $this->section_items ) >= 5 ) {
                // Закрываем предыдущую секцию если есть
                if ( !empty( $this->section_items ) ) {
                    $this->panels_output .= '</div></section>';
                    $this->section_count++;
                }
                
                // Для LTR: вставляем картинку ПОСЛЕ закрытия второй секции (section_count = 2)
                // Это происходит когда мы начинаем обрабатывать элементы для третьей секции
                if ( $this->current_direction === 'ltr' && $this->section_count === 2 && !empty( $this->current_panel_image ) && !$this->image_inserted ) {
                    $link_url = !empty( $this->current_panel_link ) ? $this->current_panel_link : '#';
                    $this->panels_output .= $n . '<section class="mega-image-section">';
                    $this->panels_output .= $n . '<a href="' . esc_url( $link_url ) . '" class="image-cta"><img src="' . esc_url( $this->current_panel_image ) . '" alt=""></a>';
                    $this->panels_output .= $n . '</section>';
                    $this->image_inserted = true;
                }
                
                // Начинаем новую текстовую секцию
                $this->panels_output .= $n . '<section>';
                $this->panels_output .= $n . '<div>';
                $this->section_items = array();
            }
            
            $attributes = '';
            $attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : ' href="#"';
            $attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
            $attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
            
            // Получаем картинку из ACF поля или featured image
            // Работает для страниц (page) и кастомных типов постов (например, cities)
            $thumbnail = '';
            if ( $item->object_id ) {
                // Сначала проверяем ACF поле "menu_image"
                $acf_image = '';
                if ( function_exists( 'get_field' ) ) {
                    $acf_image = get_field( 'menu_image', $item->object_id );
                }
                
                if ( $acf_image ) {
                    // Если ACF поле содержит ID изображения (число)
                    if ( is_numeric( $acf_image ) ) {
                        $thumbnail_url = wp_get_attachment_image_src( $acf_image, array(150, 150) );
                        if ( $thumbnail_url && isset( $thumbnail_url[0] ) ) {
                            $thumbnail = '<img src="' . esc_url( $thumbnail_url[0] ) . '" alt="' . esc_attr( $item->title ) . '" width="150" height="150" class="menu-item-thumbnail">';
                        }
                    }
                    // Если ACF поле содержит массив (объект изображения)
                    elseif ( is_array( $acf_image ) ) {
                        // Проверяем разные варианты структуры массива
                        if ( isset( $acf_image['url'] ) ) {
                            $thumbnail = '<img src="' . esc_url( $acf_image['url'] ) . '" alt="' . esc_attr( $item->title ) . '" width="150" height="150" class="menu-item-thumbnail">';
                        } elseif ( isset( $acf_image['ID'] ) ) {
                            $thumbnail_url = wp_get_attachment_image_src( $acf_image['ID'], array(150, 150) );
                            if ( $thumbnail_url && isset( $thumbnail_url[0] ) ) {
                                $thumbnail = '<img src="' . esc_url( $thumbnail_url[0] ) . '" alt="' . esc_attr( $item->title ) . '" width="150" height="150" class="menu-item-thumbnail">';
                            }
                        } elseif ( isset( $acf_image['sizes'] ) && isset( $acf_image['sizes']['thumbnail'] ) ) {
                            $thumbnail = '<img src="' . esc_url( $acf_image['sizes']['thumbnail'] ) . '" alt="' . esc_attr( $item->title ) . '" width="150" height="150" class="menu-item-thumbnail">';
                        }
                    }
                    // Если ACF поле содержит URL строку
                    elseif ( is_string( $acf_image ) && filter_var( $acf_image, FILTER_VALIDATE_URL ) ) {
                        $thumbnail = '<img src="' . esc_url( $acf_image ) . '" alt="' . esc_attr( $item->title ) . '" width="150" height="150" class="menu-item-thumbnail">';
                    }
                }
                
                // Если ACF поля нет или оно пустое, используем featured image как fallback
                if ( !$thumbnail ) {
                    $thumbnail_id = get_post_thumbnail_id( $item->object_id );
                    if ( $thumbnail_id ) {
                        $thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, array(150, 150) );
                        if ( $thumbnail_url && isset( $thumbnail_url[0] ) ) {
                            $thumbnail = '<img src="' . esc_url( $thumbnail_url[0] ) . '" alt="' . esc_attr( $item->title ) . '" width="150" height="150" class="menu-item-thumbnail">';
                        }
                    }
                }
            }
            
            // Выводим пункт меню с миниатюрой
            // Порядок: SPAN с названием, затем картинка
            $this->panels_output .= $n . '<div class="menu-item-with-thumb">';
            $this->panels_output .= $n . '<a' . $attributes . '>';
            $this->panels_output .= '<span class="menu-item-text">' . esc_html( $item->title ) . '</span>';
            if ( $thumbnail ) {
                $this->panels_output .= $thumbnail;
            }
            $this->panels_output .= '</a>';
            $this->panels_output .= '</div>';
            
            $this->section_items[] = $item->ID;
        }
    }
    
    /**
     * End the element output.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        
        if ( $depth === 0 ) {
            $has_children = in_array( 'menu-item-has-children', (array) $item->classes );
            
            // Закрываем панель мега-меню если есть подменю
            if ( $has_children ) {
                // Закрываем последнюю секцию если есть открытые элементы
                if ( !empty( $this->section_items ) ) {
                    $this->panels_output .= '</div></section>';
                    $this->section_count++;
                    
                    // Для LTR: если закрыли вторую секцию (ровно 2 секции), добавляем картинку как третью секцию
                    if ( $this->current_direction === 'ltr' && $this->section_count === 2 && !empty( $this->current_panel_image ) && !$this->image_inserted ) {
                        $link_url = !empty( $this->current_panel_link ) ? $this->current_panel_link : '#';
                        $this->panels_output .= $n . '<section class="mega-image-section">';
                        $this->panels_output .= $n . '<a href="' . esc_url( $link_url ) . '" class="image-cta"><img src="' . esc_url( $this->current_panel_image ) . '" alt=""></a>';
                        $this->panels_output .= $n . '</section>';
                        $this->image_inserted = true;
                    }
                    
                    $this->section_items = array();
                }
                
                // Для RTL: картинка всегда последней (справа)
                if ( $this->current_direction === 'rtl' && !empty( $this->current_panel_image ) ) {
                    $link_url = !empty( $this->current_panel_link ) ? $this->current_panel_link : '#';
                    $this->panels_output .= $n . '<section class="mega-image-section">';
                    $this->panels_output .= $n . '<a href="' . esc_url( $link_url ) . '" class="image-cta"><img src="' . esc_url( $this->current_panel_image ) . '" alt="' . esc_attr( $item->title ) . '"></a>';
                    $this->panels_output .= $n . '</section>';
                }
                
                $this->panels_output .= $n . '</div>'; // .mega-grid
                $this->panels_output .= $n . '</div>'; // .mega
                
                // Очищаем переменные
                $this->current_panel_id = '';
                $this->current_panel_image = '';
                $this->current_panel_link = '';
                $this->section_count = 0;
                $this->image_inserted = false;
            }
            
            $output .= $n . '</div>'; // главный div пункта меню
        }
    }
    
    /**
     * Получить вывод панелей
     */
    public function get_panels_output() {
        return $this->panels_output;
    }
}
