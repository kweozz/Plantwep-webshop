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

// Save the current scroll position before navigating
document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('click', () => {
        // Save the current scroll position to localStorage
        localStorage.setItem('scrollPosition', window.scrollY);
    });
});

// Restore the scroll position on page load
document.addEventListener('DOMContentLoaded', () => {
    const savedPosition = localStorage.getItem('scrollPosition');
    if (savedPosition) {
        window.scrollTo(0, parseInt(savedPosition, 10));
        localStorage.removeItem('scrollPosition'); // Clear the position after restoring
    }
});