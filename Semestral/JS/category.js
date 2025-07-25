let editandoId = null;
let pagina = 1;
const categoriaCount = 9;
let totalCategorias = 0;

window.addEventListener('load', () => cargarCategorias(1));

document.addEventListener('DOMContentLoaded', () => {
    const btnMostrarFormulario = document.getElementById('btnMostrarFormulario');
    const btnCancelar = document.getElementById('btnCancelar');
    const formulario = document.getElementById('formularioCategoria');
    const formCrear = document.getElementById('crearCategoriaForm');
    const fondoModal = document.getElementById('fondoModal');

    if (btnMostrarFormulario) {
        btnMostrarFormulario.addEventListener('click', (e) => {
            e.preventDefault();
            fondoModal.style.display = 'flex';
            formulario.style.display = 'block';
        });
    }

    if (btnCancelar) {
        btnCancelar.addEventListener('click', () => {
            formulario.style.display = 'none';
            fondoModal.style.display = 'none';
            formCrear.reset(); // LIMPIA FORM
            document.getElementById("vistaPrevia").style.display = 'none';
            editandoId = null; // MUY IMPORTANTE
            document.querySelector('#crearCategoriaForm button[type="submit"]').textContent = "Guardar";

        });
    }

    formCrear.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(formCrear);

        if (editandoId) {
            formData.append('Accion', 'Editar');
            formData.append('id', editandoId);
        } else {
            formData.append('Accion', 'Crear');
        }

        fetch("/Semestral7/Semestral/func/CRUD/categoryFunc.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(editandoId ?
                        "✅ Categoría actualizada" : "✅ Categoria creada",
                        data.message, "success"
                    );
                    document.getElementById('formularioCategoria').style.display = 'none';
                    document.getElementById('fondoModal').style.display = 'none';
                    formCrear.reset();
                    document.getElementById("vistaPrevia").style.display = 'none';
                    document.getElementById("vistaPrevia").src = "";
                    cargarCategorias(); // Recargar lista
                    editandoId = null;
                    document.querySelector('#crearCategoriaForm button[type="submit"]').textContent = "Guardar";
                } else {
                    Swal.fire("❌ Error", data.message, "error");
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire("❌ Error", "Hubo un problema al crear la categoría.", "error");
            });
        cargarCategorias();
    });
});

function cargarCategorias(paginaActual = 1) {
    const formData = new FormData();
    formData.append("Accion", "Listar");
    formData.append("pagina", paginaActual);
    formData.append("limite", categoriaCount);

    fetch("/Semestral7/Semestral/func/CRUD/categoryFunc.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    //console.log("🚀 Categorias cargadas:", data); // <-- Aquí va el console.log
                    const container = document.getElementById("categoriasContainer");

                    if (!container) {
                        console.error("❌ Contenedor con id 'categoriasContainer' no encontrado.");
                        return;
                    }

                    let html = '<div class="services-grid">';
                    const categoria = data.data;
                    //const totalCategoria = categoria.length;
                    //console.log("📦 Categorías recibidas:", categoria);

                    //console.log("📄 Página actual:", paginaActual, "Inicio:", inicio, "Fin:", fin);

                    const categorias = data.data;
                    totalCategorias = data.total;

                    container.innerHTML = "";

                    if (categorias.length === 0) {
                        console.warn("⚠️ No hay categorías para mostrar en esta página.");
                    } else {
                        categorias.forEach(cat => {
                                    html += `
                            <div class="service-item">
                                <a  href="page2/Books.php?categoria=${encodeURIComponent(cat.titulo)}">
                                    <img src="${cat.ruta_imagen}" alt="${cat.titulo}" class="modulo-icon">
                                    <h3>${cat.titulo}</h3>
                                    <p>${cat.descripcion}</p>
                                    <span> Ver Libros</span><br>
                                </a>
                                ${tipoUsuario === 'adm' ? `
                                    <button class="btnEditar" data-id="${cat.id}">Editar</button>
                                    <button class="btnEliminar" data-id="${cat.id}">Eliminar</button>
                                    `:''}
                            </div>
                        `;
                    });
                }
                html += '</div>';
                container.innerHTML = html;
                //console.log("🧩 HTML generado:", html);
                // Paginación
                 pagina = paginaActual;
                document.getElementById('paginaActual').textContent = pagina;
                document.getElementById('btnAnterior').disabled = (pagina === 1);
                document.getElementById('btnSiguiente').disabled = (pagina >= Math.ceil(totalCategorias / categoriaCount));

            } else {
                Swal.fire("Error", "No se pudo obtener las categorias.", "error");
            }
        })
        .catch(error => {
            console.error("Error al listar:", error);
            Swal.fire("Error", "Fallo al comunicarse con el servidor.", "error");
        });
}

