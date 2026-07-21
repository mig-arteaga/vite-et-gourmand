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
    const navList = document.querySelector('.nav-list');
    // const navList = document.getElementById('nav-list');
    const navItem = navList.children[index];
    
    navItem.classList.add("active");
});

//Paralax effect

window.addEventListener('scroll', function () {
  const parallax = document.querySelector('.parallax');
  let scrollPosition = window.pageYOffset;

  parallax.style.transform = 'translateY(' + scrollPosition * .6 + 'px)';
});