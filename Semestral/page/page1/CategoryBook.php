<?php 
session_start();
//var_dump($_SESSION['Tipo']); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Módulo de Noticias</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../resourse/css/princ.css" />
    <script> const tipoUsuario = "<?php echo isset($_SESSION['Tipo']) ? strtolower($_SESSION['Tipo']) : ''; ?>"; </script>

</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="logo">Categorias de Libros</h1>
            <nav class="nav-menu">
                    <button class="user-menu-toggle" aria-label="Abrir menú de navegación">&#9776;</button>
                    <ul class="nav-list">
                        <li ><a href="../company_info.php" >Inicio</a></li>
                        <?php if (isset($_SESSION['Tipo']) && strtolower($_SESSION['Tipo']) === 'adm'): ?>
                        <li ><a href="#" >Usuario</a></li>
                        <?php endif; ?>
                        <li ><a href="studen.php" >Estudiante</a></li>
                        <li ><a href="book.php" >Libros</a></li>
                        <li ><a href="CategoryBook.php" >Categorias</a></li>
                        <?php if (isset($_SESSION['Tipo']) && strtolower($_SESSION['Tipo']) === 'adm'): ?>
                            <li><button href="CategoryBook.php?crear=1"  id="btnMostrarFormulario">➕ Crear nueva categoría</button></li>
                        <?php endif; ?>
                    </ul>
            </nav>
        </div>
    </header>

    <section>
        <div class="container">
            <h2 class="h2-company">Categorias</h2>
            <main class="main-content" id="categoriasContainer">
              <!-- Aquí se cargarán las categorías dinámicamente -->
            </main>
        <div id="fondoModal" style="display:none">
            <div id="formularioCategoria" class="formulario-oculto">
                <h3>Crear Nueva Categoría</h3>
                <form id="crearCategoriaForm" enctype="multipart/form-data">
                    <input type="text" id="titulo" name="titulo" placeholder="Título" required><br>
                    <textarea name="descripcion" id="descripcion" placeholder="Descripción" required></textarea><br>
                    <input type="file" name="imagen" accept="image/*"><br>
                    <input type="hidden" id="ruta_imagen" name="ruta_imagen">
                    <input type="hidden" id="ruta_miniatura" name="ruta_miniatura">

                    <!-- Vista previa de imagen actual o nueva -->
                    <img id="vistaPrevia" src="" alt="Vista previa" style="display:none; max-width: 200px; margin-top: 10px;">
                    <button type="submit">Guardar</button>
                    <button type="button" id="btnCancelar">Cancelar</button>
                </form>
            </div>
        </div>
        </div>
    </section>
    <div class="paginacion-usuarios">
        <button class="btnAnterior" id="btnAnterior">&laquo;</button>
        <span class="btn-paginacion activo" id="paginaActual"></span>
        <button class="btnSiguiente" id="btnSiguiente">&raquo;</button>
    </div>
    <script src="../../js/script.js"></script>
    <script src="../../js/category.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
<?php include('../../resourse/include/footer.php')?>
