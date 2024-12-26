//index.php
const categories = document.querySelector('.categories');
const leftBtn = document.querySelector('.left-btn');
const rightBtn = document.querySelector('.right-btn');
const wrapper = document.querySelector('.categories-wrapper');

// Function to check if the categories are overflowing
function checkOverflow() {
    const isOverflowing = categories.scrollWidth > categories.clientWidth;
    if (isOverflowing) {
        leftBtn.style.display = 'block';
        rightBtn.style.display = 'block';
        wrapper.style.backgroundColor = '#f6f6f6';
    } else {
        leftBtn.style.display = 'none';
        rightBtn.style.display = 'none';
    }
}

// Initial check for overflow when the page loads
checkOverflow();

 // Optionally, recheck overflow when the window is resized
 window.addEventListener('resize', checkOverflow);



 //are you sure u want to delete this product
 //if yes, delete
 //if no, prevent default

 document.querySelectorAll('.delete-form').forEach(form => {
     form.addEventListener('submit', function (event) {
         if (!confirm('Are you sure you want to delete this product?')) {
             event.preventDefault();
         }
     });
 });