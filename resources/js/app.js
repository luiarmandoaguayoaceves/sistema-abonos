import './bootstrap';
import './pedidos';

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