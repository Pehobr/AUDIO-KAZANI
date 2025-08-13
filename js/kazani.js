// kazani.js
// Tento soubor obsahuje JavaScript pro stránku kazani.php

document.addEventListener('DOMContentLoaded', () => {
    // Najdeme VŠECHNY accordion boxy na stránce
    const accordions = document.querySelectorAll('.accordion-toggle');

    // Pro každý accordion box přidáme posluchač události
    accordions.forEach(accordion => {
        accordion.addEventListener('click', () => {
            // Najdeme obsah a šipku, které patří k tomuto konkrétnímu tlačítku
            const content = accordion.nextElementSibling;
            const arrow = accordion.querySelector('.arrow-icon');

            // Přepneme viditelnost obsahu a rotaci šipky
            content.classList.toggle('open');
            arrow.classList.toggle('rotate-180');
        });
    });

    // Najdeme VŠECHNY přehrávače na stránce
    const players = document.querySelectorAll('.audio-player-container');

    players.forEach(player => {
        // Najdeme všechny potřebné prvky uvnitř jednoho přehrávače
        const audioElement = player.querySelector('.audio-element');
        const playPauseButton = player.querySelector('.play-pause-button');
        const playIcon = player.querySelector('.play-icon');
        const pauseIcon = player.querySelector('.pause-icon');
        const progressBarContainer = player.querySelector('.progress-bar-container');
        const progressBar = player.querySelector('.progress-bar');

        // Funkce pro přepínání přehrávání/pauzy
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

        // Aktualizace lišty průběhu
        audioElement.addEventListener('timeupdate', () => {
            const progress = (audioElement.currentTime / audioElement.duration) * 100;
            progressBar.style.width = `${progress || 0}%`;
        });

        // Reset přehrávače po skončení
        audioElement.addEventListener('ended', () => {
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
            progressBar.style.width = '0%';
            audioElement.currentTime = 0;
        });

        // Přetáčení kliknutím na lištu
        progressBarContainer.addEventListener('click', (e) => {
            const rect = progressBarContainer.getBoundingClientRect();
            const pos = (e.clientX - rect.left) / rect.width;
            if (isFinite(audioElement.duration)) {
                audioElement.currentTime = pos * audioElement.duration;
            }
        });
    });
});
