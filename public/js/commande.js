// Commande
const address = document.getElementById('address');
const city = document.getElementById('city');
const zipcode = document.getElementById('zipcode');

const date = document.getElementById('date');
const hour = document.getElementById('hour');

const equipmentLoan = document.getElementById('equipment-loan');

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
let groupOfferValue = 0;
let distanceValue = 0;
let distanceFeeValue = 0;

let isAddressValid = false;
let addressVersion = 0;

async function postOrderData(menuId) {
    const formData = new FormData();
    formData.append('menuId', menuId);

    const response = await fetch('/commande/change-menu', {
        method: 'POST',
        body: formData
    });

    return await response.json();
};

async function postPriceData(menuId, people, distanceKm) {
    const formData = new FormData();

    formData.append('menuId', menuId);
    formData.append('people', people);
    formData.append('distanceKm', distanceKm);

    const response = await fetch('/commande/calculate-price', {
        method: 'POST',
        body: formData
    });

    if (!response.ok) {
        throw new Error('Erreur lors du calcul du prix');
    }

    return await response.json();
}

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

async function updatePrices() {
    let prices;

    try {
        prices = await postPriceData(
            Number(menuList.value),
            Number(people.value),
            distanceValue
        );
    } catch (error) {
        console.error('Erreur lors du calcul des prix :', error);
        return;
    }

    const menuPriceValue = prices.menuPrice;
    const groupOfferValue = -prices.groupDiscount;
    const deliveryFeeValue = prices.deliveryFee;
    const distanceFeeValue = prices.distanceFee;

    menuPrice.textContent = formatPrice(menuPriceValue);
    groupOffer.textContent = formatPrice(groupOfferValue);
    deliveryFee.textContent = formatPrice(deliveryFeeValue);
    distanceFee.textContent = formatPrice(distanceFeeValue);
    totalPrice.textContent = formatPrice(prices.totalPrice);
};

