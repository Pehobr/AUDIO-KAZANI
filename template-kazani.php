<?php
/**
 * Template Name: Kázání
 *
 * Šablona pro stránku s audio kázáními, která se otevírají v modálním okně.
 * Verze: 6.0 - Přidána funkce "Načíst další".
 */

get_header(); // Načte hlavičku šablony

// --- NASTAVENÍ ---
$csv_data = get_option('kazani_data_csv');
$base_mp3_url = 'https://audiokostel.cz/audio-kazani/final/';
// --- KONEC NASTAVENÍ ---

?>

<main id="primary" class="site-main">
    
    <div class="w-full max-w-7xl mx-auto flex justify-between items-center px-4 py-2">
        
        <a href="/kazani-podcast/" class="text-white hover:text-gray-300 transition-colors hide-on-mobile" title="Přejít na stránku podcastu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
            </svg>
        </a>

        <h1 class="flex-grow text-2xl md:text-3xl lg:text-4xl text-white text-center mx-2" style="font-family: 'Marck Script', cursive;">
            Inspirace Božího slova
        </h1>

        <a href="/kazani-pdf/" class="text-white hover:text-gray-300 transition-colors hide-on-mobile" title="Zobrazit kázání v PDF">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </a>
    </div>
    <div class="kazani-container w-full max-w-7xl mx-auto my-1 p-4 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php
        if ( empty($csv_data) ) {
            echo '<p class="col-span-full text-center text-white bg-orange-600 p-4 rounded-lg">Data o kázáních nebyla nalezena. Prosím, načtěte je v administraci webu v sekci "Kázání".</p>';
        } else {
            $all_rows = [];
            $handle = fopen('php://memory', 'r+');
            fwrite($handle, $csv_data);
            rewind($handle);
            fgetcsv($handle, 1000, ","); // Přeskočení hlavičky

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $all_rows[] = $data;
            }
            fclose($handle);

            $rows_reversed = array_reverse($all_rows);

            foreach ( $rows_reversed as $data ) {
                if (count($data) < 4 || empty(trim($data[3]))) continue;
                
                $nazev_kazani = htmlspecialchars($data[0]);
                $citace = htmlspecialchars($data[1]);
                $verse = htmlspecialchars($data[2]);
                $url_tag = htmlspecialchars($data[3]);
                $final_mp3_url = $base_mp3_url . trim($url_tag) . '.mp3';
                ?>
                <div class="w-full kazani-item">
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

    <!-- Tlačítko pro načtení dalších kázání -->
    <div class="w-full flex justify-center mt-4 mb-8">
        <button id="load-more-btn" class="bg-[#b7a99a] text-[#514332] font-bold py-3 px-8 rounded-xl hover:bg-[#9b8f84] focus:outline-none focus:ring-4 focus:ring-[#d3c7bb] ring-2 ring-white ring-inset transition-colors duration-200 shadow-lg">
            Načíst starší kázání
        </button>
    </div>

</main>

<div id="kazani-modal-overlay" class="modal-overlay hidden">
    <div id="kazani-modal-container" class="modal-container">
        
        <div class="modal-header flex justify-between items-center pb-2 mb-3 border-b border-gray-300">
            <p id="modal-citace" class="text-gray-700 font-semibold text-lg" style="font-family: 'Playfair Display', serif;"></p>
            <button id="modal-close-btn" class="text-gray-500 hover:text-gray-800 text-3xl font-bold leading-none -mt-1">&times;</button>
        </div>

        <div id="modal-content" class="modal-content-area">
            <h2 id="modal-title" class="hidden"></h2>
            <p id="modal-verse" class="text-gray-800 leading-relaxed mb-4"></p>
            
            <div class="audio-player-container">
                <audio id="modal-audio-element" src="" preload="none"></audio>
                <button id="modal-play-pause-button" class="text-[#514332] transition-colors duration-200 focus:outline-none">
                    <svg id="modal-play-icon" class="play-icon audio-player-button-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#514332"><path d="M8 5v14l11-7z" /></svg>
                    <svg id="modal-pause-icon" class="pause-icon audio-player-button-icon hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#514332"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" /></svg>
                </button>
                <div class="progress-bar-container flex-grow bg-[#b7a99a] rounded-full h-2 cursor-pointer mr-4">
                    <div id="modal-progress-bar" class="bg-[#514332] h-2 rounded-full transition-all duration-100" style="width: 0%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer(); // Načte patičku šablony (včetně nové mobilní lišty)
?>
