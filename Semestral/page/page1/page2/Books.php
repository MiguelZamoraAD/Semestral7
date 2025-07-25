<?php 
session_start();
//var_dump($_SESSION['Tipo']); 
require_once '../../../class/conexion.php';
$db = new Conexion();       // Creamos instancia
$conn = $db->getConexion();

$categoria = $_GET['categoria'] ?? null;
$libros = [];
//var_dump($_GET['categoria']??null);

if ($categoria) {
    try {
        $stmt = $conn->prepare("SELECT * FROM libros WHERE categoria = ?");
        $stmt->execute([$categoria]);
        $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al obtener libros: " . $e->getMessage());
    }
}
//var_dump($libros);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Módulo de Noticias</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../../resourse/css/princ.css" />
    <script> const tipoUsuario = "<?php echo isset($_SESSION['Tipo']) ? strtolower($_SESSION['Tipo']) : ''; ?>"; </script>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="logo">Libros de  <?= htmlspecialchars($categoria) ?></h1>
            <nav class="nav-menu">
                    <button class="user-menu-toggle" aria-label="Abrir menú de navegación">&#9776;</button>
                    <ul class="nav-list">
                        <li ><a href="../../company_info.php" >Inicio</a></li>
                        <?php if (isset($_SESSION['Tipo']) && strtolower($_SESSION['Tipo']) === 'adm'): ?>
                        <li ><a href="../../user.php" >Usuario</a></li>
                        <?php endif; ?> 
                        <li ><a href="../studen.php" >Estudiante</a></li>
                        <li ><a href="../CategoryBook.php" >Categorias</a></li>
                        <?php if (isset($_SESSION['Tipo']) && strtolower($_SESSION['Tipo']) === 'adm'): ?>
                            <li><button href="Books.php?crear=1"  id="btnMostrarFormulario">➕ Subir un nuevo libro</button></li>
                        <?php endif; ?>
                    </ul>
            </nav>
        </div>
    </header>
                        <!--  hay que hacer qeu carge los libros desde la base de datos e igualmente hacer que se vea solo los libros
                           por el id que se llevo desde categorias aparte de eso agregar la paginacion aqui e igualmente en categoriasya
                           ya que si en un futuro uno quiere agregar una nueva categoria e seguire usando el fromato de la categoria ya que
                           es la que mas espacio usa y la que mas puedo modificar   -->
    <section>
        <div class="container">
            <main class="main-content" id="librosContainer">
           
            </main>
            <!--Formulario-->
            <div id="fondoModal" style="display:none">
                <div id="formularioLibro" class="formulario-oculto">
                    <h3>Ingresar nuevo Libro</h3>
                    <form id="crearLibrosForm" enctype="multipart/form-data">
                        <input type="text" id="titulo" name="titulo" placeholder="Título" required><br>
                        <textarea name="descripcion" id="descripcion" placeholder="Descripción" required></textarea><br>
                        <input type="number" id="unidades" name="unidades" placeholder="Cantidad" required><br><br>
                        <select id="categoriatipos" name="categoria" required>
                            <option value="">Seleccione una categoria</option>
                            <option value="Química">Quimica</option>
                            <option value="Sistemas">Sistemas</option>
                            <option value="Lógica">Lógica</option>
                            <option value="Matemática">Matemática</option>
                            <option value="Estadística">Estadística</option>
                        </select>
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
     <script src="../../../js/script.js"></script>
    <script src="../../../js/book.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
<?php include('../../../resourse/include/footer.php')?>