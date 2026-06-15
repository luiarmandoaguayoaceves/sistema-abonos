import './bootstrap';
import './pedidos';
import './seguimiento';
import './facturas';

document.addEventListener('DOMContentLoaded', () => {
    const btnMenu = document.getElementById('btn-menu');
    const menuMovil = document.getElementById('menu-movil');

    if (!btnMenu || !menuMovil) {
        return;
    }

    btnMenu.addEventListener('click', () => {
        menuMovil.classList.toggle('hidden');
    });
});