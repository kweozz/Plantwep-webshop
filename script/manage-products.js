 // Optional: Handle category scrolling if needed
 const categories = document.querySelector('.categories');
 const leftBtn = document.querySelector('.left-btn');
 const rightBtn = document.querySelector('.right-btn');

 // Function to check if the categories are overflowing
 function checkOverflow() {
     const isOverflowing = categories.scrollWidth > categories.clientWidth;
     if (isOverflowing) {
         leftBtn.style.display = 'block';
         rightBtn.style.display = 'block';
     } else {
         leftBtn.style.display = 'none';
         rightBtn.style.display = 'none';
     }
 }

 // Initial check for overflow when the page loads
 checkOverflow();

 // Optionally, recheck overflow when the window is resized
 window.addEventListener('resize', checkOverflow);

 // Add event listeners for scroll buttons
 leftBtn.addEventListener('click', () => {
     categories.scrollBy({
         left: -200,
         behavior: 'smooth',
     });
 });

 rightBtn.addEventListener('click', () => {
     categories.scrollBy({
         left: 200,
         behavior: 'smooth',
     });
 });
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