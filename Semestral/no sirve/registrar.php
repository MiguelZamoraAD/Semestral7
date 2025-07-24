
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Noticia</title>
    <link rel="stylesheet" href="../../resourse/css/regi.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="form-container">
        <h2>Cargar Nueva Noticia</h2>
        <form id="formNoticia" enctype="multipart/form-data" method="post">
            <div class="form-group">
                <label for="titulo">Título de la Noticia:</label>
                <input type="text" id="titulo" name="titulo" required placeholder="Ingresa el título de la noticia">
            </div>

            <div class="form-group">
                <label for="contenido">Contenido de la Noticia:</label>
                <textarea id="contenido" name="contenido" required placeholder="Escribe el contenido completo de la noticia"></textarea>
            </div>

            <div class="form-group">
                <label for="imagen">Seleccionar Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png" required>
                <div class="image-preview" id="imagePreview">
                    <p>Vista previa de la imagen</p>
                </div>
            </div>

            <button type="button" id="publicarBtn" method="post">Publicar Noticia</button>

            <div class="SMALL">
                <small><br><a href="../moduloN.php">Regresar</a>
            </small>
            </div>
        </form>
    </div>

    <script>
        const imageInput = document.getElementById('imagen');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Vista previa de la imagen">`;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = '<p>Vista previa de la imagen</p>';
            }
        });
    </script>
    <script src="../../JS/noticiasjs.js"></script>
</body>
</html>