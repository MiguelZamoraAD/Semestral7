let pagina = 1;
const elementosPorPagina = 3;
let totalUsuarios = 0;
let textoBusqueda = "";

// variables globales

// DOMContentLoaded principal
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("btnRegistrar").addEventListener("click", registrarUsuario);

    // Búsqueda
    document.getElementById("inputBuscar").addEventListener("input", (e) => {
        textoBusqueda = e.target.value.trim();
        cargarUsuarios(1);
    });

    document.getElementById('btnAnterior').addEventListener('click', () => {
        if (pagina > 1) cargarUsuarios(pagina - 1);
    });

    document.getElementById('btnSiguiente').addEventListener('click', () => {
        if (pagina < Math.ceil(totalUsuarios / elementosPorPagina)) {
            cargarUsuarios(pagina + 1);
        }
    });

    document.getElementById("btnLimpiar").addEventListener("click", () => {
        textoBusqueda = "";
        document.getElementById("inputBuscar").value = "";
        cargarUsuarios(1);
    });

    cargarUsuarios();

    // Botón cancelar edición dinámicamente
    const cancelBtn = document.createElement("button");
    cancelBtn.id = "btnCancelarEdicion";
    cancelBtn.textContent = "Cancelar Edición";
    cancelBtn.style.display = "none";
    cancelBtn.classList.add("cancelar-edicion");
    document.getElementById("registroForm").appendChild(cancelBtn);

    cancelBtn.addEventListener("click", () => {
        document.getElementById("registroForm").reset();
        delete document.getElementById("btnRegistrar").dataset.editando;
        cancelBtn.style.display = "none";
        Swal.fire("Cancelado", "Edición cancelada.", "info");
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const toggle = document.querySelector(".user-menu-toggle");
    const navList = document.querySelector(".nav-list");

    toggle.addEventListener("click", function() {
        navList.classList.toggle("active");
    });
});

function registrarUsuario() {
    const cedula = document.getElementById("cedula").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const editandoId = document.getElementById("btnRegistrar").dataset.editando;

    const checkForm = new FormData();
    checkForm.append("Accion", "VerificarDuplicado");
    checkForm.append("Cedula", cedula);
    checkForm.append("Usuario", correo);
    if (editandoId) {
        checkForm.append("id", editandoId); // <- importante
    }

    fetch("/Semestral7/Semestral/func/CRUD/userFunc.php", {
            method: "POST",
            body: checkForm
        })
        .then(res => res.json())
        .then(data => {
            if (data.existe) {
                Swal.fire("Error", "Ya existe un usuario con esta cédula o correo.", "warning");
            } else {
                enviarFormularioRegistro();
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", "Error al verificar duplicados.", "error");
        });
}

function enviarFormularioRegistro() {
    const formData = new FormData();
    const editandoId = document.getElementById("btnRegistrar").dataset.editando;

    formData.append("Cedula", document.getElementById("cedula").value.trim());
    formData.append("Nombre", document.getElementById("nombre").value.trim());
    formData.append("SegundoN", document.getElementById("segundoN").value.trim());
    formData.append("Apellido", document.getElementById("apellido").value.trim());
    formData.append("SegundoA", document.getElementById("segundoA").value.trim());
    formData.append("FechaNacimiento", document.getElementById("fechaNacimiento").value);
    formData.append("Carrera", document.getElementById("carrera").value.trim());
    formData.append("Tipo", document.getElementById("tipo").value);
    formData.append("Sexo", document.getElementById("sexo").value);
    formData.append("Usuario", document.getElementById("correo").value.trim());
    formData.append("Password", document.getElementById("pass1").value);
    formData.append("confirmPassword", document.getElementById("pass2").value);

    if (editandoId) {
        formData.append("Accion", "Editar");
        formData.append("id", editandoId);
    } else {
        formData.append("Accion", "Guardar");
    }

    const requiredFields = ["Cedula", "Nombre", "Apellido", "FechaNacimiento", "Carrera", "Usuario", "Tipo", "Sexo"];
    for (let campo of requiredFields) {
        if (!formData.get(campo)) {
            Swal.fire("Error", "Por favor, complete todos los campos obligatorios.", "warning");
            return;
        }
    }

    const cedula = (formData.get("Cedula") || "").trim();
    if (!validarCedula(cedula)) {
        Swal.fire("Error", "Formato de cédula inválido. <br> Use 01-1234-5678 o E-1-123456.", "warning");
        return;
    }

    const password = formData.get("Password");
    const confirmPassword = formData.get("confirmPassword");

    if (!editandoId) {
        // Registro nuevo: contraseña obligatoria
        if (!password || !confirmPassword) {
            Swal.fire("Error", "Debe ingresar y confirmar una contraseña.", "warning");
            return;
        }
    }

    if (password || confirmPassword) {
        if (!validarPasswordSegura(password)) {
            Swal.fire("Error", "La contraseña debe tener al menos 8 caracteres, incluir letras, números y un símbolo especial.", "warning");
            return;
        }
        if (password !== confirmPassword) {
            Swal.fire("Error", "Las contraseñas no coinciden.", "error");
            return;
        }
    } else if (editandoId) {
        // Si es edición y no se ingresó contraseña, eliminar los campos
        formData.delete("Password");
        formData.delete("confirmPassword");
    }

    fetch("/Semestral7/Semestral/func/CRUD/userFunc.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Éxito", data.message, "success");
                document.getElementById("registroForm").reset();
                delete document.getElementById("btnRegistrar").dataset.editando;
                document.getElementById("btnCancelarEdicion").style.display = "none";
                cargarUsuarios(1);
            } else {
                Swal.fire("Error", data.message || "Ocurrió un error al registrar.", "error");
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
        });
}

