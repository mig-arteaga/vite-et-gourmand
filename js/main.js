// Initialisation des fonctions
import { initHeaderBehaviour } from "./header.js";
import { initParallax } from "./parallax.js";
import { initScrollReveal } from "./scroll-reveal.js";
import { initOpenDetailMenu, initCloseDetailMenu } from "./detail-menu.js";
import { initApplyFilters, initResetFilters, initFilterValues, initSliders } from "./filter-menus.js";
import { initOrderValues, hideEmptyDishes, initOrderChanges } from "./commande.js";

document.addEventListener("DOMContentLoaded", () => {
    initHeaderBehaviour();
    initParallax();
    initScrollReveal();
    initOpenDetailMenu();
    initCloseDetailMenu();
    initApplyFilters();
    initResetFilters();
    initFilterValues();
    initSliders();
    initOrderValues();
    hideEmptyDishes();
    initOrderChanges();
});