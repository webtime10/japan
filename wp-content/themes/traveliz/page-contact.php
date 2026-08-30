<?php
/* Template Name: contact */
get_header();
?>
    <section class="contact">

        <div class='container-3'>
            <div class='contact-into'>
                <?php
                $h1_title = get_field( 'h1_title_contact' );
                $h3_form_title = get_field( 'h3_form_title' );
                $text_title = get_field( 'text_title_contact' );
                
                // Yellow button group
                $yellow_button_contact = get_field( 'yellow_button_contact' );
                $yellow_button_switcher_contact = '';
                $yellow_button_text_contact = '';
                $yellow_button_link_contact = '';
                
                if ( $yellow_button_contact ) {
                    $yellow_button_switcher_contact = $yellow_button_contact['yellow_button_contact_sw'] ?? '';
                    $yellow_button_text_contact = $yellow_button_contact['yellow_button_contact_text'] ?? '';
                    $yellow_button_link_contact = $yellow_button_contact['yellow_button_contact_link'] ?? '#';
                }
                ?>
                    <?php if ( $h1_title ) : ?>
                        <h1><?php echo wp_kses_post( $h1_title ); ?></h1>
                        <?php endif; ?>
                            <?php if ( $text_title ) : ?>
                                <p>
                                    <?php echo wp_kses_post( $text_title ); ?>
                                </p>
                                <?php endif; ?>

                                    <img class="oval22" src="<?php echo get_template_directory_uri(); ?>/img/Ellipse103.webp" alt="" />

                                    <div class="wrap-cont">
                                        <?php if ( $yellow_button_text_contact ) : ?>
                                            <?php if ( $yellow_button_switcher_contact !== 'link' ) : ?>
                                                <button type="button" data-source="general_request" class="order-mr cont-1 js-open-popup modal-trigger_wt">
                                                    <span><?php echo wp_kses_post( $yellow_button_text_contact ); ?></span>
                                                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729" />
                                                    </svg>
                                                </button>
                                            <?php else : ?>
                                                <a class="order-mr cont-1" href="<?php echo esc_url( $yellow_button_link_contact ); ?>">
                                                    <span><?php echo wp_kses_post( $yellow_button_text_contact ); ?></span>
                                                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729" />
                                                    </svg>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

            </div>
        </div>

    </section>
    </div>
    <section class="contact-a">

        <div class="container-3">

            <div class="into-contact-a">
                <div class="left-contact">
                    <div class="form-wrapper">
                        <h3><?php echo $h3_form_title; ?></h3>
                        <?php
    $lang = function_exists('pll_current_language') ? pll_current_language() : traveliz_pll_default_slug();

    $forms = [
        'he' => '3397f3e',
        'en' => '3397f3e',
        'ar' => '3397f3e',
    ];

    $current_form_id = traveliz_resolve_cf7_form_id($forms, is_string($lang) ? $lang : traveliz_pll_current_slug());

    // Выводим форму напрямую, игнорируя ACF и его слеши
    echo do_shortcode('[contact-form-7 id="' . $current_form_id . '"]'); 
    ?>
                    </div>

                </div>
                <div class="right-contact">
                    <?php
                    // Переменные с именами как на скриншотах
                    $online_text_1 = get_field( 'online_text_1' );
                    $online_text_2 = get_field( 'online_text_2' );
                    $email_text_contact = get_field( 'email_text_contact' );
                    $email_link_text_contact = get_field( 'email_link_text_contact' );
                    $email_link_contact = get_field( 'email_link_contact' );
                    $time_work_title = get_field( 'time_work_title' );
                    $time_work_text_1 = get_field( 'time_work_text_1' );
                    $time_work_text_2 = get_field( 'time_work_text_2' );
                    $title_tel_contact = get_field( 'title_tel_contact' );
                    $text_tel_contact = get_field( 'text_tel_contact' );
                    $link_tel_contact = get_field( 'link_tel_contact' );
                    $office_title_contact = get_field( 'office_title_contact' );
                    $office_text_contact = get_field( 'office_text_contact' );
                    $office_title_map_contact = get_field( 'office_title_map_contact' );
                    $office_text_map_contact = get_field( 'office_text_map_contact' );
                    $office_link_map_contact = get_field( 'office_link_map_contact' );
                    $office_button_contact = get_field( 'office_button_contact' );
                    
                    // Green button (WhatsApp) group
                    $green_button_contact = get_field( 'green_button_contact' );
                    $green_button_switcher_contact = '';
                    $green_button_text_contact = '';
                    $green_button_link_contact = '';
                    
                    if ( $green_button_contact ) {
                        $green_button_switcher_contact = $green_button_contact['green_button_contact_sw'] ?? '';
                        $green_button_text_contact = $green_button_contact['green_button_text'] ?? '';
                        $green_button_link_contact = $green_button_contact['green_button_contact_link'] ?? '#';
                    }
                    ?>
                    <div class="block-1_contact">
                        <div class="dot-wrap">
                            <div class="dot"></div>
                            <span><?php echo $online_text_1; ?></span>
                            <p><?php echo $online_text_2; ?></p>
                        </div>
                        <?php if ( $green_button_text_contact ) : ?>
                            <?php if ( $green_button_switcher_contact !== 'link' ) : ?>
                                <button type="button" data-source="whatsapp" class="btn-whatsapp modal-trigger_wt">
                                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.9797 3.24263C16.8881 1.15808 14.0992 0 11.1554 0C5.03544 0 0.0774672 4.94115 0.0774672 11.0404C0.0774672 12.9705 0.619747 14.9007 1.54937 16.522L0 22.2352L5.8876 20.6911C7.51443 21.5403 9.2962 22.0036 11.1554 22.0036C17.2754 22.0036 22.2334 17.0624 22.2334 10.9632C22.156 8.10658 21.0714 5.32718 18.9797 3.24263ZM16.5008 14.9779C16.2684 15.5955 15.1838 16.2132 14.6415 16.2904C14.1767 16.3676 13.557 16.3676 12.9372 16.2132C12.5499 16.0588 12.0076 15.9043 11.3879 15.5955C8.59899 14.4374 6.81722 11.658 6.66228 11.4264C6.50734 11.272 5.50026 9.95952 5.50026 8.56982C5.50026 7.18012 6.19747 6.56247 6.42987 6.25365C6.66228 5.94483 6.97215 5.94483 7.20456 5.94483C7.35949 5.94483 7.5919 5.94483 7.74683 5.94483C7.90177 5.94483 8.13418 5.86762 8.36658 6.40806C8.59899 6.9485 9.14126 8.3382 9.21873 8.41541C9.2962 8.56982 9.2962 8.72423 9.21873 8.87864C9.14126 9.03305 9.0638 9.18746 8.90886 9.34187C8.75392 9.49628 8.59899 9.7279 8.52152 9.80511C8.36658 9.95952 8.21165 10.1139 8.36658 10.3455C8.52152 10.6544 9.0638 11.5036 9.91595 12.2757C11.0005 13.2021 11.8527 13.511 12.1625 13.6654C12.4724 13.8198 12.6273 13.7426 12.7823 13.5882C12.9372 13.4338 13.4795 12.8161 13.6344 12.5073C13.7894 12.1985 14.0218 12.2757 14.2542 12.3529C14.4866 12.4301 15.881 13.1249 16.1134 13.2794C16.4233 13.4338 16.5782 13.511 16.6557 13.5882C16.7332 13.8198 16.7332 14.3602 16.5008 14.9779Z" fill="white"></path>
                                    </svg>
                                    <span><?php echo wp_kses_post( $green_button_text_contact ); ?></span>
                                </button>
                            <?php else : ?>
                                <a class="btn-whatsapp" href="<?php echo esc_url( $green_button_link_contact ); ?>">
                                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.9797 3.24263C16.8881 1.15808 14.0992 0 11.1554 0C5.03544 0 0.0774672 4.94115 0.0774672 11.0404C0.0774672 12.9705 0.619747 14.9007 1.54937 16.522L0 22.2352L5.8876 20.6911C7.51443 21.5403 9.2962 22.0036 11.1554 22.0036C17.2754 22.0036 22.2334 17.0624 22.2334 10.9632C22.156 8.10658 21.0714 5.32718 18.9797 3.24263ZM16.5008 14.9779C16.2684 15.5955 15.1838 16.2132 14.6415 16.2904C14.1767 16.3676 13.557 16.3676 12.9372 16.2132C12.5499 16.0588 12.0076 15.9043 11.3879 15.5955C8.59899 14.4374 6.81722 11.658 6.66228 11.4264C6.50734 11.272 5.50026 9.95952 5.50026 8.56982C5.50026 7.18012 6.19747 6.56247 6.42987 6.25365C6.66228 5.94483 6.97215 5.94483 7.20456 5.94483C7.35949 5.94483 7.5919 5.94483 7.74683 5.94483C7.90177 5.94483 8.13418 5.86762 8.36658 6.40806C8.59899 6.9485 9.14126 8.3382 9.21873 8.41541C9.2962 8.56982 9.2962 8.72423 9.21873 8.87864C9.14126 9.03305 9.0638 9.18746 8.90886 9.34187C8.75392 9.49628 8.59899 9.7279 8.52152 9.80511C8.36658 9.95952 8.21165 10.1139 8.36658 10.3455C8.52152 10.6544 9.0638 11.5036 9.91595 12.2757C11.0005 13.2021 11.8527 13.511 12.1625 13.6654C12.4724 13.8198 12.6273 13.7426 12.7823 13.5882C12.9372 13.4338 13.4795 12.8161 13.6344 12.5073C13.7894 12.1985 14.0218 12.2757 14.2542 12.3529C14.4866 12.4301 15.881 13.1249 16.1134 13.2794C16.4233 13.4338 16.5782 13.511 16.6557 13.5882C16.7332 13.8198 16.7332 14.3602 16.5008 14.9779Z" fill="white"></path>
                                    </svg>
                                    <span><?php echo wp_kses_post( $green_button_text_contact ); ?></span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="block-2_contact">
                        <div class="item-block2">
                            <span><?php echo $email_text_contact; ?></span>
                            <a class="email-cintact" href="mailto:<?php echo $email_link_contact ?>"><?php echo $email_link_contact ?></a>
                        </div>
                        <div class="item-block2">
                            <span class="chas"><?php echo $time_work_title ?></span>
                            <div class="chas-r"><span><?php echo $time_work_text_1; ?>
                            </span><span><?php echo $time_work_text_2 ?></span></div>
                        </div>
                        <div class="item-block2">
                            <span><?php echo $title_tel_contact; ?></span>
                            <span class="telehone"><a href="tel:<?php echo $link_tel_contact; ?>"><?php echo $text_tel_contact ?></a></span>
                            <a class="calbek" href="">
                                <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.4038 14.1436C9.38382 14.1061 6.49311 13.7066 3.46569 10.6799C0.438984 7.65244 0.0401922 4.76244 0.00194225 3.74173C-0.0547244 2.18623 1.13669 0.675356 2.51298 0.0853145C2.67872 0.0137495 2.86021 -0.0134976 3.03965 0.00624721C3.21909 0.025992 3.39032 0.0920501 3.53653 0.19794C4.66986 1.02386 5.45186 2.27336 6.12336 3.25581C6.27111 3.47166 6.33428 3.73433 6.30084 3.99375C6.2674 4.25318 6.13968 4.49124 5.94203 4.66256L4.56007 5.68894C4.4933 5.73715 4.4463 5.80796 4.4278 5.88821C4.40931 5.96846 4.42056 6.0527 4.45948 6.12527C4.77257 6.69406 5.32932 7.54123 5.96682 8.17873C6.60432 8.81623 7.49186 9.40981 8.10032 9.75831C8.17661 9.80114 8.2664 9.81311 8.35125 9.79177C8.43609 9.77043 8.50954 9.71739 8.55648 9.64356L9.45607 8.27436C9.62145 8.05466 9.86543 7.90749 10.1369 7.86365C10.4084 7.81982 10.6863 7.88272 10.9124 8.03919C11.909 8.72911 13.0721 9.49765 13.9235 10.5878C14.038 10.735 14.1108 10.9104 14.1344 11.0954C14.1579 11.2804 14.1313 11.4684 14.0574 11.6396C13.4645 13.023 11.9643 14.201 10.4038 14.1436Z" fill="#695729" />
                                </svg>
                                <span><?php echo $office_button_contact; ?></span></a>
                        </div>
                        <div class="item-block2">
                            <span class="ofis"><?php echo $office_title_contact; ?></span>
                            <span class="docota"><?php echo $office_text_map_contact;?></span>
                            <a class="dmap" href="<?php echo $office_link_map_contact; ?>"><?php echo $office_text_map_contact; ?></a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="social-contact">
                <?php
                // Переменные с именами как на скриншотах
                $fb_kontact_text = get_field( 'fb_kontact_text' );
                $fb_kontact_link = get_field( 'fb_kontact_link' );
                $insta_kontact_text = get_field( 'insta_kontact_text' );
                $insta_kontact_link = get_field( 'insta_kontact_link' );
                $tik_tok_kontact_text = get_field( 'tik_tok_kontact_text' );
                $tik_tok_kontact_link = get_field( 'tik_tok_kontact_link' );
                $ytub_kontact_text = get_field( 'ytub_kontact_text' );
                $ytub_kontact_link = get_field( 'ytub_kontact_llink' );
                ?>
                <a href="<?php echo $fb_kontact_link; ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/fasb.svg" alt=""><span><?php echo $fb_kontact_text; ?></span></a>
                <a href="<?php echo $insta_kontact_link; ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/insta.svg" alt=""><span><?php echo $insta_kontact_text ?></span></a>
                <a href="<?php echo $tik_tok_kontact_link; ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/tictoc.webp" alt=""><span><?php echo $tik_tok_kontact_text; ?></span></a>
                <a href="<?php echo $ytub_kontact_link; ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/utub.svg" alt=""><span><?php echo $ytub_kontact_text; ?></span></a>
            </div>
            </div>
    </section>

    <?php

get_footer();