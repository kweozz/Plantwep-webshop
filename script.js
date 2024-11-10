document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('click', (e) => {
        // Voorkom standaard linkgedrag als je alleen een voorbeeld wilt
        e.preventDefault();

        // Verwijder de "active" klasse van alle kaarten
        document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));

        // Voeg de "active" klasse toe aan de geklikte kaart
        card.classList.add('active');
    });
});
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
