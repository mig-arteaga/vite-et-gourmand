//Duplicate URL array
function duplicateArray(arraySimple) {
    let arrayDouble = [];
    arrayDouble.push(...arraySimple);
    arrayDouble.push(...arraySimple);
    
    return arrayDouble;
};

// Open detail menu
const detailBg = document.getElementById('detail-bg');

export function initOpenDteailMenu () {
    const detailButtons = document.querySelectorAll('.menu-btn');
    
    if (!detailButtons.length || !detailBg) return;
    
    detailButtons.forEach(button => {
        button.addEventListener('click', () => {
            const menuId = button.id;
    
            //console.log("Sending menu ID:", menuId);
    
            detailBg.classList.add('visible');
    
            const formData = new FormData();
    
            formData.append('menu', menuId);
    
            fetch('assets/detail-menu.php', {
                method: 'POST',
                body: formData
            })
    
            .then(response => response.json())
            .then(data => {
                // console.log("PHP response:");
                // console.log(data);
    
                const title = document.getElementById('detail-title');
                const description = document.getElementById('detail-description');
                const theme = document.getElementById('detail-theme');
                const diet = document.getElementById('detail-diet');
                const minPeole = document.getElementById('detail-min-people');
                const delay = document.getElementById('detail-delay');
                const stock = document.getElementById('detail-stock');
                const unitPrice = document.getElementById('detail-unit-price');
    
                title.textContent = data.menu.title;
                description.textContent = data.menu.description;
                theme.textContent = data.menu.theme;
                diet.textContent = data.menu.diet;
                minPeole.textContent = data.menu.min_people;
                delay.textContent = data.menu.delay;
                stock.textContent = data.menu.stock;
    
                const priceValue = data.menu.unit_price;
    
                //console.log(priceValue);
                
                if (priceValue % parseInt(priceValue) == 0) {
                    unitPrice.textContent = parseInt(priceValue);
                } else {
                    unitPrice.textContent = priceValue;
                }
    
    
                // Photos
                let photos = [];
                data.photos.forEach(photo => {
                    photos.push(photo.path);
                });
    
                photos = duplicateArray(photos);
    
                const gallery = document.getElementById('menu-slide');
                gallery.innerHTML = "";
                
                photos.forEach(photo => {
                    const img = document.createElement('img');
                    img.src = photo;
                    img.classList.add('menu-slide-image');
                    gallery.appendChild(img);
                });
    
                //Dishes
                const appetizer = document.getElementById('detail-appetizer');
                const mainCourse = document.getElementById('detail-main-course');
                const dessert = document.getElementById('detail-dessert');
                const composition = document.getElementById('detail-composition');
    
                const hidden = document.querySelectorAll('.hidden')
    
                hidden.forEach(hidden => {
                    hidden.classList.remove('hidden');
                });
    
                if (data.dishes.length == 3) {
                    composition.textContent = "Entrée + plat + dessert";
                    appetizer.textContent = data.dishes[0].title;
                    mainCourse.textContent = data.dishes[1].title;
                    dessert.textContent = data.dishes[2].title;
                } else if (data.dishes[0].type == "Entrée") {
                    composition.textContent = "Entrée + plat"
                    appetizer.textContent = data.dishes[0].title;
                    mainCourse.textContent = data.dishes[1].title;
                    dessert.textContent = "Pas de dessert";
    
                    dessert.parentElement.classList.add('hidden');
                } else {
                    composition.textContent = "Plat + dessert"
                    appetizer.textContent = "Pas d'entrée";
                    mainCourse.textContent = data.dishes[0].title;
                    dessert.textContent = data.dishes[1].title;
    
                    appetizer.parentElement.classList.add('hidden');
                };
    
                // Allergenics
                const allergenicList = document.getElementById('allergenic-list');
                allergenicList.innerHTML = "";
                
                data.allergenics.forEach(allergenic => {
                    const li = document.createElement('li');
                    li.textContent = allergenic.allergenic;
                    li.classList.add('sub-list-item');
                    allergenicList.appendChild(li);
                });
            });
        });
    });
};

// Close detail menu
export function initCloseDteailMenu () {
    const closeMenuButton = document.querySelector('.close-btn');
    
    if(!closeMenuButton) return;
    
    closeMenuButton.addEventListener('click', () => {
        detailBg.classList.remove("visible");
    });
};