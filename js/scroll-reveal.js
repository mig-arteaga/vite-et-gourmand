// Scroll reveal
export function initScrollReveal() {
    window.sr = ScrollReveal();
    
    sr.reveal('.animate-right', {
      origin: 'left',
      duration: 1000,
      distance: '25rem',
      delay: 150
    });
    
    sr.reveal('.animate-left', {
      origin: 'right',
      duration: 1000,
      distance: '25rem',
      delay: 300
    });
    
    sr.reveal('.animate-bottom', {
      origin: 'top',
      duration: 1000,
      distance: '25rem',
      delay: 300
    });
    
    sr.reveal('.animate-top', {
      origin: 'bottom',
      duration: 1000,
      distance: '25rem',
      delay: 300
    });
    
    sr.reveal('.title-down', {
      origin: 'top',
      duration: 1000,
      distance: '1rem',
      delay: 150
    });
    
    sr.reveal('.title-up', {
      origin: 'bottom',
      duration: 1000,
      distance: '1rem',
      delay: 150
    });
};
