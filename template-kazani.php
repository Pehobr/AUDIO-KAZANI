<?php
/**
 * Template Name: Kázání
 *
 * Šablona pro stránku s audio kázáními, která se otevírají v modálním okně.
 * Verze: 3.2 - Úprava responzivních velikostí nadpisu
 */

get_header(); // Načte hlavičku šablony

// --- NASTAVENÍ ---
// Data se nyní načítají z databáze WordPressu pomocí get_option()
$csv_data = get_option('kazani_data_csv');

// Základní část URL adresy pro MP3 soubory
$base_mp3_url = 'https://audiokostel.cz/audio-kazani/';
// --- KONEC NASTAVENÍ ---

?>

<main id="primary" class="site-main">
    
    <!-- Hlavní nadpis stránky s responzivní velikostí textu -->
    <h1 class="text-2xl md:text-3xl lg:text-4xl text-white text-center my-8" style="font-family: 'Marck Script', cursive;">
        Inspirace Božího slova
    </h1>

    <!-- Kontejner pro tlačítka kázání -->
    <div class="kazani-container w-full max-w-7xl mx-auto my-8 p-4 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php
        // Zkontrolujeme, zda jsou data k dispozici v databázi
        if ( empty($csv_data) ) {
            echo '<p class="col-span-full text-center text-white bg-orange-600 p-4 rounded-lg">Data o kázáních nebyla nalezena. Prosím, načtěte je v administraci webu v sekci "Kázání".</p>';
        } else {
            // Převedeme CSV data na pole řádků
            $rows = str_getcsv( $csv_data, "\n" ); 
            
            // Z pole odstraníme první řádek (hlavičku)
            array_shift($rows);

            // Obrátíme pořadí, aby novější byly první
            $rows = array_reverse($rows);

            // Projdeme všechny řádky
            foreach ( $rows as $row ) {
                $data = str_getcsv( $row, "," );
                
                $nazev_kazani = isset($data[0]) ? htmlspecialchars($data[0]) : 'Bez názvu';
                $citace = isset($data[1]) ? htmlspecialchars($data[1]) : '';
                $verse = isset($data[2]) ? htmlspecialchars($data[2]) : '';
                $url_tag = isset($data[3]) ? htmlspecialchars($data[3]) : '';

                if (empty($url_tag)) continue; // Přeskočíme prázdné řádky

                $final_mp3_url = $base_mp3_url . $url_tag . '.mp3';
                ?>
                <!-- Tlačítko pro otevření modálního okna -->
                <div class="w-full">
                    <button 
                        class="open-modal-btn w-full h-20 p-3 flex justify-center items-center text-center bg-[#b7a99a] text-[#514332] font-normal rounded-xl hover:bg-[#9b8f84] focus:outline-none focus:ring-4 focus:ring-[#d3c7bb] ring-2 ring-white ring-inset transition-colors duration-200"
                        data-title="<?php echo $nazev_kazani; ?>"
                        data-citace="<?php echo $citace; ?>"
                        data-verse="<?php echo $verse; ?>"
                        data-mp3="<?php echo $final_mp3_url; ?>"
                    >
                        <span class="leading-tight"><?php echo $nazev_kazani; ?></span>
                    </button>
                </div>
                <?php
            }
        }
        ?>
    </div>
</main>

<!-- =============================================================== -->
<!-- ===========   Struktura Modálního Okna (začátek)   ============ -->
<!-- =============================================================== -->
<div id="kazani-modal-overlay" class="modal-overlay hidden">
    <div id="kazani-modal-container" class="modal-container">
        <!-- Hlavička modálního okna s názvem a zavíracím tlačítkem -->
        <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-300">
            <h2 id="modal-title" class="text-xl font-bold text-[#514332]" style="font-family: 'Akaya Kanadaka', cursive;"></h2>
            <button id="modal-close-btn" class="text-gray-500 hover:text-gray-800 text-3xl font-bold">&times;</button>
        </div>

        <!-- Obsah modálního okna -->
        <div id="modal-content" class="modal-content-area">
            <p id="modal-citace" class="text-gray-600 mb-2 font-semibold"></p>
            <p id="modal-verse" class="text-gray-800 leading-relaxed mb-4"></p>
            
            <!-- Vlastní audio přehrávač -->
            <div class="audio-player-container">
                <audio id="modal-audio-element" src="" preload="none"></audio>
                <button id="modal-play-pause-button" class="bg-[#b7a99a] p-2 rounded-full text-[#514332] shadow-md hover:bg-[#9b8f84] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#d3c7bb]">
                    <svg id="modal-play-icon" class="play-icon audio-player-button-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#514332"><path d="M8 5v14l11-7z" /></svg>
                    <svg id="modal-pause-icon" class="pause-icon audio-player-button-icon hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#514332"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" /></svg>
                </button>
                <div class="progress-bar-container flex-grow bg-[#b7a99a] rounded-full h-2 cursor-pointer">
                    <div id="modal-progress-bar" class="bg-[#514332] h-2 rounded-full transition-all duration-100" style="width: 0%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- =============================================================== -->
<!-- ============    Struktura Modálního Okna (konec)   ============ -->
<!-- =============================================================== -->


<?php
get_footer(); // Načte patičku šablony
?>
