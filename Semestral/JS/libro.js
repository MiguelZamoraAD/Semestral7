document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    form.addEventListener('submit', async(e) => {
        e.preventDefault();

        const dias = parseInt(document.getElementById('diaR').value, 10);
        if (isNaN(dias) || dias < 1 || dias > 31) {
            Swal.fire({
                title: "Advertencia",
                text: "La reserva debe ser entre 1 y 31 días.",
                icon: "warning",
                confirmButtonText: "Aceptar"
            });
            return;
        }

        const libroId = new URLSearchParams(window.location.search).get('data-id');

        if (!document.getElementById('terms').checked) {
            Swal.fire({
                title: "Advertencia",
                text: "Debes aceptar los términos.",
                icon: "warning",
                confirmButtonText: "Aceptar"
            });
            return;
        }

        const formData = new FormData();
        formData.append('accion', 'registrar_reserva');
        formData.append('libro_id', libroId);
        formData.append('dias', dias);

        try {
            const res = await fetch('/Semestral7/Semestral/func/CRUD/libroFunc.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            Swal.fire({
                title: data.success ? "Éxito" : "Error",
                text: data.message,
                icon: data.success ? "success" : "error",
                confirmButtonText: "Aceptar"
            }).then(() => {
                if (data.success) {
                    location.href = '../CategoryBook.php';
                }
            });

        } catch (err) {
            console.error('Error:', err);
            Swal.fire({
                title: "Error",
                text: "Error al registrar reserva.",
                icon: "error",
                confirmButtonText: "Aceptar"
            });
        }
    });
});

document.getElementById('devolverBtn').addEventListener('click', async(e) => {
    e.preventDefault(); // para evitar comportamiento del botón

    const libroId = new URLSearchParams(window.location.search).get('data-id');

    const formData = new FormData();
    formData.append('accion', 'devolver_libro');
    formData.append('libro_id', libroId);

    try {
        const res = await fetch('/Semestral7/Semestral/func/CRUD/libroFunc.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        Swal.fire({
            title: data.success ? "Éxito" : "Error",
            text: data.message,
            icon: data.success ? "success" : "error",
            confirmButtonText: "Aceptar"
        }).then(() => {
            if (data.success) {
                location.reload(); // o redirigir si lo prefieres
            }
        });

    } catch (err) {
        console.error(err);
        Swal.fire({
            title: "Error",
            text: "Error al devolver el libro.",
            icon: "error",
            confirmButtonText: "Aceptar"
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const navList = document.querySelector('.nav-list');
    const navMenu = document.querySelector('.nav-menu ul');

    if (menuToggle && navList) {
        menuToggle.addEventListener('click', () => {
            navList.classList.toggle('active');
        });
    }

    if (userMenuToggle && navMenu) {
        userMenuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }
});