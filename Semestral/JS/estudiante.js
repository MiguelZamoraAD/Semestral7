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

function registrarUsuario() {
    const cedula = document.getElementById("cedula").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const userID = document.getElementById("btnContraseña").dataset.id;

    const checkForm = new FormData();
    checkForm.append("Accion", "VerificarDuplicado");
    checkForm.append("Cedula", cedula);
    checkForm.append("Usuario", correo);
    if (editandoId) {
        checkForm.append("id", userId); // <- importante
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
                actualizarContrasena();
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", "Error al verificar duplicados.", "error");
        });
}

function actualizarContrasena() {
    const password = document.getElementById("pass1").value;
    const confirmPassword = document.getElementById("pass2").value;
    const userId = document.getElementById("btnRegistrar").dataset.editando;

    if (!validarPasswordSegura(password)) {
        Swal.fire("Error", "La contraseña debe tener al menos 8 caracteres, incluir letras, números y un símbolo especial.", "warning");
        return;
    }

    if (password !== confirmPassword) {
        Swal.fire("Error", "Las contraseñas no coinciden.", "error");
        return;
    }

    const formData = new FormData();
    formData.append("Password", password);
    formData.append("Accion", "EditarPassword");
    formData.append("id", userId);

    fetch("/Semestral7/Semestral/func/CRUD/estudianteFunc.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Éxito", data.message, "success");
                document.getElementById("contraseñaForm").reset();
                document.getElementById("fondoModal").style.display = "none";
            } else {
                Swal.fire("Error", data.message || "Ocurrió un error al actualizar.", "error");
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
        });
}


// Botón editar
document.getElementById("usuariosBody").addEventListener("click", actualizarContrasena); {
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
};


//validaviones
function validarPasswordSegura(password) {
    const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&\-_])[A-Za-z\d@$!%*#?&\-_]{8,}$/;
    return regex.test(password);
}