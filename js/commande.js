// Commande
const address = document.getElementById('address');
const city = document.getElementById('city');
const zipcode = document.getElementById('zipcode');

const date = document.getElementById('date');
const hour = document.getElementById('hour');

const menuList = document.getElementById('menu-list');
const photo = document.getElementById('order-menu-img');
const menuTitle = document.getElementById('menu-title');
const appetizer = document.getElementById('detail-appetizer');
const mainCourse = document.getElementById('detail-main-course');
const dessert = document.getElementById('detail-dessert');
const minPeople = document.getElementById('detail-min-people');
const unitPrice = document.getElementById('detail-unit-price');
const delay = document.getElementById('detail-delay');
const stock = document.getElementById('detail-stock');
const people = document.getElementById('people');

const menuPrice = document.getElementById('menu-price');
const groupOffer = document.getElementById('group-offer');
const deliveryFee = document.getElementById('delivery-fee');
const distanceFee = document.getElementById('distance-fee');
const totalPrice = document.getElementById('total-price');

const deliveryFeeValue = 5;
const groupOfferPercentage = 0.1;
const distanceFeePerKm = 0.59;
let unitPriceValue = Number(unitPrice.innerHTML);
let peopleValue = Number(people.value);
let minPeopleValue = Number(people.min);
let groupOfferValue = 0;
let distanceValue = 0;
let distanceFeeValue = 0;
let totalPriceValue = Number(totalPrice.innerHTML);

async function postOrderData(menuId) {
    const formData = new FormData();
    formData.append('menuId', menuId);
    
    const response = await fetch('back-end/change-commande.php', {
        method: 'POST',
        body: formData
    });

    return await response.json();
};

export function initHideEmptyDishes() {
    const dishList = document.querySelectorAll(".dish-list-item");
    
    if (dishList.length > 0) {
        dishList.forEach(item => {
            const span = item.querySelector("span");
    
            if (!span.textContent.trim()) {
                item.style.display = "none";
            };
        });
    };
};

function updateDishes(dishes) {
    const rows = {
        "Entrée": document.getElementById("detail-appetizer").parentElement,
        "Plat": document.getElementById("detail-main-course").parentElement,
        "Dessert": document.getElementById("detail-dessert").parentElement
    };

    Object.values(rows).forEach(row => row.style.display = "none");

    dishes.forEach(dish => {
        const span = {
            "Entrée": document.getElementById("detail-appetizer"),
            "Plat": document.getElementById("detail-main-course"),
            "Dessert": document.getElementById("detail-dessert")
        }[dish.type];

        span.textContent = dish.title;
        rows[dish.type].style.display = "flex";
    });
};

function formatPrice(value) {
    const number = Number(value);
    return Number.isInteger(number) ? number : number.toFixed(2);
};

function updatePrices() {
    unitPriceValue = Number(unitPrice.innerHTML);
    peopleValue = Number(people.value);
    minPeopleValue = Number(people.min);
    groupOfferValue = 0;
    distanceFeeValue = 0;

    if (peopleValue >= (minPeopleValue + 5)) {
        groupOfferValue = - unitPriceValue * peopleValue * groupOfferPercentage;
    };

    if (distanceValue > 0) {
        distanceFeeValue = distanceValue * distanceFeePerKm;
    };

    totalPriceValue = (unitPriceValue * peopleValue) + groupOfferValue + deliveryFeeValue + distanceFeeValue;
    
    menuPrice.innerHTML = formatPrice(unitPriceValue * peopleValue);
    groupOffer.innerHTML = formatPrice(groupOfferValue);
    deliveryFee.innerHTML = formatPrice(deliveryFeeValue);
    distanceFee.innerHTML = formatPrice(distanceFeeValue);
    totalPrice.innerHTML = formatPrice(totalPriceValue);
};

async function loadOrderChange() {
    const menuId = parseInt(menuList.value);
    const data = await postOrderData(menuId);
    
    photo.src = data.menu.photo;
    menuTitle.innerHTML = data.menu.title
    menuTitle.innerHTML = data.menu.title
    
    updateDishes(data.dishes);
    
    minPeople.innerHTML = data.menu.min_people;
    delay.innerHTML = data.menu.delay;
    stock.innerHTML = data.menu.stock;

    unitPriceValue = Number(data.menu.unit_price);

    if (unitPriceValue % parseInt(unitPriceValue) === 0) {
        unitPrice.innerHTML = parseInt(unitPriceValue);
    }
    else {
        unitPrice.innerHTML = unitPriceValue;
    };

    // Dates
    const today = new Date();
    const minDate = new Date();
    const maxDate = new Date();
    const dateValue = date.value;

    minDate.setDate(today.getDate() + data.menu.delay);
    maxDate.setMonth(today.getMonth() + 6);

    date.min = minDate.toISOString().split("T")[0];
    date.max = maxDate.toISOString().split("T")[0];
    date.value = ""

    // People
    people.min = data.menu.min_people;
    people.max = data.menu.stock;
    people.value = data.menu.min_people;

    // Change prices
    updatePrices();
};

async function getDistance() {
    distanceValue = 0;

    if (!address.value || !city.value || city.value.toLowerCase() === "grenoble") return;
    
    const formData = new FormData();

    formData.append("address", address.value);
    formData.append("city", city.value);
    formData.append("zipcode", zipcode.value);

    try {
        const response = await fetch("back-end/get-distance.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (!response.ok || data.error) {
            throw new Error(data.error || "Erreur du calcul de la distance");
        }

        const distanceMeters = data.routes[0].distanceMeters;
        const distanceKm = distanceMeters / 1000;

        console.log(distanceKm);
        distanceValue = distanceKm;

    } catch (error) {
        console.error(error.message);
    }

    updatePrices();
}

export function initOrderChanges() {
    if(!menuList || !people || !address || !city || !zipcode) return;

    menuList.addEventListener('input', loadOrderChange);
    people.addEventListener('input', updatePrices);
    address.addEventListener('change', getDistance);
    city.addEventListener('change', getDistance);
    zipcode.addEventListener('change', getDistance);
};



// Open order confirmation

function displayRecapData(recapData) {
    const recapAddress = document.getElementById('recap-address');
    const recapDatetime = document.getElementById('recap-datetime');
    const recapMenu = document.getElementById('recap-menu');
    const recapPeople = document.getElementById('recap-people');
    const recapTotal = document.getElementById('recap-total');

    recapAddress.innerHTML = recapData.address;
    recapDatetime.innerHTML = recapData.date;
    recapMenu.innerHTML = recapData.menu;
    recapPeople.innerHTML = recapData.people;
    recapTotal.innerHTML = recapData.total;
};

const confirmationBg = document.getElementById('confirmation-bg');

export function initOpenConfirmationMenu () {
    const recapButton = document.getElementById('recap-btn');
    
    if (!recapButton) return;

    recapButton.addEventListener('click', () => {
        const recapData = {
            address: address.value,
            city: city.value,
            zipcode: zipcode.value,
            date: date.value,
            hour: hour.value,
            menu: menuTitle.textContent,
            people: people.value,
            total: totalPrice.textContent
        };

        displayRecapData(recapData);

        confirmationBg.classList.add('visible');
    });
};

// Close order confirmation
export function initCloseConfirmationMenu () {
    const closeRecapButton = document.getElementById('close-recap-btn');
    const returnButton = document.getElementById('return-btn');
    
    if(!closeRecapButton || !returnButton) return;
    
    closeRecapButton.addEventListener('click', () => {
        confirmationBg.classList.remove("visible");
    });

    returnButton.addEventListener('click', () => {
        confirmationBg.classList.remove("visible");
    });
};