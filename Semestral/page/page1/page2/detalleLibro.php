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
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($noticia['titulo']); ?></title>
    <link rel="stylesheet" href="../resourse/css/noti.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($noticia['titulo']); ?></h1>
        <p class="post-meta">Publicado el <?php echo $noticia['fecha']; ?></p>
    </header>

    <main class="main-content">
        <div class="post-content">
            <div class="adverbs-image">
                <img src="<?php echo $noticia['ruta_imagen']; ?>" alt="<?php echo $noticia['titulo']; ?>" />
            </div>
            <div class="news-items">
                <p><?php echo nl2br($noticia['contenido']); ?></p>
            </div>
        </div>
        <br>
        <a href="moduloN.php">⬅ Volver a las noticias</a>
    </main>
</body>
</html>
