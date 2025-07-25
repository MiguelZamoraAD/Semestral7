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

    $tipoUsuario = strtolower($datosUsuario['Tipo'] ?? '');

    // Consulta los datos del reservas
    $stmt = $conn->prepare("
        SELECT r.id, r.libro_id, l.titulo AS titulo_libro, r.fecha_reserva, r.dias_reservado, r.estado
        FROM reservas r
        JOIN libros l ON r.libro_id = l.id
        WHERE r.usuario = ? AND r.estado = 'reservado'
    ");
    $stmt->execute([$correo]);
    $datosreserva = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta todas las reservas con información del libro y tipo de usuario
            if ($tipoUsuario === 'adm'){
            // Admin ve todo
            $stmt = $conn->prepare("
                SELECT r.fecha_reserva, l.titulo AS libro, r.dias_reservado, u.tipo AS tipo_usuario, r.usuario
                FROM reservas r
                JOIN libros l ON r.libro_id = l.id
                JOIN usuarios u ON r.usuario = u.Usuario
                ORDER BY r.fecha_reserva DESC
            ");
            $stmt->execute();
        } else {
            // Solo ve sus propias reservas
            $stmt = $conn->prepare("
                SELECT r.fecha_reserva, l.titulo AS libro, r.dias_reservado, u.tipo AS tipo_usuario, r.usuario
                FROM reservas r
                JOIN libros l ON r.libro_id = l.id
                JOIN usuarios u ON r.usuario = u.Usuario
                WHERE r.usuario = ?
                ORDER BY r.fecha_reserva DESC
            ");
            $stmt->execute([$correo]);
        }
        $reservasGlobales = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <script href="../../JS/estudiante.js"></script>
    <link rel="stylesheet" href="../../resourse/css/estudiante.css">
    <link rel="stylesheet" href="../../resourse/css/princ.css">
    </head>
<body>

    <div class="page-container">
        <header class="header">
                <div class="container">
                    <h1 class="logo">Categorias de Libros</h1>
                    <nav class="nav-menu">
                            <button class="user-menu-toggle" aria-label="Abrir menú de navegación">&#9776;</button>
                            <ul class="nav-list">
                                <li ><a href="../company_info.php" >Inicio</a></li>
                                <?php if (isset($_SESSION['Tipo']) && strtolower($_SESSION['Tipo']) === 'adm'): ?>
                                <li ><a href="user.php" >Usuario</a></li>
                                <?php endif; ?>
                                <li ><a href="estudiante.php" >Estudiante</a></li>
                                <li ><a href="CategoryBook.php" >Categorias</a></li>
                            </ul>
                    </nav>
                </div>
            </header>

        <div class="main-layout">

            <main class="content-area">
                <section class="student-info-section">
                    <div class="info-card-right">
                        <div class="student-photo-display">
                            <h2>Historial de Reservas Global</h2>
                            <?php if (!empty($reservasGlobales)): ?>
                                <table class="tabla-reservas-global" id="tabla-global">
                                    <thead>
                                        <tr>
                                            <th>Fecha Reserva</th>
                                            <th>Libro</th>
                                            <th>Días Reservados</th>
                                            <th>Tipo Usuario</th>
                                            <th>Usuario</th>
                                        </tr>
                                    </thead>
                                    <div id="registro">
                                    <tbody>
                                        <?php foreach ($reservasGlobales as $res): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($res['fecha_reserva']); ?></td>
                                                <td><?php echo htmlspecialchars($res['libro']); ?></td>
                                                <td><?php echo htmlspecialchars($res['dias_reservado']); ?></td>
                                                <td>
                                                    <?php
                                                        $tipo = strtolower($res['tipo_usuario']);
                                                        echo match($tipo) {
                                                            'adm' => 'Admin',
                                                            'user' => 'Usuario',
                                                            'prof' => 'Profesor',
                                                            default => ucfirst($tipo)
                                                        };
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($res['usuario']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    </div>
                                </table>
                                <button onclick="exportarExcel('tabla-global')" class="btn-export">Exportar a Excel</button>
                            <?php else: ?>
                                <p>No hay reservas registradas aún.</p>
                            <?php endif; ?>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <script>
        function exportarExcel(idTabla) {
            const tabla = document.getElementById(idTabla);
            let html = tabla.outerHTML.replace(/ /g, '%20');

            const nombreArchivo = 'reservas_globales.xls';
            const tipo = 'application/vnd.ms-excel';

            let enlace = document.createElement('a');
            enlace.href = 'data:' + tipo + ', ' + html;
            enlace.download = nombreArchivo;
            enlace.click();
        }
    </script>
</body>
</html>
<?php include("../../resourse/include/footer.php")?>