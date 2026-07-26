// Initialisation des fonctions
import { initHeaderBehaviour } from "./header.js";
import { initScrollReveal } from "./scroll-reveal.js";
import { initOpenDetailMenu, initCloseDetailMenu } from "./detail-menu.js";
import { initFilterMenus } from "./filter-menus.js";

document.addEventListener("DOMContentLoaded", () => {
    initHeaderBehaviour();
    initScrollReveal();
    initOpenDetailMenu();
    initCloseDetailMenu();
    initFilterMenus();
})

// Paralax effect
window.addEventListener('scroll', function () {
    const parallax = document.querySelector('.parallax');
    let scrollPosition = window.pageYOffset;

    if (parallax != null) {
        parallax.style.transform = 'translateY(' + scrollPosition * .6 + 'px)';
    }
});