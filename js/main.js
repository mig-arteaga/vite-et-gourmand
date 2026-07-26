// Initialisation des fonctions
import { initHeaderBehaviour } from "./header.js";
import { initParallax } from "./parallax.js";
import { initScrollReveal } from "./scroll-reveal.js";
import { initOpenDetailMenu, initCloseDetailMenu } from "./detail-menu.js";
import { initFilterMenus, initFilterValues, initSliders } from "./filter-menus.js";

document.addEventListener("DOMContentLoaded", () => {
    initHeaderBehaviour();
    initParallax();
    initScrollReveal();
    initOpenDetailMenu();
    initCloseDetailMenu();
    initFilterMenus();
    initFilterValues();
    initSliders();
});