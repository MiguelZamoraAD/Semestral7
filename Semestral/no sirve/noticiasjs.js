let pagina = 1;
const noticiasPorPagina = 5;

function cargarNoticias(paginaActual = 1) {
    const formData = new FormData();
    formData.append("Accion", "Listar");

    fetch("../Funciones/registarNoti.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('noticiasContainer');
            const noticias = data.data;
            const totalNoticias = noticias.length;
            const inicio = (paginaActual - 1) * noticiasPorPagina;
            const fin = inicio + noticiasPorPagina;
            const noticiasPaginadas = noticias.slice(inicio, fin);

            container.innerHTML = '';
            noticiasPaginadas.forEach(n => {
                container.innerHTML += `
                <article>
                    <h2>${n.titulo}</h2>
                    <p class="post-meta">Publicado el ${n.fecha}</p>
                    <div class="post-content">
                        <div class="adverbs-image">
                            <img src="${n.ruta_miniatura}" alt="${n.titulo}" />
                        </div>
                        <div class="news-items">
                            <p>${cortarContenido(n.contenido)}</p>
                            <a href="detalleNoticia.php?id=${n.id}" class="more-button">More <span style="font-size: 0.8em;">&#x27A4;</span></a>
                        </div>
                    </div>
                </article>
                <br>`;
            });

            document.getElementById('paginaActual').textContent = paginaActual;
            pagina = paginaActual;

            document.getElementById('btnAnterior').disabled = (pagina === 1);
            document.getElementById('btnSiguiente').disabled = (fin >= totalNoticias);
        })
        .catch(error => {
            console.error('Error cargando noticias:', error);
        });
}

//funcion para cortar bien el contenido mostrado
function cortarContenido(texto, max = 150) {
    if (texto.length <= max) return texto;
    const corte = texto.indexOf(" ", max);
    return texto.substring(0, corte !== -1 ? corte : max) + "...";
}


// Paginación
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById('btnAnterior').addEventListener('click', () => {
        if (pagina > 1) cargarNoticias(pagina - 1);
    });

    document.getElementById('btnSiguiente').addEventListener('click', () => {
        cargarNoticias(pagina + 1);
    });

    // Cargar la primera página al inicio
    cargarNoticias(1);
});

//listar noticias
function listarNoticias() {
    const formData = new FormData();
    formData.append("Accion", "Listar");

    fetch('../Funciones/registarNoti.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('newsTableBody');
            tbody.innerHTML = '';
            data.data.forEach(noticia => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${noticia.id}</td>
                <td>${noticia.titulo}</td>
                <td><img src="${noticia.ruta_miniatura}" height="50"></td>
                <td>
                    <button onclick="editNews(${noticia.id})">Editar</button>
                    <button onclick="deleteNews(${noticia.id})">Eliminar</button>
                </td>
            `;
                tbody.appendChild(row);
            });
        });
}

//guardar noticias
function guardarNoticia() {
    const titulo = document.getElementById('titulo').value.trim();
    const contenido = document.getElementById('contenido').value.trim();
    const imagenInput = document.getElementById('imagen');
    const imagen = imagenInput.files[0];

    if (!titulo || !contenido || !imagen) {
        Swal.fire("Error", "Completa todos los campos y selecciona una imagen.", "warning");
        return;
    }

    const formData = new FormData();
    formData.append("Accion", "Guardar");
    formData.append("titulo", titulo);
    formData.append("contenido", contenido);
    formData.append("imagen", imagen);
    console.log("Enviando noticia:", titulo, contenido, imagen);

    fetch("../../Funciones/registarNoti.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Éxito", data.message, "success");
                document.getElementById('formNoticia').reset();
                document.getElementById("imagePreview").innerHTML = "<p>Vista previa de la imagen</p>";
                listarNoticias(); // Si estás en la tabla
            } else {
                Swal.fire("Error", data.message || "No se pudo guardar la noticia.", "error");
                console.error(data.errors);
            }
        })
        .catch(error => {
            console.error("Error al guardar:", error);
            Swal.fire("Error", "Fallo al comunicarse con el servidor.", "error");
        });
}

//evento para guardar los datos
document.addEventListener("DOMContentLoaded", () => {
    // Botones de paginación
    const btnAnterior = document.getElementById('btnAnterior');
    const btnSiguiente = document.getElementById('btnSiguiente');

    if (btnAnterior && btnSiguiente) {
        btnAnterior.addEventListener('click', () => {
            if (pagina > 1) cargarNoticias(pagina - 1);
        });

        btnSiguiente.addEventListener('click', () => {
            cargarNoticias(pagina + 1);
        });

        cargarNoticias(1);
    }

    // Botón de registrar noticia
    const publicarBtn = document.getElementById('publicarBtn');
    if (publicarBtn) {
        publicarBtn.addEventListener("click", guardarNoticia);
    }
});