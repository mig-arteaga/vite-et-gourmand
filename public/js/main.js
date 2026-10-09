// Initialisation des fonctions
import { initHeaderBehaviour } from "./header.js";
import { initParallax } from "./parallax.js";
import { initScrollReveal } from "./scroll-reveal.js";
import { initOpenDetailMenu, initCloseDetailMenu } from "./detail-menu.js";
import { initApplyFilters, initResetFilters, initFilterValues, initSliders } from "./filter-menus.js";
import { initHideEmptyDishes, initOrderChanges, initOpenConfirmationMenu, initCloseConfirmationMenu } from "./commande.js";

document.addEventListener("DOMContentLoaded", () => {
    initHeaderBehaviour();
    initParallax();
    initScrollReveal();
    // Menus
    initOpenDetailMenu();
    initCloseDetailMenu();
    initApplyFilters();
    initResetFilters();
    initFilterValues();
    initSliders();
    // Orders
    initHideEmptyDishes();
    initOrderChanges();
    initOpenConfirmationMenu();
    initCloseConfirmationMenu();
});