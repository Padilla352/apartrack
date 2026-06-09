document.addEventListener('DOMContentLoaded', function () {
    const dropdownTriggers = document.querySelectorAll('[data-dropdown-target]');
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');

    function closeDropdowns() {
        dropdownMenus.forEach((menu) => menu.classList.remove('show'));
        dropdownTriggers.forEach((trigger) => trigger.setAttribute('aria-expanded', 'false'));
    }

    dropdownTriggers.forEach((trigger) => {
        trigger.addEventListener('click', function (event) {
            event.stopPropagation();

            const targetId = this.getAttribute('data-dropdown-target');
            const targetMenu = document.getElementById(targetId);
            const willOpen = targetMenu && !targetMenu.classList.contains('show');

            closeDropdowns();

            if (targetMenu && willOpen) {
                targetMenu.classList.add('show');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.addEventListener('click', function (event) {
        const clickedInsideDropdown = event.target.closest('.profile-dropdown, .guestmode-dropdown');
        if (!clickedInsideDropdown) {
            closeDropdowns();
        }
    });
});
