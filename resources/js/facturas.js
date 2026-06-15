document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-facturas-page]');

    if (!page) {
        return;
    }

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-confirm-delete]');

        if (!form) {
            return;
        }

        const confirmed = window.confirm('¿Eliminar esta factura para subir otra?');

        if (!confirmed) {
            event.preventDefault();
        }
    });

    const fileInput = document.querySelector('[data-pdf-input]');
    const fileNameLabel = document.querySelector('[data-pdf-name]');

    if (!fileInput || !fileNameLabel) {
        return;
    }

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];

        fileNameLabel.textContent = file
            ? file.name
            : 'Ningún archivo seleccionado';
    });
});