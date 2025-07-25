// DOMContentLoaded principal
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("btnContraseña").addEventListener("click", registrarUsuario);

    // Botón cancelar edición dinámicamente
    const cancelBtn = document.createElement("button");
    cancelBtn.id = "btnCancelarEdicion";
    cancelBtn.textContent = "Cancelar Edición";
    cancelBtn.style.display = "none";
    cancelBtn.classList.add("cancelar-edicion");
    document.getElementById("contraseñaForm").appendChild(cancelBtn);

    cancelBtn.addEventListener("click", () => {
        document.getElementById("registroForm").reset();
        delete document.getElementById("btnContraseña").dataset.editando;
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
    const editandoId = document.getElementById("btnContraseña").dataset.editando;

    const checkForm = new FormData();
    checkForm.append("Accion", "VerificarDuplicado");
    checkForm.append("Cedula", cedula);
    checkForm.append("Usuario", correo);
    if (editandoId) {
        checkForm.append("id", editandoId); // <- importante
    }

    fetch("/Semestral7/Semestral/func/CRUD/estudianteFunc.php", {
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
    const editandoId = document.getElementById("btnContraseña").dataset.editando;


    formData.append("Password", document.getElementById("pass1").value);
    formData.append("confirmPassword", document.getElementById("pass2").value);

    formData.append("Accion", "Editar");
    formData.append("id", editandoId);

    const password = formData.get("Password");
    const confirmPassword = formData.get("confirmPassword");

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

    fetch("/Semestral7/Semestral/func/CRUD/estudianteFunc.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Éxito", data.message, "success");
                document.getElementById("containerForm").reset();
                delete document.getElementById("btnContraseña").dataset.editando;
                document.getElementById("btnCancelarEdicion").style.display = "none";
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

                    document.getElementById("btnContraseña").dataset.editando = u.id;
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
function validarPasswordSegura(password) {
    const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&\-_])[A-Za-z\d@$!%*#?&\-_]{8,}$/;
    return regex.test(password);
}