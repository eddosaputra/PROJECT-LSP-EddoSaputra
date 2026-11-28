import './bootstrap';
import '../css/app.css';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import 'bootstrap-icons/font/bootstrap-icons.css';
document.addEventListener('DOMContentLoaded', function () {

    const announcementToast = document.querySelector('[data-login-announcements]');

    if (announcementToast) {
        const dismissAnnouncementToast = function () {
            announcementToast.classList.add('is-hiding');
            announcementToast.setAttribute('aria-hidden', 'true');
            window.setTimeout(function () {
                announcementToast.remove();
            }, 300);
        };

        const dismissButton = announcementToast.querySelector('[data-dismiss-login-announcements]');

        if (dismissButton) {
            dismissButton.addEventListener('click', dismissAnnouncementToast);
        }

        window.setTimeout(dismissAnnouncementToast, 5000);
    }

    document.querySelectorAll('[data-payment-form]').forEach(function (form) {
        const paymentMethod = form.querySelector('[data-payment-method]');
        const proofField = form.querySelector('[data-payment-proof-field]');
        const proofInput = form.querySelector('[data-payment-proof]');

        const updateProofRequirement = function () {
            const isCash = paymentMethod.value === 'cash';

            proofField.hidden = isCash;
            proofInput.required = !isCash;

            if (isCash) {
                proofInput.value = '';
            }
        };

        paymentMethod.addEventListener('change', updateProofRequirement);
        updateProofRequirement();
    });

    const menuLinks = document.querySelectorAll('.gym-menu-link');

    menuLinks.forEach(link => {
        link.addEventListener('click', function () {

            menuLinks.forEach(item => {
                item.classList.remove('active');
            });

            this.classList.add('active');
        });
    });

    const sidebarLinks = Array.from(document.querySelectorAll('.dashboard-sidebar .sidebar-link'));

    if (sidebarLinks.length > 0) {
        const updateActiveSidebarLink = function () {
            const currentHash = window.location.hash;
            const activeLink = sidebarLinks.find(function (link) {
                const target = new URL(link.href, window.location.href);

                return target.pathname === window.location.pathname
                    && target.hash === currentHash;
            });

            if (activeLink) {
                sidebarLinks.forEach(function (link) {
                    link.classList.toggle('active', link === activeLink);
                });
            }
        };

        updateActiveSidebarLink();
        window.addEventListener('hashchange', updateActiveSidebarLink);

        sidebarLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                const target = new URL(link.href, window.location.href);

                if (target.pathname === window.location.pathname) {
                    window.setTimeout(updateActiveSidebarLink, 0);
                }
            });
        });
    }

});