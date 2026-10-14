// Paralax effect
export function initParallax() {
    window.addEventListener('scroll', () => {
        const parallax = document.querySelector('.parallax');
        let scrollPosition = window.pageYOffset;
    
        if (parallax) {
            parallax.style.transform = 'translateY(' + scrollPosition * .6 + 'px)';
        }
    });
};