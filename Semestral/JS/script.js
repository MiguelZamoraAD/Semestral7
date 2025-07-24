//para ocultar elementos por usuario
document.addEventListener('DOMContentLoaded', function() {
    const elementosOcultables = document.querySelectorAll('.ocultarlogin');
    const rutaActual = window.location.pathname;

    // Si estás en 'page/Registro.php', ocultar el elemento
    if (rutaActual.endsWith('/Semestral/company_info.php') || rutaActual.endsWith('/Semestral/page/user.php') || rutaActual.endsWith('/Semestral/page/page1/studen.php') || rutaActual.endsWith('/Semestral/page/page1/registrar.php') || rutaActual.endsWith('/Semestral/page/page1/book.php')) {
        elementosOcultables.forEach(function(elemento) {
            elemento.style.display = 'none';
        });
    }
});

//JS para hacer que el menu funcione en dispositivos moviles
document.addEventListener('DOMContentLoaded', function() {
    // Para el menú hamburguesa universal
    const userToggle = document.querySelector('.user-menu-toggle');
    const navMenu = document.querySelector('.nav-menu ul');
    if (userToggle && navMenu) {
        userToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Si tienes otro menú con .menu-toggle y .nav-list, puedes agregar el código aquí
    const mainToggle = document.querySelector('.menu-toggle');
    const mainList = document.querySelector('.nav-list');
    if (mainToggle && mainList) {
        mainToggle.addEventListener('click', function() {
            mainList.classList.toggle('active');
        });
    }
});