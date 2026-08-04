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
let unitPriceValue;
let peopleValue;

async function postOrderData(menuId) {
    const formData = new FormData();
    formData.append('menuId', menuId);
    
    const response = await fetch('back-end/change-commande.php', {
        method: 'POST',
        body: formData
    });

    return await response.json();
};

export async function initOrderValues() {
    // const menuId = parseInt(menuList.value);
    // const data = await postOrderData(menuId);
    // const today = new Date();
    // const minDate = new Date();
    // const maxDate = new Date();

    // minDate.setDate(today.getDate() + data.menu.delay);
    // maxDate.setMonth(today.getMonth() + 6);

    // console.log(data);
    // console.log(minDate);
    // console.log(maxDate);

    // date.min = minDate.toISOString().split("T")[0];
    // date.max = maxDate.toISOString().split("T")[0];
    // date.value = minDate.toISOString().split("T")[0];
};

export function hideEmptyDishes() {
    const dishList = document.querySelectorAll(".dish-list-item");
    
    if (dishList.length > 0) {
        dishList.forEach(item => {
            const span = item.querySelector("span");
    
            if (!span.textContent.trim()) {
                item.style.display = "none";
            }
        });
    }
}

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

function changePrices() {
    menuPrice.innerHTML = unitPriceValue * peopleValue;
    groupOffer.innerHTML = "-0";
    deliveryFee.innerHTML = deliveryFeeValue;
    distanceFee.innerHTML = 0;
    totalPrice.innerHTML = (unitPriceValue * peopleValue) + deliveryFeeValue;
};

async function loadOrderChanges() {
    const menuId = parseInt(menuList.value);
    const data = await postOrderData(menuId);

    unitPriceValue = data.menu.unit_price;
    
    photo.src = data.menu.photo;
    menuTitle.innerHTML = data.menu.title
    menuTitle.innerHTML = data.menu.title
    
    updateDishes(data.dishes);
    
    minPeople.innerHTML = data.menu.min_people;
    delay.innerHTML = data.menu.delay;
    stock.innerHTML = data.menu.stock;

    // const unitPriceValue = data.menu.unit_price; 
    
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

    peopleValue = people.value;

    // Change prices
    changePrices();
};

export function initOrderChanges() {
    if(menuList) {
        menuList.addEventListener('input', loadOrderChanges);
    };
};