<?php
/**
 * Template Name: Kázání
 *
 * Šablona pro stránku s audio kázáními načítanými z Google Sheets.
 */

get_header(); // Načte hlavičku šablony

// --- NASTAVENÍ ---
// Zde vložte odkaz na vaši Google tabulku ve formátu CSV
$google_sheet_csv_url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vR_UZtCZfhxPg2kkkDn8n-cSuAzIZL8vVDAek37jtEviNQ0RpcBC4modkap85KZh4wvb7nh-ZDbTBNl/pub?gid=0&single=true&output=csv';

// Zde vložte základní část URL adresy pro vaše MP3 soubory
$base_mp3_url = 'https://audiokostel.cz/audio-kazani/';
// --- KONEC NASTAVENÍ ---

?>

<main id="primary" class="site-main">
    <div class="kazani-container w-full max-w-xl mx-auto my-8 space-y-4">
        <?php
        // Pokusíme se načíst data z Google Sheets
        $csv_data = @file_get_contents( $google_sheet_csv_url );

        if ( $csv_data === false ) {
            // Pokud se data nepodaří načíst, zobrazíme chybovou hlášku
            echo '<p class="text-center text-white bg-red-600 p-4 rounded-lg">Nepodařilo se načíst data z Google Tabulky. Zkontrolujte prosím URL adresu v šabloně.</p>';
        } else {
            // Převedeme CSV data na pole řádků
            $rows = str_getcsv( $csv_data, "\n" ); 
            
            // Projdeme všechny řádky
            foreach ( $rows as $index => $row ) {
                // Přeskočíme první řádek (hlavičku tabulky)
                if ( $index === 0 ) continue;

                // Rozdělíme řádek na jednotlivé buňky
                $data = str_getcsv( $row, "," );

                // Pro přehlednost si data uložíme do proměnných
                $nazev_kazani = isset($data[0]) ? htmlspecialchars($data[0]) : 'Bez názvu';
                $citace = isset($data[1]) ? htmlspecialchars($data[1]) : '';
                $verse = isset($data[2]) ? htmlspecialchars($data[2]) : '';
                $url_tag = isset($data[3]) ? htmlspecialchars($data[3]) : '';

                // Pokud řádek nemá tag, přeskočíme ho
                if (empty($url_tag)) continue;

                // Sestavíme kompletní URL k MP3 souboru (PŘIDÁNA KONCOVKA .mp3)
                $final_mp3_url = $base_mp3_url . $url_tag . '.mp3';

                // Vygenerujeme HTML pro každý záznam (tlačítko)
                ?>
                <div class="w-full bg-[#f1eeea] rounded-xl shadow-lg overflow-hidden">
                    <!-- Tlačítko pro rozbalení/sbalení -->
                    <button class="accordion-toggle w-full p-4 flex justify-between items-center bg-[#b7a99a] text-[#514332] uppercase font-bold text-lg tracking-widest rounded-xl hover:bg-[#9b8f84] focus:outline-none focus:ring-4 focus:ring-[#d3c7bb] ring-2 ring-white ring-inset">
                        <span><?php echo $nazev_kazani; ?></span>
                        <svg class="arrow-icon w-6 h-6 transform transition-transform duration-300" fill="none" stroke="#514332" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Obsah, který se zobrazí po kliknutí -->
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
