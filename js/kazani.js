// kazani.js
// Tento soubor obsahuje JavaScript pro stránku kazani.php
// Verze: 1.2 - Oprava pro otevírání pouze jednoho akordeonu najednou

document.addEventListener('DOMContentLoaded', () => {
    // Najdeme VŠECHNY accordion boxy na stránce
    const allAccordions = document.querySelectorAll('.accordion-toggle');

    allAccordions.forEach(clickedAccordion => {
        clickedAccordion.addEventListener('click', () => {
            // Cíl: Otevřít kliknutý akordeon a zavřít všechny ostatní.

            const contentToToggle = clickedAccordion.nextElementSibling;
            const arrowToToggle = clickedAccordion.querySelector('.arrow-icon');
            
            // Zjistíme, jestli je kliknutý akordeon právě otevřený, NEŽ cokoliv změníme.
            const isCurrentlyOpen = contentToToggle.classList.contains('open');

            // 1. Nejprve zavřeme VŠECHNY akordeony bez výjimky.
            allAccordions.forEach(accordion => {
                const content = accordion.nextElementSibling;
                const arrow = accordion.querySelector('.arrow-icon');
                
                // Odstraníme třídy, které způsobují zobrazení obsahu a rotaci šipky.
                content.classList.remove('open');
                arrow.classList.remove('rotate-180');
            });

            // 2. Pokud kliknutý akordeon PŮVODNĚ NEBYL otevřený, tak ho nyní otevřeme.
            //    Pokud už otevřený BYL, tak po kroku 1 zůstane zavřený, což je správně.
            if (!isCurrentlyOpen) {
                contentToToggle.classList.add('open');
                arrowToToggle.classList.add('rotate-180');
            }
        });
    });

    // --- Kód pro audio přehrávač (zůstává beze změny) ---
    const players = document.querySelectorAll('.audio-player-container');

    players.forEach(player => {
        const audioElement = player.querySelector('.audio-element');
        const playPauseButton = player.querySelector('.play-pause-button');
        const playIcon = player.querySelector('.play-icon');
        const pauseIcon = player.querySelector('.pause-icon');
        const progressBarContainer = player.querySelector('.progress-bar-container');
        const progressBar = player.querySelector('.progress-bar');

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

        audioElement.addEventListener('ended', () => {
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
            progressBar.style.width = '0%';
            audioElement.currentTime = 0;
        });

        progressBarContainer.addEventListener('click', (e) => {
            const rect = progressBarContainer.getBoundingClientRect();
            const pos = (e.clientX - rect.left) / rect.width;
            if (isFinite(audioElement.duration)) {
                audioElement.currentTime = pos * audioElement.duration;
            }
        });
    });
});
