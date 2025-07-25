let editandoId = null;
let pagina = 1;
const librosCount = 5;
let totalLibros = 0;
const params = new URLSearchParams(window.location.search);
const categoriaSeleccionada = params.get('categoria');

window.addEventListener('load', () => cargarlibros(1, categoriaSeleccionada));

document.addEventListener('DOMContentLoaded', () => {
    const btnMostrarFormulario = document.getElementById('btnMostrarFormulario');
    const btnCancelar = document.getElementById('btnCancelar');
    const formulario = document.getElementById('formularioLibro');
    const formCrear = document.getElementById('crearLibrosForm');
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
            document.querySelector('#crearLibrosForm button[type="submit"]').textContent = "Guardar";

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

        fetch("/Semestral7/Semestral/func/CRUD/bookFunc.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(editandoId ?
                        "✅ Categoría actualizada" : "✅ Libros creada",
                        data.message, "success"
                    );
                    document.getElementById('formularioLibro').style.display = 'none';
                    document.getElementById('fondoModal').style.display = 'none';
                    formCrear.reset();
                    document.getElementById("vistaPrevia").style.display = 'none';
                    document.getElementById("vistaPrevia").src = "";
                    cargarlibros(); // Recargar lista
                    editandoId = null;
                    document.querySelector('#crearLibrosForm button[type="submit"]').textContent = "Guardar";
                } else {
                    Swal.fire("❌ Error", data.message, "error");
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire("❌ Error", "Hubo un problema al crear el Libro.", "error");
            });
        cargarlibros();
    });
});

document.getElementById("btnBuscar").addEventListener("click", () => {
    paginaActual = 1;
    cargarlibros(pagina, categoriaSeleccionada); // Que cargue usando el nuevo término
});

function cargarlibros(paginaActual = 1, categoria = null) {
    const formData = new FormData();
    const textoBusqueda = document.getElementById("inputBuscar").value.trim();
    formData.append("busqueda", textoBusqueda);
    formData.append("Accion", "Listar");
    formData.append("pagina", paginaActual);
    formData.append("categoria", categoria);
    formData.append("limite", librosCount);

    if (categoria) {
        formData.append("categoria", categoria);
    }

    fetch("/Semestral7/Semestral/func/CRUD/bookFunc.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    //console.log("🚀 libros cargadas:", data); // <-- Aquí va el console.log
                    const librosContainer = document.getElementById("librosContainer");

                    if (!librosContainer) {
                        console.error("❌ Contenedor con id 'librosContainer' no encontrado.");
                        return;
                    }

                    let html = '<div class="services-grid">';
                    //const totalLibros = Libros.length;
                    //console.log("📦 Categorías recibidas:", Libros);

                    //console.log("📄 Página actual:", paginaActual, "Inicio:", inicio, "Fin:", fin);

                    const libros = data.data;
                    totalLibros = data.total;

                    librosContainer.innerHTML = "";
                    if (!categoria) {
                        html += `<p>Libro no especificada.</p>`;
                    } else if (libros.length === 0) {
                        console.warn("⚠️ No hay Libros para mostrar en esta página.");
                    } else {
                        libros.forEach(lib => {
                                    const sinStock = lib.unidades === 0 ? "sin-stock" : "";
                                    html += `
                            <div class="service-item ${sinStock}">
                                <a>
                                    <img src="${lib.ruta_imagen}" alt="${lib.titulo}" class="modulo-icon">
                                    <h3>${lib.titulo}</h3>
                                    <p>${lib.descripcion}</p>
                                    <p class="${sinStock}">Cantidad disponible: ${lib.unidades}</p>
                                </a>
                                <a href="detalleLibro.php?data-id=${lib.id}">Ver Mas informacion</a><br>
                                ${tipoUsuario === 'adm' ? `
                                    <button class="btnEditar" data-id="${lib.id}">Editar</button>
                                    <button class="btnEliminar" data-id="${lib.id}">Eliminar</button>
                                    `:''}
                            </div>
                        `;
                    });
                }
                html += '</div>';
                librosContainer.innerHTML = html;
                //console.log("🧩 HTML generado:", html);
                // Paginación
                 pagina = paginaActual;
                document.getElementById('paginaActual').textContent = pagina;
                document.getElementById('btnAnterior').disabled = (pagina === 1);
                document.getElementById('btnSiguiente').disabled = (pagina >= Math.ceil(totalLibros / librosCount));

            } else {
                Swal.fire("Error", "No se pudo obtener las libros.", "error");
            }
        })
        .catch(error => {
            console.error("Error al listar:", error);
            Swal.fire("Error", "Fallo al comunicarse con el servidor.", "error");
        });
        
}


document.getElementById('btnAnterior').addEventListener('click', () => {
    if (pagina > 1) {
        cargarlibros(pagina - 1);
    }
});

document.getElementById('btnSiguiente').addEventListener('click', () => {
    const totalPaginas = Math.ceil(totalLibros / librosCount);
    if (pagina < totalPaginas) {
        cargarlibros(pagina + 1);
    }
});
document.getElementById("btnLimpiar").addEventListener("click", () => {
    document.getElementById("inputBuscar").value = '';
    paginaActual = 1;
    cargarlibros(pagina, categoriaSeleccionada);
});

document.getElementById("librosContainer").addEventListener("click", function(e) {
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
                eliminarLibros(id);
            }
        });
    }

    if (e.target.classList.contains("btnEditar")) {
        const id = e.target.getAttribute("data-id");
        obtenerLibros(id);
    }

});

function eliminarLibros(id) {
    const formData = new FormData();
    formData.append("Accion", "Eliminar");
    formData.append("id", id);

    fetch("/Semestral7/Semestral/func/CRUD/bookFunc.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Eliminado", "La Libros fue Eliminada correctamente.", "success");
                cargarlibros(pagina);
            } else {
                Swal.fire("Error", data.message || "No se pudo eliminar la Libros.", "error");
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", "Error al conectar con el servidor.", "error");
        });
}

function obtenerLibros(id) {
    const formData = new FormData();
    formData.append("Accion", "Obtener");
    formData.append("id", id);

    fetch("/Semestral7/Semestral/func/CRUD/bookFunc.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.querySelector('#crearLibrosForm button[type="submit"]').textContent = "Actualizar";
        if (data.success && data.Libro && data.Libro.categoria) {
            const cat = data.Libro.categoria;
            const vistaPrevia = document.getElementById("vistaPrevia");

            // Llenar formulario
            document.getElementById("titulo").value = cat.titulo;
            document.getElementById("descripcion").value = cat.descripcion;
            document.getElementById("unidades").value = cat.unidades;
            document.getElementById("categoriatipos").value = cat.categoria || "";

            document.getElementById("ruta_imagen").value = cat.ruta_imagen;
            document.getElementById("ruta_miniatura").value = cat.ruta_miniatura;

            if (cat.ruta_imagen) {
                vistaPrevia.src = cat.ruta_imagen;
                vistaPrevia.style.display = "block";
            } else {
                vistaPrevia.style.display = "none";
            }
            editandoId = cat.id;
            
            // Mostrar formulario
            document.getElementById('formularioLibro').style.display = 'block';
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