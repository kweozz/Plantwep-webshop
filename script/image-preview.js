function previewImage(event, previewId) {
    const reader = new FileReader();
    reader.onload = function () {
        const preview = document.getElementById(previewId);
        preview.src = reader.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
