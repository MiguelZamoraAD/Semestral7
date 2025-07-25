<?php
session_start();
require_once '../../../class/conexion.php';
$db = new Conexion();
$conn = $db->getConexion();
//var_dump($_SESSION['correo']); 

$libroId = $_GET['data-id'] ?? null;
$libro = null;

if ($libroId) {
    try {
        $stmt = $conn->prepare("SELECT * FROM libros WHERE id = ?");
        $stmt->execute([$libroId]);
        $libro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$libro) {
            die("Libro no encontrado.");
        }
    } catch (PDOException $e) {
        die("Error al obtener el libro: " . $e->getMessage());
    }
} else {
    die("ID de libro no especificado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title><?php echo htmlspecialchars($libro['titulo'] ?? 'Título no disponible'); ?></title>
    <link rel="stylesheet" href="../../../resourse/css/noti.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="logo"><?php echo htmlspecialchars($libro['titulo'] ?? 'Química'); ?></h1>
            <nav class="nav-menu">
                    <button class="menu-toggle" aria-label="Abrir menú de navegación">&#9776;</button>
                    <ul class="nav-list">
                        <li ><a href="../../company_info.php" >Inicio</a></li>
                        <?php if (isset($_SESSION['Tipo']) && strtolower($_SESSION['Tipo']) === 'adm'): ?>
                        <li ><a href="../../user.php" >Usuario</a></li>
                        <?php endif; ?> 
                        <li ><a href="../studen.php" >Estudiante</a></li>
                        <li ><a href="../CategoryBook.php" >Categorias</a></li>
                    </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="post-details-section"> <h2 class="section-title">Descripción del Libro</h2> <div class="post-content">
                <div class="adverbs-image">
                    <img src="<?php echo htmlspecialchars($libro['ruta_imagen'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($libro['titulo'] ?? 'Libro'); ?>" /><br>
                </div>
                <div class="news-items">
                    <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($libro['descripcion'] ?? 'No disponible')); ?></p>
                    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($libro['categoria'] ?? 'No disponible'); ?></p>
                    <p><strong>Disponibilidad:</strong> <?php echo htmlspecialchars($libro['unidades'] ?? 'No disponible'); ?> copias disponibles</p> </div>
            </div>
            <a href="../CategoryBook.php" class="back-link">⬅ Volver</a>
        </div>

        <div class="registration-sidebar">
            <h3>¡Reserva este libro ya!</h3>
            <p>Ingresa los dias para obtener una copia de este libro.</p>
            <form action="process_registration.php" method="POST"> 
                <div class="form-group">
                    <label for="diaR">Dias a reservar</label>
                    <input type="number" id="diaR" name="diaR" min="1" max="31" required>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">Confirmar que ha leído y acepta los <a href="#" style="color: #3498db;">Términos y Condiciones</a></label>
                </div>
                <button type="submit" class="submit-button">Reservar</button><br>
                <button class="devolver-button" id="devolverBtn">Devolver libro</button>
            </form>
        </div>
    </main>
    <script src="../../../JS/libro.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>