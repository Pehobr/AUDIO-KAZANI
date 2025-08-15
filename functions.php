<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// ... (tato část zůstává stejná jako předtím)
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
    if ( is_page_template( 'template-kazani.php' ) ) {
        wp_enqueue_script( 'tailwind-css', 'https://cdn.tailwindcss.com', array(), null, false );
        // Načtení všech potřebných fontů
        wp_enqueue_style( 'google-fonts-kazani', 'https://fonts.googleapis.com/css2?family=Akaya+Kanadaka&family=Marck+Script&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap', array(), null );
        
        // ZMĚNA ZDE: Zvýšena verze stylu na 2.0
        wp_enqueue_style( 'kazani-style', get_stylesheet_directory_uri() . '/css/kazani.css', array(), '2.0' );
        
        // ZMĚNA ZDE: Zvýšena verze skriptu na 2.0
        wp_enqueue_script( 'kazani-script', get_stylesheet_directory_uri() . '/js/kazani.js', array(), '2.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_kazani_assets' );


// --- NASTAVENÍ STRÁNKY V ADMINISTRACI PRO NAČÍTÁNÍ KÁZÁNÍ ---
// Tato část zůstává beze změny

/**
 * 1. Vytvoření menu v administraci WordPressu.
 */
function kazani_admin_menu() {
    add_menu_page(
        'Nastavení Kázání',
        'Kázání',
        'manage_options',
        'kazani-settings',
        'kazani_settings_page_html',
        'dashicons-cloud-upload',
        20
    );
}
add_action('admin_menu', 'kazani_admin_menu');

/**
 * 2. Vykreslení obsahu administrátorské stránky (HTML) včetně hlášek.
 */
function kazani_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php
        // Zobrazíme hlášku na základě parametru v URL
        if (isset($_GET['kazani_update_status'])) {
            $status = sanitize_key($_GET['kazani_update_status']);
            if ($status === 'success') {
                echo '<div id="message" class="notice notice-success is-dismissible"><p>✅ Data o kázáních byla úspěšně aktualizována.</p></div>';
            } elseif ($status === 'error_fetch') {
                echo '<div id="message" class="notice notice-error is-dismissible"><p>❌ Nepodařilo se načíst data z Google Tabulky. Zkontrolujte URL a zda je tabulka správně publikována.</p></div>';
            } elseif ($status === 'error_no_url') {
                echo '<div id="message" class="notice notice-error is-dismissible"><p>❌ URL adresa pro Google Tabulku není definována v souboru wp-config.php.</p></div>';
            }
        }
        ?>

        <p>Zde můžete ručně aktualizovat data o kázáních z vaší Google Tabulky. Data se poté uloží do databáze a nebudou se stahovat při každém načtení stránky pro návštěvníky.</p>
        
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="action" value="update_kazani_data">
            <?php wp_nonce_field('update_kazani_data_nonce'); ?>
            <?php submit_button('Načíst nová data z Google Tabulky'); ?>
        </form>

        <?php
        $last_updated = get_option('kazani_data_last_updated');
        if ($last_updated) {
            echo '<p><i>Poslední úspěšná aktualizace proběhla: ' . date_i18n('j. F Y H:i:s', $last_updated) . '</i></p>';
        } else {
            echo '<p><i>Data ještě nebyla nikdy načtena. Klikněte na tlačítko pro první načtení.</i></p>';
        }
        
        // Zobrazení odkazu na RSS feed
        echo '<h3>Váš odkaz na RSS feed podcastu:</h3>';
        echo '<p>Tento odkaz vložte do podcastových aplikací (Spotify, Apple Podcasts atd.):</p>';
        echo '<strong><a href="' . esc_url(get_site_url(null, 'podcast')) . '" target="_blank">' . esc_url(get_site_url(null, 'podcast')) . '</a></strong>';
        ?>
    </div>
    <?php
}

/**
 * 3. Zpracování formuláře a přesměrování s parametrem.
 */
function handle_update_kazani_data() {
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update_kazani_data_nonce') || !current_user_can('manage_options')) {
        wp_die('Bezpečnostní kontrola selhala.');
    }

    $google_sheet_csv_url = defined('KAZANI_GOOGLE_SHEET_URL') ? KAZANI_GOOGLE_SHEET_URL : '';
    $redirect_url = admin_url('admin.php?page=kazani-settings');

    if (empty($google_sheet_csv_url)) {
        $redirect_url = add_query_arg('kazani_update_status', 'error_no_url', $redirect_url);
    } else {
        $response = wp_remote_get($google_sheet_csv_url, ['timeout' => 20]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            $redirect_url = add_query_arg('kazani_update_status', 'error_fetch', $redirect_url);
        } else {
            $csv_data = wp_remote_retrieve_body($response);
            update_option('kazani_data_csv', $csv_data);
            update_option('kazani_data_last_updated', time());
            $redirect_url = add_query_arg('kazani_update_status', 'success', $redirect_url);
        }
    }
    
    wp_redirect($redirect_url);
    exit;
}
add_action('admin_post_update_kazani_data', 'handle_update_kazani_data');


// --- PŘIDÁNO: KÓD PRO RSS FEED PODCASTU ---

/**
 * 1. Registrace přepisovacího pravidla pro hezkou URL.
 * Díky tomu bude feed dostupný na adrese /podcast/
 */
function kazani_podcast_rewrite_rule() {
    add_rewrite_rule('^podcast/?$', 'index.php?kazani_podcast_feed=1', 'top');
}
add_action('init', 'kazani_podcast_rewrite_rule');

/**
 * 2. Přidání query var, aby WordPress rozuměl našemu parametru.
 */
function kazani_podcast_query_vars($vars) {
    $vars[] = 'kazani_podcast_feed';
    return $vars;
}
add_filter('query_vars', 'kazani_podcast_query_vars');

/**
 * 3. Načtení souboru s feedem, pokud je náš parametr v URL.
 */
function kazani_podcast_template_redirect() {
    if (get_query_var('kazani_podcast_feed')) {
        $feed_template = get_stylesheet_directory() . '/podcast-rss.php';
        if (file_exists($feed_template)) {
            include $feed_template;
            exit;
        }
    }
}
add_action('template_redirect', 'kazani_podcast_template_redirect');

/**
 * 4. Vyčistí (flush) přepisovací pravidla při aktivaci šablony.
 * To je důležité, aby se nové pravidlo pro /podcast/ správně načetlo.
 */
function kazani_flush_rewrite_rules_on_activate() {
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'kazani_flush_rewrite_rules_on_activate');
