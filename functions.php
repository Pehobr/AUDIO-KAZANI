<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'font-awesome' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

// END ENQUEUE PARENT ACTION


/**
 * Načte skripty a styly pro šablonu stránky "Kázání".
 */
function enqueue_kazani_assets() {
    // Zkontroluje, zda je použita naše vlastní šablona stránky 'template-kazani.php'
    if ( is_page_template( 'template-kazani.php' ) ) {
        
        // Načtení Tailwind CSS z CDN
        wp_enqueue_script( 'tailwind-css', 'https://cdn.tailwindcss.com', array(), null, false );

        // Načtení Google Fonts
        wp_enqueue_style( 'google-fonts-kazani', 'https://fonts.googleapis.com/css2?family=DynaPuff&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap', array(), null );
        
        // Načtení vlastního CSS souboru z adresáře /css/ v child šabloně
        wp_enqueue_style( 'kazani-style', get_stylesheet_directory_uri() . '/css/kazani.css', array(), '1.0' );

        // Načtení vlastního JavaScript souboru z adresáře /js/ v child šabloně
        wp_enqueue_script( 'kazani-script', get_stylesheet_directory_uri() . '/js/kazani.js', array(), '1.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_kazani_assets' );

