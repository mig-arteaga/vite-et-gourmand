// Filter menus
const filterButton = document.getElementById('filter-btn');
const resetFilterButton = document.getElementById('reset-filter-btn');
const filterMaxPrice = document.getElementById('max-price');
const filterTheme = document.getElementById('theme');
const filterDiet = document.getElementById('diet');
const filterPeople = document.getElementById('people');
const priceOutput = document.getElementById("max-price-output");
const peopleOutput = document.getElementById("people-output");

let maxPriceDefault;
let peopleDefault;

function postFilterData(maxPrice, theme, diet, people) {
    const formData = new FormData();

    formData.append('maxPrice', maxPrice);
    formData.append('theme', theme);
    formData.append('diet', diet);
    formData.append('people', people);
    
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
};

export function initFilterMenus () {
    
    if (!filterButton || !resetFilterButton) return;
    
    filterButton.addEventListener('click', () => {
        const maxPrice = parseInt(filterMaxPrice.value);
        const theme = filterTheme.value;
        const diet = filterDiet.value;
        const people = parseInt(filterPeople.value);
        
        postFilterData(maxPrice, theme, diet, people);
    });

    resetFilterButton.addEventListener('click', () => {
        filterMaxPrice.value = maxPriceDefault;
        filterTheme.value = "";
        filterDiet.value = "";
        filterPeople.value = peopleDefault;

        priceOutput.innerHTML = filterMaxPrice.value + " €";
        peopleOutput.innerHTML = filterPeople.value + " personnes";
        
        postFilterData("", "", "", "");
    });
};

// Update filter visuals
export function initSliders() {
    if (!filterMaxPrice || !filterPeople) return;

    // filterMaxPrice.value
    priceOutput.innerHTML = filterMaxPrice.value;
    peopleOutput.innerHTML = filterPeople.value;

    filterMaxPrice.addEventListener("input", () => {
        priceOutput.innerHTML = filterMaxPrice.value + " €";
    });
    filterPeople.addEventListener("input", () => {
        peopleOutput.innerHTML = filterPeople.value + " personnes";
    });
};

// Load filter values
export async function initFilterValues() {
    const response = await fetch('back-end/get-filter-values.php');
    const data = await response.json();

    console.log(data);

    // Max price
    maxPriceDefault = data.prices.maxPrice;

    filterMaxPrice.min = data.prices.minPrice;
    filterMaxPrice.max = data.prices.maxPrice;
    filterMaxPrice.value = data.prices.maxPrice;

    priceOutput.innerHTML = filterMaxPrice.value + " €";

    // Theme
    const emptyTheme = document.createElement('option');

    emptyTheme.value = ""
    emptyTheme.innerHTML = "Choisir un thème"

    filterTheme.appendChild(emptyTheme);

    data.themes.forEach(theme => {
        const option = document.createElement('option');

        option.value = theme.theme
        option.innerHTML = theme.theme

        filterTheme.appendChild(option);
    });

    // Diet
    const emptyDiet = document.createElement('option');

    emptyDiet.value = ""
    emptyDiet.innerHTML = "Choisir un régime"

    filterDiet.appendChild(emptyDiet);

    data.diets.forEach(diet => {
        const option = document.createElement('option');

        option.value = diet.diet
        option.innerHTML = diet.diet

        filterDiet.appendChild(option);
    });

    // People
    peopleDefault = data.prices.maxPrice;

    filterPeople.min = data.people.minPeople;
    filterPeople.max = data.people.maxPeople;
    filterPeople.value = data.people.maxPeople;

    peopleOutput.innerHTML = filterPeople.value + " personnes";
};