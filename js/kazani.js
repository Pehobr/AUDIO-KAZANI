// kazani.js
// Tento soubor obsahuje JavaScript pro stránku s kázáními.
// Verze: 2.1 - Přidána logika pro modální okno a "Načíst další".

document.addEventListener('DOMContentLoaded', () => {
    // --- Logika pro modální okno ---
    const modalOverlay = document.getElementById('kazani-modal-overlay');
    
    // Pouze pokud je na stránce modální okno, připojíme jeho logiku
    if (modalOverlay) {
        const modalContainer = document.getElementById('kazani-modal-container');
        const modalCloseBtn = document.getElementById('modal-close-btn');
        const openModalButtons = document.querySelectorAll('.open-modal-btn');
        const modalTitle = document.getElementById('modal-title');
        const modalCitace = document.getElementById('modal-citace');
        const modalVerse = document.getElementById('modal-verse');
        const audioElement = document.getElementById('modal-audio-element');
        const playPauseButton = document.getElementById('modal-play-pause-button');
        const playIcon = document.getElementById('modal-play-icon');
        const pauseIcon = document.getElementById('modal-pause-icon');
        const progressBarContainer = document.querySelector('#kazani-modal-container .progress-bar-container');
        const progressBar = document.getElementById('modal-progress-bar');

        const openModal = (title, citace, verse, mp3) => {
            modalTitle.textContent = title;
            modalCitace.textContent = citace;
            modalVerse.innerHTML = verse.replace(/\n/g, '<br>');
            audioElement.src = mp3;
            resetPlayer();
            modalOverlay.classList.remove('hidden');
            setTimeout(() => modalOverlay.classList.add('visible'), 10);
        };

        const closeModal = () => {
            audioElement.pause();
            modalOverlay.classList.remove('visible');
            setTimeout(() => {
                modalOverlay.classList.add('hidden');
                audioElement.src = ""; 
            }, 300);
        };
        
        const resetPlayer = () => {
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
            progressBar.style.width = '0%';
            audioElement.currentTime = 0;
        };

        openModalButtons.forEach(button => {
            button.addEventListener('click', () => {
                const data = button.dataset;
                openModal(data.title, data.citace, data.verse, data.mp3);
            });
        });

        modalCloseBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modalOverlay.classList.contains('visible')) {
                closeModal();
            }
        });

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

        audioElement.addEventListener('timeupdate', () => {
            const progress = (audioElement.currentTime / audioElement.duration) * 100;
            progressBar.style.width = `${progress || 0}%`;
        });

        audioElement.addEventListener('ended', resetPlayer);

        if (progressBarContainer) {
            progressBarContainer.addEventListener('click', (e) => {
                const rect = progressBarContainer.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                if (isFinite(audioElement.duration)) {
                    audioElement.currentTime = pos * audioElement.duration;
                }
            });
        }
    }

    // --- Logika pro "Načíst další" ---
    const loadMoreBtn = document.getElementById('load-more-btn');
    const sermons = document.querySelectorAll('.kazani-item');
    const itemsPerPage = 12;
    let visibleItems = itemsPerPage;

    // Pokud není tlačítko na stránce nebo nejsou žádná kázání, nic nedělat
    if (loadMoreBtn && sermons.length > 0) {
        
        // Zobrazit prvních 12 a skrýt zbytek
        sermons.forEach((sermon, index) => {
            if (index >= itemsPerPage) {
                sermon.style.display = 'none';
            }
        });

        // Skrýt tlačítko, pokud je kázání 12 nebo méně
        if (sermons.length <= itemsPerPage) {
            loadMoreBtn.style.display = 'none';
        }

        // Při kliknutí na tlačítko
        loadMoreBtn.addEventListener('click', () => {
            const nextVisibleItems = visibleItems + itemsPerPage;

            // Zobrazit dalších 12 kázání
            for (let i = visibleItems; i < nextVisibleItems && i < sermons.length; i++) {
                sermons[i].style.display = 'block';
            }

            visibleItems = nextVisibleItems;

            // Skrýt tlačítko, pokud už nejsou žádná další kázání k zobrazení
            if (visibleItems >= sermons.length) {
                loadMoreBtn.style.display = 'none';
            }
        });
    }
});
