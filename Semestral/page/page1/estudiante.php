<?php 
session_start();
require_once '../../class/conexion.php';
$db = new Conexion();
$conn = $db->getConexion();
//var_dump($_SESSION['correo']); 

$correo = $_SESSION['correo'] ?? null;
$datosEstudiante = null;
$datosUsuario = null;
$datosreserva = null;

if ($correo) {
    // Consulta los datos del estudiante
    $stmt = $conn->prepare("SELECT * FROM estudiantes WHERE Usuario = ?");
    $stmt->execute([$correo]);
    $datosEstudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consulta los datos del usuario
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Usuario = ?");
    $stmt->execute([$correo]);
    $datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
    // Consulta los datos del reservas
    $stmt = $conn->prepare("
        SELECT r.id, r.libro_id, l.titulo AS titulo_libro, r.fecha_reserva, r.dias_reservado, r.estado
        FROM reservas r
        JOIN libros l ON r.libro_id = l.id
        WHERE r.usuario = ? AND r.estado = 'reservado'
    ");
    $stmt->execute([$correo]);
    $datosreserva = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Consulta los datos de vista_estadisticas_libros
    $stmt = $conn->prepare("SELECT * FROM vista_estadisticas_libros");
    $datosestadistica = $stmt->fetch(PDO::FETCH_ASSOC);

    // Contar reservas por estado para estados
    $stmt = $conn->prepare("
        SELECT estado, COUNT(*) as cantidad 
        FROM reservas 
        WHERE usuario = ? 
        GROUP BY estado
    ");
    $stmt->execute([$correo]);
    $conteos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // ['reservado' => 5, 'devuelto' => 3, ...]

} else {
    die("Sesión inválida o no iniciada.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido al Sitio de Estudiante</title>
    <link rel="stylesheet" href="../../resourse/css/estudiante.css">
    <link rel="stylesheet" href="../../resourse/css/princ.css">
    </head>
<body>

    <div class="page-container">
        <!--<header class="main-header">
            <div class="header-left">
                <span class="logo">Biblioteca Virtual</span>
            </div>
            <div class="header-right">
                Algo puedo hacer aqui pero por el momento no
            </div>
        </header>-->

        <div class="main-layout">
            <aside class="sidebar">
                <div class="user-profile-sidebar">
                    <h3>Biblioteca Virtual</h3>
                </div>
                <nav class="main-navigation">
                    <ul>
                        <li><a href="../company_info.php" class="nav-item active"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="CategoryBook.php" class="nav-item"><i class="fas fa-search"></i> Categorias de Libros</a></li>
                        <li><a href="#" class="nav-item"><i class="fas fa-history"></i> Reservacion</a></li>
                        <li><a href="../../func/salir.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
                        <div class="accordion-item">
                        <button class="accordion-header">
                            <span><i class="fas fa-money-bill-wave"></i> Gastos Pendientes</span>
                            <span class="toggle-icon"><i class="fas fa-plus"></i></span>
                        </button>
                        <div class="accordion-content">
                            <p>No hay gastos pendientes.</p>
                        </div>
                        <div class="accordion-item">
                    </div>
                    </div>
                    </ul>
                </nav>
            </aside>

            <main class="content-area">

                <section class="student-info-section">
                    <div class="info-card-left">
                        <div class="student-photo-display">
                            <p class="student-name-main"><?php echo htmlspecialchars(($datosEstudiante['Apellido'] ?? '') . ' ' . ($datosEstudiante['Nombre'] ?? '')); ?></p>
                            <p class="student-id"><?php echo htmlspecialchars($datosEstudiante['Cedula']); ?></p>
                        </div>
                        <div class="contact-info">
                            <h4>Información de Contacto:</h4>
                            <p><strong>Email:</strong> <a href="#"><?php echo htmlspecialchars($correo); ?></a></p>
                            <p><strong>Carrera: </strong><?php echo htmlspecialchars($datosEstudiante['Carrera']); ?></p>
                            <p><strong>Contraseña:
                            </strong><button id="btnContraseña" data-id="<?php echo $datosUsuario['id'];?>">cambiar contraseña</button></p>
                        </div>
                    </div>
                    <div class="info-card-right">
                        <h2>Libros Reservados</h2>
                        <?php if (!empty($datosreserva)): ?>
                            <table class="tabla-reservas">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Libro</th>
                                        <th>Fecha Reserva</th>
                                        <th>Días</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datosreserva as $reserva): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reserva['libro_id']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['titulo_libro']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['fecha_reserva']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['dias_reservado']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['estado']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No tienes libros reservados actualmente.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="service-hours-section">
                    <div class="hour-card">
                        <div class="icon-placeholder"><i class="fas fa-users"></i></div>
                        <div class="hours-value"><?php echo $conteos['devuelto'] ?? 0; ?></div>
                        <div class="hours-label">Libros devueltos</div>
                    </div>
                    <div class="hour-card">
                        <div class="icon-placeholder"><i class="fas fa-hands-helping"></i></div>
                        <div class="hours-value"><?php echo $conteos['retardo'] ?? 0; ?></div>
                        <div class="hours-label">Libros con Retardo</div>
                    </div>
                    <div class="hour-card total">
                        <div class="icon-placeholder"><i class="fas fa-clock"></i></div>
                        <div class="hours-value"><?php echo $conteos['reservado'] ?? 0; ?></div>
                        <div class="hours-label">Total de libros Reservados</div>
                    </div>
                </section>

                <section class="accordion-sections">
                    <!--Aqui tambien puedo colocar otra cosa pero nop-->
                </section>
            </main>
        </div>
            <section>
                <div class="containerForm">
                    <div id="fondoModal" style="display:none">
                        <div id="formularioCategoria" class="formulario-oculto">
                            <h3>Cambiar contraseña</h3>
                            <form id="contraseñaForm" enctype="multipart/form-data">
                                    <div class="form-group"><input type="password" id="pass1" name="hashMagic" placeholder="Contraseña" required></div>
                                    <div class="form-group"><input type="password" id="pass2" name="confirmPassword" placeholder="Confirmar contraseña" required></div>
                                    <div class="form-group full-width">
                                    <button type="button" id="btnRegistrar">Registrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
    </div>

    <script href="../../JS/estudiante.js"></script>
    <script>
        document.querySelectorAll('.accordion-header').forEach(header => {
            header.addEventListener('click', () => {
                const item = header.parentElement;
                const content = item.querySelector('.accordion-content');
                const icon = header.querySelector('.toggle-icon i');

                item.classList.toggle('active');
                if (item.classList.contains('active')) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');
                } else {
                    content.style.maxHeight = '0';
                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');
                }
            });
        });
    </script>
</body>
</html>
<?php include("../../resourse/include/footer.php")?>