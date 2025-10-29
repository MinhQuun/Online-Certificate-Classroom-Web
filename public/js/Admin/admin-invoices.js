document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.invoice-row');

    rows.forEach((row) => {
        const href = row.getAttribute('data-href');
        if (!href) {
            return;
        }

        row.addEventListener('click', (event) => {
            const target = event.target;
            if (target.closest('a, button')) {
                return;
            }
            window.location.href = href;
        });
    });
});