document.getElementById('btnAnterior').addEventListener('click', () => {
    if (pagina > 1) {
        cargarCategorias(pagina - 1);
    }
});

document.getElementById('btnSiguiente').addEventListener('click', () => {
    const totalPaginas = Math.ceil(totalCategorias / categoriaCount);
    if (pagina < totalPaginas) {
        cargarCategorias(pagina + 1);
    }
});

document.getElementById("categoriasContainer").addEventListener("click", function(e) {
    if (e.target.classList.contains("btnEliminar")) {
        const id = e.target.getAttribute("data-id");

        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarCategoria(id);
            }
        });
    }

    if (e.target.classList.contains("btnEditar")) {
        const id = e.target.getAttribute("data-id");
        obtenerCategoria(id);
    }

});

function eliminarCategoria(id) {
    const formData = new FormData();
    formData.append("Accion", "Eliminar");
    formData.append("id", id);

    fetch("/Semestral7/Semestral/func/CRUD/categoryFunc.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Eliminado", "La categoria fue Eliminada correctamente.", "success");
                cargarCategorias(pagina);
            } else {
                Swal.fire("Error", data.message || "No se pudo eliminar la categoria.", "error");
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", "Error al conectar con el servidor.", "error");
        });
}

function obtenerCategoria(id) {
    const formData = new FormData();
    formData.append("Accion", "Obtener");
    formData.append("id", id);

    fetch("/Semestral7/Semestral/func/CRUD/categoryFunc.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.querySelector('#crearCategoriaForm button[type="submit"]').textContent = "Actualizar";
        if (data.success && data.categoria) {
            const cat = data.categoria;
            const vistaPrevia = document.getElementById("vistaPrevia");

            // Llenar formulario
            document.getElementById("titulo").value = cat.titulo;
            document.getElementById("descripcion").value = cat.descripcion;
            document.getElementById("ruta_imagen").value = cat.ruta_imagen;
            document.getElementById("ruta_miniatura").value = cat.ruta_miniatura;

            editandoId = cat.id;
            vistaPrevia.src = `${cat.ruta_imagen}`;
            vistaPrevia.style.display = "block";

            // Mostrar formulario
            document.getElementById('formularioCategoria').style.display = 'block';
            document.getElementById('fondoModal').style.display = 'flex';

        } else {
            Swal.fire("Error", data.message || "No se pudo cargar la categoría.", "error");
        }
    })
    .catch(err => {
        console.error("Error al obtener categoría:", err);
        Swal.fire("Error", "Fallo al comunicarse con el servidor.", "error");
    });
}

document.querySelector('input[name="imagen"]').addEventListener('change', function (e) {
    const archivo = e.target.files[0];
    const vistaPrevia = document.getElementById("vistaPrevia");

    if (archivo) {
        const reader = new FileReader();
        reader.onload = function (e) {
            vistaPrevia.src = e.target.result;
            vistaPrevia.style.display = "block";
        };
        reader.readAsDataURL(archivo);
    }
});