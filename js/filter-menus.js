// Filter menus
const resetFilterButton = document.getElementById('reset-filter-btn');
const filterMinPrice = document.getElementById('min-price');
const filterMaxPrice = document.getElementById('max-price');
const filterTheme = document.getElementById('theme');
const filterDiet = document.getElementById('diet');
const filterPeople = document.getElementById('people');
const minPriceOutput = document.getElementById("min-price-output");
const maxPriceOutput = document.getElementById("max-price-output");
const peopleOutput = document.getElementById("people-output");

const priceGap = 1;

let minPriceDefault;
let maxPriceDefault;
let peopleDefault;
let minPeople;

function postFilterData(minPrice, maxPrice, theme, diet, people) {
    const formData = new FormData();

    formData.append('minPrice', minPrice);
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

function applyFilters() {
    const minPrice = parseInt(filterMinPrice.value);
    const maxPrice = parseInt(filterMaxPrice.value);
    const theme = filterTheme.value;
    const diet = filterDiet.value;
    const people = parseInt(filterPeople.value);
    
    postFilterData(minPrice, maxPrice, theme, diet, people);
}

export function initApplyFilters() {
    if (
        !filterMinPrice || 
        !filterMaxPrice || 
        !filterTheme || 
        !filterDiet || 
        !filterPeople ) return;
    
    filterMinPrice.addEventListener("change", applyFilters);
    filterMaxPrice.addEventListener("change", applyFilters);
    filterTheme.addEventListener("change", applyFilters);
    filterDiet.addEventListener("change", applyFilters);
    filterPeople.addEventListener("change", applyFilters);
};

export function initResetFilters() {
    if (!resetFilterButton) return;
    
    resetFilterButton.addEventListener('click', () => {
        filterMinPrice.value = minPriceDefault;
        filterMaxPrice.value = maxPriceDefault;
        filterTheme.value = "";
        filterDiet.value = "";
        filterPeople.value = peopleDefault;

        minPriceOutput.innerHTML = filterMinPrice.value + " €";
        maxPriceOutput.innerHTML = filterMaxPrice.value + " €";
        peopleOutput.innerHTML = filterPeople.value + " personnes";
        
        postFilterData("", "", "", "", "");

        fillColor();
    });
};

// Update filter visuals
export function initSliders() {
    if (!filterMinPrice || !filterMaxPrice || !filterPeople) return;

    minPriceOutput.innerHTML = filterMinPrice.value;
    maxPriceOutput.innerHTML = filterMaxPrice.value;
    peopleOutput.innerHTML = filterPeople.value;

    filterMinPrice.addEventListener("input", () => {
        if (parseInt(filterMaxPrice.value) - parseInt(filterMinPrice.value) <= priceGap) {
            filterMinPrice.value = parseInt(filterMaxPrice.value) - priceGap
        };

        minPriceOutput.innerHTML = filterMinPrice.value + " €";
        fillColor();
    });
    filterMaxPrice.addEventListener("input", () => {
        if (parseInt(filterMaxPrice.value) - parseInt(filterMinPrice.value) <= priceGap) {
            filterMaxPrice.value = parseInt(filterMinPrice.value) + priceGap
        };

        maxPriceOutput.innerHTML = filterMaxPrice.value + " €";
        fillColor();
    });
    filterPeople.addEventListener("input", () => {
        peopleOutput.innerHTML = filterPeople.value + " personnes";
        fillColor();
    });
};

// Background color of sliders
function fillColor() {
    const sliderTrackPrice = document.getElementById('slider-track-price');
    const sliderTrackPeople = document.getElementById('slider-track-people');
    
    let percent1 = ((filterMinPrice.value - minPriceDefault) / (maxPriceDefault - minPriceDefault)) * 100;
    let percent2 = ((filterMaxPrice.value - minPriceDefault) / (maxPriceDefault - minPriceDefault)) * 100;
    let percent3 = ((filterPeople.value - minPeople) / (peopleDefault - minPeople)) * 100;

    sliderTrackPrice.style.background = `linear-gradient(
        to right, 
        #D9D9D9 ${percent1}% , 
        #C59D5F ${percent1}% , 
        #C59D5F ${percent2}%, 
        #D9D9D9 ${percent2}%
    )`;
    
    sliderTrackPeople.style.background = `linear-gradient(
        to right, 
        #C59D5F ${percent3}% , 
        #D9D9D9 ${percent3}%
    )`;
};

// Load filter values
export async function initFilterValues() {
    if (!resetFilterButton) return;

    const response = await fetch('back-end/get-filter-values.php');
    const data = await response.json();

    // console.log(data);

    // Default prices
    minPriceDefault = parseInt(data.prices.minPrice);
    maxPriceDefault = Math.ceil(data.prices.maxPrice);

    // Min price
    filterMinPrice.min = minPriceDefault;
    filterMinPrice.max = maxPriceDefault;
    filterMinPrice.value = minPriceDefault;

    minPriceOutput.innerHTML = filterMinPrice.value + " €";

    // Max price
    filterMaxPrice.min = minPriceDefault;
    filterMaxPrice.max = maxPriceDefault;
    filterMaxPrice.value = maxPriceDefault;

    maxPriceOutput.innerHTML = filterMaxPrice.value + " €";

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
    peopleDefault = data.people.maxPeople;
    minPeople = data.people.minPeople;

    filterPeople.min = data.people.minPeople;
    filterPeople.max = data.people.maxPeople;
    filterPeople.value = data.people.maxPeople;

    peopleOutput.innerHTML = filterPeople.value + " personnes";

    fillColor();
};