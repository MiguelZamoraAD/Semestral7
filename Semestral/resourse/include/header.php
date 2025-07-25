
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Que ves aqui no hay nada</title>
      <link rel="stylesheet" href="resourse/css/princ.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
</head>
<body>
<header class="header">
        <div class="container">
            <h1 class="logo">Biblioteca Virtual</h1>
            <?php if (isset($_SESSION['correo'])): ?>
            <nav class="nav-menu">
                <button class="user-menu-toggle" aria-label="Abrir menú de navegación">&#9776; </button>
                <ul class="nav-list">
                    <?php if (isset($_SESSION['Tipo']) && strtolower($_SESSION['Tipo']) === 'adm'): ?>
                    <li ><a href="page1/user.php" >Usuario</a></li>
                    <?php endif; ?><li><a href="#about">Estudiante</a></li>
                    <li><a href="page1/categoryBook.php">Categoria</a></li>
                    <li><a href="#about">Reservas</a></li>
                    <li><a href="../func/salir.php">cerrar secion</a></li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </header>
