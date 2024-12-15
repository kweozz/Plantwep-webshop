document.querySelector('#btnAddReview').addEventListener('click', function (event) {
    event.preventDefault(); // Prevent the default form submission

    let productid = this.dataset.productid;
    let userid = this.dataset.userid;
    let rating = document.querySelector('input[name="rating"]:checked').value;
    let comment = document.querySelector('#reviewText').value;

    let formData = new FormData();
    formData.append('product_id', productid);
    formData.append('user_id', userid);
    formData.append('rating', rating);
    formData.append('comment', comment);

    fetch('ajax/savereview.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        if (response.headers.get('content-type') && response.headers.get('content-type').includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                throw new Error('Invalid JSON response: ' + text);
            });
        }
    })
    .then(result => {
        if (result.status === 'success') {
            console.log("Success", result);
            let div = document.createElement('div');
            div.classList.add('review');
            div.innerHTML = `
                <div class="review-details">
                    <h3>${result.user_name}</h3>
                    <p>
                        ${'<i class="fas fa-star"></i>'.repeat(result.rating)}${'<i class="far fa-star"></i>'.repeat(5 - result.rating)}
                    </p>
                    <p>${result.body}</p>
                </div>
                <div class="date">
                    <p>${new Date().toLocaleDateString()}</p>
                </div>
            `;
            document.querySelector('.reviews').appendChild(div);
            document.querySelector('#reviewText').value = ''; // Clear the input field
        } else {
            console.error('Error:', result.message);
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
    });
});
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
