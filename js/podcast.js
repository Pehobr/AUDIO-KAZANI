document.addEventListener('DOMContentLoaded', () => {
    const episodeCards = document.querySelectorAll('.episode-card');
    let currentAudio = null;
    let currentPlayingCard = null;

    // Funkce pro formátování času z sekund na MM:SS
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    episodeCards.forEach(card => {
        const playPauseBtn = card.querySelector('.play-pause-btn');
        const playIcon = card.querySelector('.play-icon');
        const pauseIcon = card.querySelector('.pause-icon');
        const progressBar = card.querySelector('.progress-bar');
        const progressBarWrapper = card.querySelector('.progress-bar-wrapper');
        const currentTimeEl = card.querySelector('.current-time');
        const durationEl = card.querySelector('.duration');
        const mp3Url = card.dataset.mp3Url;

        const audio = new Audio(mp3Url);

        // Po načtení metadat audia nastavíme jeho celkovou délku
        audio.addEventListener('loadedmetadata', () => {
            durationEl.textContent = formatTime(audio.duration);
        });

        // Aktualizace progress baru a času během přehrávání
        audio.addEventListener('timeupdate', () => {
            const progressPercent = (audio.currentTime / audio.duration) * 100;
            progressBar.style.width = `${progressPercent}%`;
            currentTimeEl.textContent = formatTime(audio.currentTime);
        });

        // Po skončení přehrávání resetujeme UI
        audio.addEventListener('ended', () => {
            resetCardUI(card);
            currentAudio = null;
            currentPlayingCard = null;
        });

        // Kliknutí na tlačítko Play/Pause
        playPauseBtn.addEventListener('click', () => {
            if (currentAudio && currentAudio !== audio) {
                // Pokud hraje jiná epizoda, zastavíme ji
                currentAudio.pause();
                resetCardUI(currentPlayingCard);
            }

            currentAudio = audio;
            currentPlayingCard = card;

            if (audio.paused) {
                audio.play();
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
                card.classList.add('is-playing');
                card.classList.remove('is-paused');
            } else {
                audio.pause();
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
                card.classList.remove('is-playing');
                card.classList.add('is-paused');
            }
        });

        // Přetáčení kliknutím na progress bar
        progressBarWrapper.addEventListener('click', (e) => {
            const rect = progressBarWrapper.getBoundingClientRect();
            const clickPosition = e.clientX - rect.left;
            const percentage = clickPosition / rect.width;
            if (isFinite(audio.duration)) {
                audio.currentTime = percentage * audio.duration;
            }
        });
    });

    // Funkce pro resetování vzhledu karty
    function resetCardUI(card) {
        if (!card) return;
        card.querySelector('.play-icon').classList.remove('hidden');
        card.querySelector('.pause-icon').classList.add('hidden');
        card.classList.remove('is-playing');
        card.classList.remove('is-paused');
        card.querySelector('.progress-bar').style.width = '0%';
        card.querySelector('.current-time').textContent = '00:00';
    }
});
