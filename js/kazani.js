// kazani.js
// Tento soubor obsahuje JavaScript pro stránku s kázáními v modálním okně.
// Verze: 2.0 - Logika pro modální okno

document.addEventListener('DOMContentLoaded', () => {
    // --- Výběr prvků pro modální okno ---
    const modalOverlay = document.getElementById('kazani-modal-overlay');
    // Pokud na stránce není modální okno, nic nedělat (bezpečnostní pojistka)
    if (!modalOverlay) return; 

    const modalContainer = document.getElementById('kazani-modal-container');
    const modalCloseBtn = document.getElementById('modal-close-btn');
    const openModalButtons = document.querySelectorAll('.open-modal-btn');

    // --- Výběr prvků uvnitř modálního okna ---
    const modalTitle = document.getElementById('modal-title');
    const modalCitace = document.getElementById('modal-citace');
    const modalVerse = document.getElementById('modal-verse');
    
    // --- Výběr prvků audio přehrávače v modálním okně ---
    const audioElement = document.getElementById('modal-audio-element');
    const playPauseButton = document.getElementById('modal-play-pause-button');
    const playIcon = document.getElementById('modal-play-icon');
    const pauseIcon = document.getElementById('modal-pause-icon');
    const progressBarContainer = document.querySelector('#kazani-modal-container .progress-bar-container');
    const progressBar = document.getElementById('modal-progress-bar');

    /**
     * Otevře modální okno a naplní ho daty.
     * @param {string} title - Název kázání.
     * @param {string} citace - Biblická citace.
     * @param {string} verse - Biblické verše.
     * @param {string} mp3 - URL adresa audio souboru.
     */
    const openModal = (title, citace, verse, mp3) => {
        // 1. Naplnění modálního okna daty z tlačítka
        modalTitle.textContent = title;
        modalCitace.textContent = citace;
        modalVerse.innerHTML = verse.replace(/\n/g, '<br>'); // Nahradí nové řádky za <br> pro správné zobrazení
        audioElement.src = mp3;

        // 2. Resetování stavu přehrávače
        resetPlayer();

        // 3. Zobrazení modálního okna
        modalOverlay.classList.remove('hidden');
        setTimeout(() => modalOverlay.classList.add('visible'), 10); // Malé zpoždění pro spuštění CSS přechodu
    };

    /**
     * Zavře modální okno a zastaví přehrávání.
     */
    const closeModal = () => {
        // Zastavíme přehrávání audia, aby nehrálo na pozadí
        audioElement.pause();

        modalOverlay.classList.remove('visible');
        // Počkáme na dokončení animace (300ms) a pak skryjeme prvek
        setTimeout(() => {
            modalOverlay.classList.add('hidden');
            // Vyčistíme src, aby se audio zbytečně nestahovalo na pozadí
            audioElement.src = ""; 
        }, 300);
    };
    
    /**
     * Resetuje přehrávač do výchozího stavu.
     */
    const resetPlayer = () => {
        playIcon.classList.remove('hidden');
        pauseIcon.classList.add('hidden');
        progressBar.style.width = '0%';
        audioElement.currentTime = 0;
    };


    // --- Přiřazení událostí ---

    // 1. Otevření modálního okna po kliknutí na tlačítko kázání
    openModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const data = button.dataset;
            openModal(data.title, data.citace, data.verse, data.mp3);
        });
    });

    // 2. Zavření modálního okna
    modalCloseBtn.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (e) => {
        // Zavře se jen při kliknutí na pozadí (overlay), ne na obsah okna (container)
        if (e.target === modalOverlay) {
            closeModal();
        }
    });
    // Zavření modálního okna klávesou Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalOverlay.classList.contains('visible')) {
            closeModal();
        }
    });


    // --- Logika pro audio přehrávač v modálním okně ---

    // Přehrávání / Pauza
    playPauseButton.addEventListener('click', () => {
        if (audioElement.paused) {
            audioElement.play();
            playIcon.classList.add('hidden');
            pauseIcon.classList.remove('hidden');
        } else {
            audioElement.pause();
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
        }
    });

    // Aktualizace progress baru
    audioElement.addEventListener('timeupdate', () => {
        const progress = (audioElement.currentTime / audioElement.duration) * 100;
        progressBar.style.width = `${progress || 0}%`;
    });

    // Po skončení přehrávání
    audioElement.addEventListener('ended', resetPlayer);

    // Přetáčení v progress baru kliknutím
    progressBarContainer.addEventListener('click', (e) => {
        const rect = progressBarContainer.getBoundingClientRect();
        const pos = (e.clientX - rect.left) / rect.width;
        if (isFinite(audioElement.duration)) {
            audioElement.currentTime = pos * audioElement.duration;
        }
    });
});
