document.addEventListener('DOMContentLoaded', function () {
    const viewAllOrdersBtn = document.querySelector('.btn[href*="view_all_orders=true"]');
    if (viewAllOrdersBtn) {
        viewAllOrdersBtn.addEventListener('click', function (event) {
         
            this.textContent = 'Verberg alle bestellingen';
        });
    }
});

document.querySelector('.logout-form').addEventListener('submit', function (event) {
    if (!confirm('Weet je zeker dat je wilt uitloggen?')) {
        event.preventDefault();
    }
});