// Botón editar
document.getElementById("usuariosBody").addEventListener("click", function(e) {
    if (e.target.classList.contains("btnEditar")) {
        const id = e.target.dataset.id;

        const formData = new FormData();
        formData.append("Accion", "Obtener");
        formData.append("id", id);

        fetch("/Semestral7/Semestral/func/CRUD/userFunc.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.usuario) {
                    const u = data.usuario;

                    // Rellenar formulario con los datos
                    document.getElementById("cedula").value = u.Cedula;
                    document.getElementById("nombre").value = u.Nombre;
                    document.getElementById("segundoN").value = u.SegundoN;
                    document.getElementById("apellido").value = u.Apellido;
                    document.getElementById("segundoA").value = u.SegundoA;
                    document.getElementById("fechaNacimiento").value = u.FechaNacimiento;
                    document.getElementById("carrera").value = u.Carrera;
                    document.getElementById("tipo").value = u.Tipo;
                    document.getElementById("sexo").value = u.Sexo;
                    document.getElementById("correo").value = u.Usuario;

                    document.getElementById("btnRegistrar").dataset.editando = u.id;
                    document.getElementById("btnCancelarEdicion").style.display = "inline-block";

                    Swal.fire("Modo edición", "Ahora puedes editar este usuario", "info");
                } else {
                    Swal.fire("Error", data.message || "No se pudo obtener el usuario", "error");
                }
            })
            .catch(err => {
                console.error("Error:", err);
                Swal.fire("Error", "No se pudo conectar al servidor.", "error");
            });
    }
});


//validaviones
function validarCedula(cedula) {
    const regexNacional = /^\d{2}-\d{4}-\d{4}$/;
    const regexExtranjero = /^E-\d+-\d+$/;
    return regexNacional.test(cedula) || regexExtranjero.test(cedula);
}

function validarPasswordSegura(password) {
    const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&\-_])[A-Za-z\d@$!%*#?&\-_]{8,}$/;
    return regex.test(password);
}

//carga de usuarios
function cargarUsuarios(paginaActual = 1) {
    const formData = new FormData();
    formData.append("Accion", "Listar");
    formData.append("pagina", paginaActual);
    formData.append("limite", elementosPorPagina);
    formData.append("busqueda", textoBusqueda);

    fetch("/Semestral7/Semestral/func/CRUD/userFunc.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById("usuariosBody");
            tbody.innerHTML = "";

            if (data.success && Array.isArray(data.data)) {
                data.data.forEach(usuario => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                    <td>${usuario.id}</td>
                    <td>${usuario.Nombre}</td>
                    <td>${usuario.Apellido}</td>
                    <td>${ocultarCorreo(usuario.Usuario)}</td>
                    <td>${usuario.Tipo}</td>
                    <td>${usuario.Sexo}</td>
                    <td>${cortarContenido(usuario.HashMagic)}</td>
                    <td>
                        <button class="btnEditar" data-id="${usuario.id}">Editar</button>
                        <button class="btnEliminar" data-id="${usuario.id}">Eliminar</button>
                    </td>
                `;
                    tbody.appendChild(row);
                });

                totalUsuarios = data.total || 0;
                pagina = paginaActual;
                document.getElementById('paginaActual').textContent = pagina;
                document.getElementById('btnAnterior').disabled = (pagina === 1);
                document.getElementById('btnSiguiente').disabled = (pagina >= Math.ceil(totalUsuarios / elementosPorPagina));

                document.getElementById("registroForm").reset();
                delete document.getElementById("btnRegistrar").dataset.editando;
                document.getElementById("btnCancelarEdicion").style.display = "none";

            } else {
                Swal.fire("Error", data.error || "Ocurrió un error al mostrar los usuarios.", "error");
            }
        })
        .catch(error => {
            console.error("Error al cargar usuarios:", error);
            Swal.fire("Error", "No se pudo conectar al servidor.", "error");
        });
}

// Botón eliminar

document.getElementById("usuariosBody").addEventListener("click", function(e) {
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
                eliminarUsuario(id);
            }
        });
    }
});

function eliminarUsuario(id) {
    const formData = new FormData();
    formData.append("Accion", "Eliminar");
    formData.append("id", id);

    fetch("/Semestral7/Semestral/func/CRUD/userFunc.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Eliminado", "El usuario fue eliminado correctamente.", "success");
                cargarUsuarios(pagina);
            } else {
                Swal.fire("Error", data.message || "No se pudo eliminar el usuario.", "error");
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", "Error al conectar con el servidor.", "error");
        });
}

function cortarContenido(texto, max = 10) {
    if (texto.length <= max) return texto;
    const corte = texto.indexOf(" ", max);
    return texto.substring(0, corte !== -1 ? corte : max) + "...";
}

function ocultarCorreo(correo) {
    const [usuario, dominio] = correo.split('@');

    if (usuario.length <= 3) {
        return usuario[0] + '***@' + dominio;
    }

    const visibles = usuario.slice(0, 3);
    return `${visibles}***@${dominio}`;
}