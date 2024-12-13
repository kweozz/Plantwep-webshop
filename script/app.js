document.querySelector('#btnAddReview').addEventListener('click', function () {
    let productid = this.dataset.productid;
    let userid = this.dataset.userid;
    let rating = 5; // Assuming a fixed rating for testing
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
                <p><strong>${result.user_name}</strong></p>
                <p>Rating: ${'★'.repeat(result.rating)}${'☆'.repeat(5 - result.rating)}</p>
                <p>${result.body}</p>
                <p><small>${new Date().toLocaleString()}</small></p>
            `;
            document.querySelector('.reviews').appendChild(div);
        } else {
            console.error('Error:', result.message);
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
    });
});