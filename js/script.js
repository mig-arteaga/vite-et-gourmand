// Header decorations

const pages = [
    'index.php',
    'menus.php',
    'nous.php',
    'contact.php'
];

window.addEventListener('load', () => {
    const windowName = location.pathname.split('/').pop();
    const index = pages.indexOf(windowName);
    // const navList = document.querySelector('.nav-list');
    const navList = document.getElementById('nav-list');
    const navItem = navList.children[index];
    
    navItem.classList.add("active");
});

// Paralax effect

window.addEventListener('scroll', function () {
    const parallax = document.querySelector('.parallax');
    let scrollPosition = window.pageYOffset;

    if (parallax != null) {
        parallax.style.transform = 'translateY(' + scrollPosition * .6 + 'px)';
    }
});

// Scroll reveal

// window.sr = ScrollReveal();

// sr.reveal('.animate-left', {
//   origin: 'left',
//   duration: 1000,
//   distance: '25rem',
//   delay: 300
// });

// sr.reveal('.animate-right', {
//   origin: 'right',
//   duration: 1000,
//   distance: '25rem',
//   delay: 600
// });

// sr.reveal('.animate-top', {
//   origin: 'top',
//   duration: 1000,
//   distance: '25rem',
//   delay: 600
// });

// sr.reveal('.animate-bottom', {
//   origin: 'bottom',
//   duration: 1000,
//   distance: '25rem',
//   delay: 600
// });

// Open detail menu

const menuButton = document.querySelector('.menu-btn');
const detailMenu = document.getElementById('detail-bg');

menuButton.addEventListener('click', () => {
    detailMenu.classList.add("visible");
});

// Close detail menu

const closeMenuButton = document.querySelector('.close-btn');

closeMenuButton.addEventListener('click', () => {
    detailMenu.classList.remove("visible");
});

detailMenu.addEventListener('click', (e) => {
    if (e.target === e.currentTarget) {
        detailMenu.classList.remove("visible");
    }
});