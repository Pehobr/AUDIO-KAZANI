<?php
/**
 * Template Name: Kázání
 *
 * Šablona pro stránku s audio kázáními načítanými z databáze WordPressu.
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
    
    <!-- Hlavní nadpis stránky -->
    <h1 class="text-2xl text-white text-center my-8" style="font-family: 'Marck Script', cursive;">
        Inspirace Božího slova
    </h1>

    <div class="kazani-container w-full max-w-xl mx-auto my-8 space-y-4">
        <?php
        // Zkontrolujeme, zda jsou data k dispozici v databázi
        if ( empty($csv_data) ) {
            echo '<p class="text-center text-white bg-orange-600 p-4 rounded-lg">Data o kázáních nebyla nalezena. Prosím, načtěte je v administraci webu v sekci "Kázání".</p>';
        } else {
            // Převedeme CSV data na pole řádků
            $rows = str_getcsv( $csv_data, "\n" ); 
            
            // Z pole odstraníme první řádek (hlavičku), abychom s ním dále nepracovali.
            array_shift($rows);

            // Obrátíme pořadí zbývajících řádků, aby novější byly první.
            $rows = array_reverse($rows);

            // Projdeme všechny řádky v novém, obráceném pořadí.
            foreach ( $rows as $row ) {
                // Kontrola indexu pro přeskočení hlavičky již není potřeba.
                $data = str_getcsv( $row, "," );
                
                $nazev_kazani = isset($data[0]) ? htmlspecialchars($data[0]) : 'Bez názvu';
                $citace = isset($data[1]) ? htmlspecialchars($data[1]) : '';
                $verse = isset($data[2]) ? htmlspecialchars($data[2]) : '';
                $url_tag = isset($data[3]) ? htmlspecialchars($data[3]) : '';

                if (empty($url_tag)) continue; // Přeskočíme prázdné řádky

                // OPRAVA: Přidán chybějící znak $ k proměnné
                $final_mp3_url = $base_mp3_url . $url_tag . '.mp3';
                ?>
                <div class="w-full bg-[#f1eeea] rounded-xl shadow-lg overflow-hidden">
                    <button class="accordion-toggle w-full p-3 flex justify-between items-center bg-[#b7a99a] text-[#514332] font-normal rounded-xl hover:bg-[#9b8f84] focus:outline-none focus:ring-4 focus:ring-[#d3c7bb] ring-2 ring-white ring-inset">
                        <span><?php echo $nazev_kazani; ?></span>
                        <svg class="arrow-icon w-6 h-6 transform transition-transform duration-300" fill="none" stroke="#514332" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="accordion-content">
                        <div class="p-4">
                            <p class="text-gray-600 mb-2 font-semibold"><?php echo $citace; ?></p>
                            <p class="text-gray-800 leading-relaxed mb-4"><?php echo $verse; ?></p>
                            <div class="audio-player-container">
                                <audio class="audio-element" src="<?php echo $final_mp3_url; ?>" preload="none"></audio>
                                <button class="play-pause-button bg-[#b7a99a] p-2 rounded-full text-[#514332] shadow-md hover:bg-[#9b8f84] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#d3c7bb]">
                                    <svg class="play-icon audio-player-button-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#514332"><path d="M8 5v14l11-7z" /></svg>
                                    <svg class="pause-icon audio-player-button-icon hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#514332"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" /></svg>
                                </button>
                                <div class="progress-bar-container flex-grow bg-[#b7a99a] rounded-full h-2 cursor-pointer">
                                    <div class="progress-bar bg-[#514332] h-2 rounded-full transition-all duration-100" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</main><!-- #main -->

<?php
get_footer(); // Načte patičku šablony
