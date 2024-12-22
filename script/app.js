document.getElementById('btnAddReview').addEventListener('click', function (event) {
    event.preventDefault();

    const productId = this.getAttribute('data-productid');
    const userId = this.getAttribute('data-userid');
    const rating = document.querySelector('input[name="rating"]:checked').value;
    const comment = document.getElementById('reviewText').value;

    fetch('/ajax/savereview.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&user_id=${userId}&rating=${rating}&comment=${encodeURIComponent(comment)}`,
    })
    .then(response => {
        if (response.headers.get('content-type') && response.headers.get('content-type').includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error('Invalid JSON response:', text);
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
