// Image previewer
function previewImage(event, previewId) {
    const reader = new FileReader();
    reader.onload = function () {
        const preview = document.getElementById(previewId);
        preview.src = reader.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}

// Select All Sizes functionality
document.getElementById('select-all-sizes').addEventListener('change', function () {
    const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
    sizeCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
});

// Before form submission, ensure that options are always an array
document.querySelector('form').addEventListener('submit', function (e) {
    var options = document.querySelectorAll('input[name="options[]"]:checked');
    var optionsArray = [];

    options.forEach(function (option) {
        optionsArray.push(option.value);
    });

    // Ensure that optionsArray is always passed even if it's empty
    if (optionsArray.length === 0) {
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'options[]';
        hiddenInput.value = '';
        this.appendChild(hiddenInput);
    }
});
// Function to handle checkbox changes
document.querySelectorAll('.option-button input[type="checkbox"]').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        var priceAdditionInputId = this.getAttribute('data-price-addition-input');
        var priceAdditionDiv = document.getElementById(priceAdditionInputId);

        // Show or hide the price addition input depending on checkbox state
        if (this.checked) {
            priceAdditionDiv.style.display = 'block';
        } else {
            priceAdditionDiv.style.display = 'none';
        }
    });
});

// Select All Sizes functionality
document.getElementById('select-all-sizes').addEventListener('change', function () {
    const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
    sizeCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        var priceAdditionInputId = checkbox.getAttribute('data-price-addition-input');
        var priceAdditionDiv = document.getElementById(priceAdditionInputId);

        // Show or hide the price addition input based on the "Select All" checkbox
        priceAdditionDiv.style.display = this.checked ? 'block' : 'none';
    });
});