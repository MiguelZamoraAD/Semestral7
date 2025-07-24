<?php
require_once "../clases/noticias.php";

$id = intval($_GET['id'] ?? 0);
$noticia = Noticia::buscarPorId($id); // Necesitas este método en la clase

if (!$noticia) {
    echo "<h2>Noticia no encontrada</h2>";
    exit;
}
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
