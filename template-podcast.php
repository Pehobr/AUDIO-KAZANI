<?php
/**
 * Template Name: Podcast
 *
 * Šablona pro stránku s přehrávačem podcast epizod.
 * @version 1.4 - Skrytí nadpisu na mobilních zařízeních
 */

get_header();

// --- NASTAVENÍ (stejné jako v RSS feedu) ---
$podcast_title = 'Inspirace Božího slova'; 
$podcast_description = 'AI zpracování kázání z kostela sv. Alžběty Durynské ve Vnorovech.'; 
$podcast_image_url = 'https://audiokostel.cz/audio-kazani/final/ikona-podcast.png'; 
$base_mp3_url = 'https://audiokostel.cz/audio-kazani/final/';
// --- KONEC NASTAVENÍ ---

// Načtení dat z databáze
$csv_data = get_option('kazani_data_csv');
?>

<main id="primary" class="site-main podcast-page-container py-8 md:py-12">
    <div class="w-full max-w-4xl mx-auto px-4">

        <header class="podcast-header flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-6 mb-10">
            <img src="<?php echo esc_url($podcast_image_url); ?>" alt="Cover podcastu <?php echo esc_attr($podcast_title); ?>" class="w-32 h-32 md:w-40 md:h-40 rounded-lg shadow-lg object-cover">
            <div class="flex-1">
                <h1 class="hidden sm:block text-3xl md:text-4xl font-bold" style="font-family: 'Playfair Display', serif;"><?php echo esc_html($podcast_title); ?></h1>
                <p class="mt-2 text-lg"><?php echo esc_html($podcast_description); ?></p>
                
                <div class="subscribe-buttons mt-4 flex justify-center sm:justify-start flex-wrap gap-3">
                    <a href="<?php echo esc_url(get_site_url(null, 'podcast')); ?>" target="_blank" class="subscribe-btn rss">RSS Feed</a>
                </div>
            </div>
        </header>

        <div class="episode-list space-y-4">
            <?php
            if ( !empty($csv_data) ) {
                $all_rows = [];
                $handle = fopen('php://memory', 'r+');
                fwrite($handle, $csv_data);
                rewind($handle);
                fgetcsv($handle); // Přeskočit hlavičku

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (isset($data[4]) && !empty(trim($data[4]))) {
                        $all_rows[] = $data;
                    }
                }
                fclose($handle);

                // Seřazení epizod od nejnovější
                usort($all_rows, function($a, $b) {
                    $date_a = DateTime::createFromFormat('d.m.Y', $a[4]);
                    $date_b = DateTime::createFromFormat('d.m.Y', $b[4]);
                    return ($date_a && $date_b) ? ($date_b <=> $date_a) : 0;
                });

                foreach ($all_rows as $data) {
                    if (count($data) < 5) continue;

                    $nazev_kazani = htmlspecialchars(trim($data[0]));
                    $citace       = htmlspecialchars(trim($data[1]));
                    $verse        = htmlspecialchars(trim($data[2]));
                    $url_tag      = trim($data[3]);
                    $datum_text   = htmlspecialchars(trim($data[4]));
                    $final_mp3_url = $base_mp3_url . $url_tag . '.mp3';
                    ?>
                    <article class="episode-card p-4 rounded-lg shadow-sm border" data-mp3-url="<?php echo esc_url($final_mp3_url); ?>">
                        <div class="flex items-center gap-4">
                            <button class="play-pause-btn flex-shrink-0 w-12 h-12 rounded-full text-white flex items-center justify-center transition-colors">
                                <svg class="play-icon w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                <svg class="pause-icon w-6 h-6 hidden" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                            </button>
                            <div class="flex-1">
                                <p class="text-sm"><?php echo $datum_text; ?></p>
                                <h3 class="font-semibold"><?php echo $nazev_kazani; ?></h3>
                                <p class="episode-summary mt-1"><?php echo $verse; ?></p>
                            </div>
                        </div>
                        <div class="player-controls">
                             <div class="progress-bar-wrapper w-full h-2 rounded-full cursor-pointer">
                                <div class="progress-bar h-full rounded-full" style="width: 0%;"></div>
                            </div>
                            <div class="time-display flex justify-between text-xs mt-1">
                                <span class="current-time">00:00</span>
                                <span class="duration">00:00</span>
                            </div>
                        </div>
                    </article>
                    <?php
                }
            } else {
                echo '<p class="text-center bg-gray-100 p-4 rounded-lg">Nebyly nalezeny žádné epizody. Zkuste je načíst v administraci.</p>';
            }
            ?>
        </div>
    </div>
</main>

<?php
get_footer();
?>