function previewImage(event, previewId) {
    const reader = new FileReader();
    reader.onload = function () {
        const preview = document.getElementById(previewId);
        preview.src = reader.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}

// Dynamisch tonen/verbergen van prijsverhoging invoervelden
document.querySelectorAll('.option-button input[type="checkbox"]').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const priceAdditionInputId = this.getAttribute('data-price-addition-input');
        const priceAdditionDiv = document.getElementById(priceAdditionInputId);

        if (this.checked) {
            priceAdditionDiv.style.display = 'block';
        } else {
            priceAdditionDiv.style.display = 'none';
        }
    });
});