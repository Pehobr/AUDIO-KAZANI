<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kázání</title>
    <!-- Načtení Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Načtení vlastních stylů -->
    <link rel="stylesheet" href="kazani.css">
    <!-- Importování Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DynaPuff&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
</head>
<body class="bg-[#514332] flex items-center justify-center min-h-screen p-4">

    <!-- Kontejner pro accordion box -->
    <div class="w-full max-w-xl mx-auto bg-[#f1eeea] rounded-xl shadow-lg overflow-hidden">
        <!-- Tlačítko pro rozbalení/sbalení -->
        <button id="accordion-toggle" class="w-full p-4 flex justify-between items-center bg-[#b7a99a] text-[#514332] uppercase font-bold text-lg tracking-widest rounded-xl hover:bg-[#9b8f84] focus:outline-none focus:ring-4 focus:ring-[#d3c7bb] ring-2 ring-white ring-inset">
            Název kázání (Ukázka)
            <!-- Šipka pro vizuální indikaci -->
            <svg id="arrow-icon" class="w-6 h-6 transform transition-transform duration-300" fill="none" stroke="#514332" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Obsah, který se zobrazí po kliknutí -->
        <div id="accordion-content" class="accordion-content">
            <div class="p-4">
                <!-- Biblická citace -->
                <p class="text-gray-600 mb-2 font-semibold">NT 3:1, 4-6</p>
                <!-- Biblický text -->
                <p class="text-gray-800 leading-relaxed mb-4">
                    Tento verš, druhý verš, třetí verš. A další verš. A tak dále.
                </p>

                <!-- Vlastní audio přehrávač -->
                <div class="audio-player-container">
                    <!-- Skrytý HTML5 audio prvek -->
                    <audio id="audio-element" src="https://www.learningcontainer.com/wp-content/uploads/2020/02/Kalimba.mp3"></audio>
                    
                    <!-- Tlačítko pro přehrávání/pauzu -->
                    <button id="play-pause-button" class="bg-[#b7a99a] p-2 rounded-full text-[#514332] shadow-md hover:bg-[#9b8f84] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#d3c7bb]">
                        <!-- Ikona pro přehrávání -->
                        <svg id="play-icon" xmlns="http://www.w3.org/2000/svg" class="audio-player-button-icon" viewBox="0 0 24 24" fill="#514332">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                        <!-- Ikona pro pauzu -->
                        <svg id="pause-icon" xmlns="http://www.w3.org/2000/svg" class="audio-player-button-icon hidden" viewBox="0 0 24 24" fill="#514332">
                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                        </svg>
                    </button>

                    <!-- Lišta s ukazatelem průběhu -->
                    <div class="flex-grow bg-[#b7a99a] rounded-full h-2">
                        <div id="progress-bar" class="bg-[#514332] h-2 rounded-full transition-all duration-100" style="width: 0%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Načtení vlastního JavaScriptu -->
    <script src="kazani.js"></script>
</body>
</html>
