const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
const potCheckboxes = document.querySelectorAll('.pot-checkbox');
const finalPrice = document.getElementById('finalPrice');
const productPrice = parseFloat(finalPrice.innerText);
const quantityInput = document.getElementById('quantity');
const quantityError = document.getElementById('quantity-error');
const addToBasketForm = document.getElementById('add-to-basket-form');

function calculatePrice() {
    let price = productPrice;

    sizeCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            price += parseFloat(checkbox.dataset.price || 0);
        }
    });

    potCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            price += parseFloat(checkbox.dataset.price || 0);
        }
    });

    // Add the quantity to the price
    const quantity = quantityInput.value;
    price *= quantity;

    // Ensure the price does not go below the base price
    if (price < productPrice) {
        price = productPrice;
    }

    finalPrice.innerText = price.toFixed(2);
}

// Add event listeners to checkboxes
sizeCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        sizeCheckboxes.forEach(cb => cb.checked = false);
        this.checked = true;
        calculatePrice();
    });
});

potCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        potCheckboxes.forEach(cb => cb.checked = false);
        this.checked = true;
        calculatePrice();
    });
});

// Add event listener to quantity input
quantityInput.addEventListener('input', function () {
    const maxQuantity = parseInt(quantityInput.getAttribute('max'), 10);
    if (quantityInput.value > maxQuantity) {
        quantityError.style.display = 'block';
        quantityInput.value = maxQuantity;
    } else {
        quantityError.style.display = 'none';
    }
    calculatePrice();
});

// Prevent form submission if quantity exceeds stock
addToBasketForm.addEventListener('submit', function (event) {
    const maxQuantity = parseInt(quantityInput.getAttribute('max'), 10);
    if (quantityInput.value > maxQuantity) {
        event.preventDefault();
        quantityError.style.display = 'block';
    }
});
