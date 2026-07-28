export function initHeaderBehaviour() {
    const header = document.getElementById('header');
    
    if (!header) return;
    
    // Header decorations
    const pages = [
        'index.php',
        'menus.php',
        'nous.php',
        'contact.php',
        'employe.php',
        'admin.php'
    ];

    window.addEventListener('load', () => {
        const windowName = location.pathname.split('/').pop();
        const index = pages.indexOf(windowName);
        
        if(!index === -1) {
            const navList = document.getElementById('nav-list');
            const navItem = navList.children[index];
            
            navItem.classList.add("active");
        };
    });
    
    
    // Scroll header
    window.addEventListener("scroll", () => {
        if (window.scrollY > 0) {
            header.classList.add("header-dark");
        } else {
            header.classList.remove("header-dark");
        }
    });
    
    // Toggle nav menu
    const toggleMenu = document.getElementById("toggle-menu");
    const toggleProfile = document.getElementById("profile-button");
    
    const navMenu = document.getElementById("nav-list");
    const profileMenu = document.getElementById("profile-menu");
    
    toggleMenu.addEventListener("click", () => {
        navMenu.classList.toggle("nav-visible");
    });
    
    toggleProfile.addEventListener("click", () => {
        profileMenu.classList.toggle("nav-visible");
    });
};