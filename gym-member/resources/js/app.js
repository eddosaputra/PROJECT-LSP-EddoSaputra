import './bootstrap';
import '../css/app.css';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import 'bootstrap-icons/font/bootstrap-icons.css';
document.addEventListener('DOMContentLoaded', function () {

    const menuLinks = document.querySelectorAll('.gym-menu-link');

    menuLinks.forEach(link => {
        link.addEventListener('click', function () {

            menuLinks.forEach(item => {
                item.classList.remove('active');
            });

            this.classList.add('active');
        });
    });

});