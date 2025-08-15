<?php
/**
 * Generátor RSS Feedu pro Podcast Kázání
 *
 * Tento soubor načte data o kázáních z WordPress databáze 
 * a vygeneruje z nich validní RSS 2.0 feed s iTunes-specifickými tagy
 * pro kompatibilitu s podcastovými aplikacemi.
 *
 * @version 1.1
 */

// Načtení WordPress prostředí. Cesta musí být správně nastavena.
// Předpokládáme, že tento soubor je v kořenovém adresáři šablony.
require_once( realpath( dirname( __FILE__ ) . '/../../../wp-load.php' ) );

// --- ZÁKLADNÍ NASTAVENÍ PODCASTU (UPRAVTE PODLE POTŘEBY) ---

// Název vašeho podcastu
$podcast_title = 'Inspirace Božího slova'; 
// Popis vašeho podcastu
$podcast_description = 'AI zpracování kázání  kostela sv. Alžběty Durynské ve Vnorovech.'; 
// URL adresa webu, kde jsou kázání dostupná
$podcast_link = get_site_url(); 
// Jazyk podcastu (cs-CZ pro češtinu)
$podcast_language = 'cs-CZ'; 
// Copyright
$podcast_copyright = '&#xA9; ' . date('Y') . ' Římskokatolická farnost Vnorovy'; 
// URL adresa obrázku podcastu (čtvercový, doporučeno 3000x3000px, JPG nebo PNG)
$podcast_image_url = 'https://audiokostel.cz/audio-kazani/final/ikona-podcast.png'; 
// Jméno autora/kazatele
$podcast_author = 'Farnost Vnorovy'; 
// Kategorie podcastu (viz Apple Podcasts kategorie)
$podcast_category = 'Katolická církev'; 
// Explicitní obsah (yes/no)
$podcast_explicit = 'no'; 
// Základní URL pro MP3 soubory
$base_mp3_url = 'https://audiokostel.cz/audio-kazani/final/';

// --- KONEC NASTAVENÍ ---


// Načtení uložených CSV dat z databáze
$csv_data = get_option('kazani_data_csv');

// Nastavení hlavičky pro XML feed
header('Content-Type: application/rss+xml; charset=utf-8');

// Začátek XML dokumentu
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0"
    xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title><?php echo htmlspecialchars($podcast_title); ?></title>
    <link><?php echo htmlspecialchars($podcast_link); ?></link>
    <language><?php echo $podcast_language; ?></language>
    <copyright><?php echo $podcast_copyright; ?></copyright>
    <description><![CDATA[<?php echo $podcast_description; ?>]]></description>
    <atom:link href="<?php echo htmlspecialchars( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ); ?>" rel="self" type="application/rss+xml" />

    <!-- iTunes specifické tagy -->
    <itunes:author><?php echo htmlspecialchars($podcast_author); ?></itunes:author>
    <itunes:summary><![CDATA[<?php echo $podcast_description; ?>]]></itunes:summary>
    <itunes:owner>
        <itunes:name><?php echo htmlspecialchars($podcast_author); ?></itunes:name>
        <itunes:email>priklad@email.com</itunes:email> <!-- Doplňte kontaktní email -->
    </itunes:owner>
    <itunes:image href="<?php echo htmlspecialchars($podcast_image_url); ?>" />
    <itunes:category text="<?php echo htmlspecialchars($podcast_category); ?>"/>
    <itunes:explicit><?php echo $podcast_explicit; ?></itunes:explicit>

<?php
// Zpracování CSV dat, pokud existují
if ( !empty($csv_data) ) {
    $all_rows = [];
    $handle = fopen('php://memory', 'r+');
    fwrite($handle, $csv_data);
    rewind($handle);

    // Přeskočení hlavičky
    fgetcsv($handle, 1000, ",");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Přidáme řádek do pole pouze pokud obsahuje alespoň 5 sloupců a 5. sloupec (datum) není prázdný
        if (isset($data[4]) && !empty(trim($data[4]))) {
            $all_rows[] = $data;
        }
    }
    fclose($handle);

    // Seřazení epizod sestupně podle data (nejnovější první)
    // Předpokládá formát data DD.MM.YYYY v 5. sloupci ($data[4])
    usort($all_rows, function($a, $b) {
        $date_a = DateTime::createFromFormat('d.m.Y', $a[4]);
        $date_b = DateTime::createFromFormat('d.m.Y', $b[4]);
        if ($date_a === false || $date_b === false) return 0;
        return $date_b <=> $date_a;
    });

    // Projdeme seřazené řádky a vygenerujeme položky <item>
    foreach ( $all_rows as $data ) {
        // Přeskočíme řádky, které nemají dostatek dat
        if (count($data) < 5) continue;

        $nazev_kazani = isset($data[0]) ? htmlspecialchars(trim($data[0])) : 'Bez názvu';
        $citace       = isset($data[1]) ? htmlspecialchars(trim($data[1])) : '';
        $verse        = isset($data[2]) ? htmlspecialchars(trim($data[2])) : '';
        $url_tag      = isset($data[3]) ? trim($data[3]) : '';
        $datum_text   = isset($data[4]) ? trim($data[4]) : '';

        // Přeskočíme epizody bez URL tagu nebo data
        if (empty($url_tag) || empty($datum_text)) continue;

        // Finální URL adresa MP3 souboru
        $final_mp3_url = $base_mp3_url . $url_tag . '.mp3';

        // Převedení data do formátu RFC 2822, který RSS vyžaduje
        $date_obj = DateTime::createFromFormat('d.m.Y', $datum_text);
        $pub_date = ($date_obj) ? $date_obj->format(DateTime::RFC2822) : date(DateTime::RFC2822);

        // Vytvoření popisu epizody
        $item_description = $citace . "\n\n" . $verse;
?>
    <item>
        <title><?php echo $nazev_kazani; ?></title>
        <description><![CDATA[<?php echo nl2br(htmlspecialchars($item_description)); ?>]]></description>
        <pubDate><?php echo $pub_date; ?></pubDate>
        <enclosure url="<?php echo htmlspecialchars($final_mp3_url); ?>" type="audio/mpeg" length="0" />
        <guid isPermaLink="false"><?php echo htmlspecialchars($final_mp3_url); ?></guid>
        
        <!-- iTunes specifické tagy pro epizodu -->
        <itunes:author><?php echo htmlspecialchars($podcast_author); ?></itunes:author>
        <itunes:summary><![CDATA[<?php echo htmlspecialchars($nazev_kazani); ?>]]></itunes:summary>
        <itunes:explicit><?php echo $podcast_explicit; ?></itunes:explicit>
    </item>
<?php
    }
}
?>
</channel>
</rss>
