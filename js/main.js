// Initialisation des fonctions
import { initHeaderBehaviour } from "./header.js";
import { initOpenDteailMenu, initCloseDteailMenu } from "./detail-menu.js";
import { initScrollReveal } from "./scroll-reveal.js";

document.addEventListener("DOMContentLoaded", () => {
    initHeaderBehaviour();
    initOpenDteailMenu();
    initCloseDteailMenu();
    initScrollReveal();
})

// Paralax effect
window.addEventListener('scroll', function () {
    const parallax = document.querySelector('.parallax');
    let scrollPosition = window.pageYOffset;

    if (parallax != null) {
        parallax.style.transform = 'translateY(' + scrollPosition * .6 + 'px)';
    }
});