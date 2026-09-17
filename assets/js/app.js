document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('deleteModal');
    const deleteId = document.getElementById('deleteId');
    const menuToggle = document.querySelector('.menu-toggle');
    const siteNav = document.getElementById('siteNav');

    if (deleteModal && deleteId) {
        deleteModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            deleteId.value = button?.getAttribute('data-delete-id') || '';
        });
    }

    if (menuToggle && siteNav) {
        menuToggle.addEventListener('click', () => {
            const open = siteNav.classList.toggle('is-open');
            menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }
});