async function loadOrderChange() {
    const menuId = parseInt(menuList.value);
    const data = await postOrderData(menuId);

    photo.src = data.menu.photo;
    menuTitle.textContent = data.menu.title
    menuTitle.textContent = data.menu.title

    updateDishes(data.dishes);

    minPeople.textContent = data.menu.min_people;
    delay.textContent = data.menu.delay;
    stock.textContent = data.menu.stock;

    const unitPriceValue = Number(data.menu.unit_price);

    if (unitPriceValue % parseInt(unitPriceValue) === 0) {
        unitPrice.textContent = parseInt(unitPriceValue);
    }
    else {
        unitPrice.textContent = unitPriceValue;
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
    const currentVersion = ++addressVersion;

    distanceValue = 0;
    isAddressValid = false;

    if (!address.value.trim() || !city.value.trim()) {
        await updatePrices();
        return;
    }

    const formData = new FormData();

    formData.append("address", address.value);
    formData.append("city", city.value);
    formData.append("zipcode", zipcode.value);
    formData.append(
        "csrf_token",
        document.getElementById("csrf_token").value
    );

    try {
        const response = await fetch("/commande/distance", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (currentVersion !== addressVersion) return;

        if (!response.ok || data.error) {
            throw new Error(data.error || "Erreur du calcul de la distance");
        }

        distanceValue = data.distanceKm;
        isAddressValid = true;

    } catch (error) {
        if (currentVersion !== addressVersion) return;

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

    const invalidateAddress = () => {
        isAddressValid = false;
        addressVersion++;
    };

    address.addEventListener('input', invalidateAddress);
    city.addEventListener('input', invalidateAddress);
    zipcode.addEventListener('input', invalidateAddress);
};



// Open order confirmation
function displayRecapData(recapData) {
    const recapAddress = document.getElementById('recap-address');
    const recapDatetime = document.getElementById('recap-datetime');
    const recapMenu = document.getElementById('recap-menu');
    const recapPeople = document.getElementById('recap-people');
    const recapEquipmentLoan = document.getElementById('recap-equipment-loan');
    const recapTotal = document.getElementById('recap-total');

    const dateValue = new Date(recapData.date);
    const formattedDate = dateValue.toLocaleDateString("fr-FR", {
        day: "2-digit",
        month: "long",
        year: "numeric"
    });

    recapAddress.textContent =
    `${recapData.address}, ${recapData.zipcode ? recapData.zipcode + ' ' : ''}${recapData.city}`;
    recapDatetime.textContent = formattedDate + ' à ' + recapData.hour;
    recapMenu.textContent = recapData.menu;
    recapPeople.textContent = recapData.people;
    recapEquipmentLoan.textContent = recapData.equipmentLoan ? 'Oui' : 'Non';
    recapTotal.textContent = recapData.total;
};

const confirmationBg = document.getElementById('confirmation-bg');

export function initOpenConfirmationMenu () {
    const recapButton = document.getElementById('recap-btn');

    if (!recapButton) return;

    recapButton.addEventListener('click', () => {
        if (!validateOrderForm()) return;

        const recapData = {
            address: address.value,
            city: city.value,
            zipcode: zipcode.value,
            date: date.value,
            hour: hour.value,
            menu: menuTitle.textContent,
            people: people.value,
            equipmentLoan: equipmentLoan.checked,
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

// Show error
function showError(input, message) {
    input.parentElement.classList.add('input-error');

    const error = document.createElement('p');
    error.classList.add('error-message');
    error.textContent = message;

    input.parentElement.appendChild(error);
};

// Clear errors
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(error => {
        error.remove();
    });

    document.querySelectorAll('.input-error').forEach(input => {
        input.classList.remove('input-error');
    });
}

// Validate Order Form
function validateOrderForm() {
    let isValid = true;

    clearErrors();

    // Check people
    if(people.value < Number(minPeople.textContent)) {
        showError(
            people,
            `Minimum ${minPeople.textContent} personnes`
        );
        isValid = false;
    };

    // Check date
    if (!date.value) {
        showError(
            date,
            `Veuillez renseigner une date`
        );
        isValid = false;
    };

    const selectedDate = new Date(date.value);
    const minDate = new Date(date.min);
    const maxDate = new Date(date.max);

    if (selectedDate < minDate || selectedDate > maxDate) {
        showError(
            date,
            `Veuillez renseigner une date correcte`
        );
        isValid = false;
    };

    // Check time
    const selectedTime = hour.value;
    const minTime = hour.min;
    const maxTime = hour.max;

    if (!selectedTime) {
        showError(
            hour,
            `Veuillez renseigner une heure`
        );
        isValid = false;
    }
    else if (selectedTime < minTime || selectedTime > maxTime) {
        showError(
            hour,
            `L\'heure doit être comprise entre ${minTime} et ${maxTime}`
        );
        isValid = false;
    };

    //Check address
    if (!address.value.trim()) {
        showError(
            address,
            `Veuillez renseigner une adresse`
        )
    };
    if (!isAddressValid) {
        showError(
            address,
            "Veuillez renseigner une adresse valide."
        );
        return;
    }

    //Check city
    if (!city.value.trim()) {
        showError(
            city,
            `Veuillez renseigner une ville`
        )
    };

    //Check zipcode
    // if (!zipcode.value.trim()) {
    //     showError(
    //         zipcode,
    //         `Veuillez renseigner un code postal`
    //     )
    // };

    return isValid;
};

export function initConfirmOrder() {
    const confirmButton = document.getElementById('order-confirm-btn');
    const form = document.getElementById('order');
    const confirmBox = document.getElementById('confirmation-box');
    const successBox = document.getElementById('order-success');
    const successId = document.getElementById('order-success-id');
    const submitError = document.getElementById('order-submit-error');

    if (!confirmButton || !form || !confirmBox || !successBox || !successId || !submitError) return;

    let isSubmitting = false;

    confirmButton.addEventListener('click', async () => {
        if (isSubmitting) return;

        isSubmitting = true;

        submitError.textContent = '';
        submitError.hidden = true;

        const formData = new FormData(form);

        try {
            const response = await fetch('/commande/create', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (!response.ok || data.error) {
                throw new Error(data.error || 'Erreur de commande');
            }

            // Afficher le numéro de commande
            successId.textContent = data.commandeId;

            // Masquer la fenêtre de confirmation pour empêcher une nouvelle soumission
            confirmBox.hidden = true

            // Afficher le message de confirmation
            successBox.hidden = false;

        } catch (error) {
            console.error(error);

            submitError.textContent = error instanceof TypeError
                ? 'Impossible de contacter le serveur. Vérifiez votre connexion.'
                : (error.message || 'Une erreur est survenue lors de la commande.');

            submitError.hidden = false;

            isSubmitting = false;
        }
    });
}