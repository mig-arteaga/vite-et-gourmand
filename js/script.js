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

// const detailButtons = document.querySelectorAll('.menu-btn');
// const detailBg = document.getElementById('detail-bg');

// menuButton.addEventListener('click', () => {
//     detailMenu.classList.add("visible");
// });

// Close detail menu

// const closeMenuButton = document.querySelector('.close-btn');

// closeMenuButton.addEventListener('click', () => {
//     detailBg.classList.remove("visible");
// });

// detailMenu.addEventListener('click', (e) => {
//     if (e.target === e.currentTarget) {
//         detailBg.classList.remove("visible");
//     }
// });


// Open detail menu

const detailButtons = document.querySelectorAll('.menu-btn');
const detailBg = document.getElementById('detail-bg');

console.log("Number of buttons found:", detailButtons.length);

detailButtons.forEach(button => {
    button.addEventListener('click', () => {
        const menuId = button.id;

        console.log("Sending menu ID:", menuId);

        detailBg.classList.add('visible');

        const formData = new FormData();

        formData.append('menu', menuId);

        fetch('assets/detail-menu.php', {
            method: 'POST',
            body: formData
        })

        .then(response => response.json())
        .then(data => {
            console.log("PHP response:");
            console.log(data);

            const title = document.getElementById('detail-title');
            title.textContent = data.titre;
        });
    });
});


// Close detail menu

const closeMenuButton = document.querySelector('.close-btn');

closeMenuButton.addEventListener('click', () => {
    detailBg.classList.remove("visible");
});