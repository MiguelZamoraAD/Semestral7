<?php 
session_start();
//var_dump($_SESSION['Tipo']); 
include('../resourse/include/header.php')
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Módulo de Noticias</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../resourse/css/princ.css" />

</head>
<body>
    
    <section>
        <div class="container">
            <h2 class="h2-company">Menu</h2>
            <div class="services-grid">
                <?php if (isset($_SESSION['Tipo']) && strtolower($_SESSION['Tipo']) === 'adm'): ?>
                <div class="service-item">
                    <a href="page1/user.php">
                        <img src="../resourse/img/usuario.png" alt="Icono Trans" class="modulo-icon">
                        <h3>Usuarios</h3>
                    </a>
                </div>
                <?php endif; ?>
                <div class="service-item">
                    <a href="page1/estudiante.php">
                        <img src="../resourse/img/student.png" alt="Icono Trans" class="modulo-icon">
                        <h3>Estudiante</h3>
                    </a>
                </div>
                <div class="service-item">
                    <a href="page1/CategoryBook.php">
                    <img src="../resourse/img/categoria.png" alt="Icono Noti" class="modulo-icon">
                    <h3>Categoria de Libros</h3>
                    </a>
    
                </div>
                <?php if (isset($_SESSION['Tipo']) && strtolower($_SESSION['Tipo']) === 'adm'): ?>
                <div class="service-item">
                    <a href="page1/reserva.php">
                    <img src="../resourse/img/reservar.png" alt="Icono Sali" class="modulo-icon">
                    <h3>Reservacion</h3>
                    </a>

                </div>
                 <?php endif; ?>
                <div class="service-item">
                    <a href="../func/salir.php"><img src="../resourse/img/salir.png" alt="Icono Sali" class="modulo-icon">
                    <h3>Salir</h3>
                    </a>

                </div>
            </div>
        </div>
    </section>
    <script src="../js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
<?php include('../resourse/include/footer.php')?>