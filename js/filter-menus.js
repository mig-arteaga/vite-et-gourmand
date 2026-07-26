// Filter menus
export function initFilterMenus () {
    const filterButton = document.getElementById('filter-btn');
    const filterMaxPrice = document.getElementById('max-price');
    const filterTheme = document.getElementById('theme');
    const filterDiet = document.getElementById('diet');
    
    if (!filterButton) return;
    
    filterButton.addEventListener('click', () => {
        const maxPrice = parseInt(filterMaxPrice.value);
        const theme = filterTheme.value;
        const diet = filterDiet.value;

        const formData = new FormData();

        formData.append('maxPrice', maxPrice);
        formData.append('theme', theme);
        formData.append('diet', diet);
        
        fetch('back-end/filter-menus.php', {
            method: 'POST',
            body: formData
        })

        .then(response => response.text())
        .then(html => {
            // console.log("PHP response:");
            // console.log(html);
            
            const menuWrap = document.getElementById('menu-wrap');

            menuWrap.innerHTML = html;
        });
    });
};