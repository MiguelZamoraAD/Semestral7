<?php include('resourse/include/header.php')?>
<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    require_once 'class/conexion.php';
    require_once 'class/Usuario.php';
   
    $conexion = new Conexion();
    $pdo = $conexion->getConexion();

    $ip = $_SERVER['REMOTE_ADDR'];
    $deteccion = 0;

    // 1. 🔍 Verificar intentos fallidos desde esta IP en los últimos 10 minutos
    $stmt = $pdo->prepare("
         SELECT COUNT(*) AS intentos 
            FROM intentos_login 
         WHERE ipRemoto = ? 
            AND timestamp > (NOW() - INTERVAL 10 MINUTE)
        ");
        $stmt->execute([$ip]);
        $resultadoIntentos = $stmt->fetch(PDO::FETCH_ASSOC);
        $intentos = $resultadoIntentos['intentos'] ?? 0;


    // 2. 🛑 Bloquear si hay 5 o más intentos recientes
    if ($intentos >= 5) {
        echo "<p style='color:red;'>⚠️ Demasiados intentos fallidos. Inténtalo de nuevo en unos minutos.</p>";
        exit;
    }

    // 3. Validar credenciales
    $login = new UsuarioLogin();
    $resultado = $login->verificarCredenciales($correo, $contrasena);

    if ($resultado) {
        $_SESSION['correo'] = $resultado['Usuario'];
        $_SESSION['autenticado'] = "SI";
        $_SESSION['Tipo'] = $resultado['Tipo'];
        $_SESSION['id'] = $resultado['id'];
        header("Location: page/company_info.php");
        exit;
    } else {
        echo "<p style='color:red;'>❌ Usuario o contraseña incorrectos.</p>";
    }
}


?>
<body>
    <link rel="stylesheet" href="resourse/css/index.css">
    <div class="login-container">
        <form method="post" class="login-form">
            <h2>Iniciar sesión</h2>
            Correo: <input type="text" name="correo" required><br>
            Contraseña: <input type="password" name="contrasena" required><br>
            <button type="submit">Entrar</button><br>
        </form>
    </div>
</body>


<?php include('resourse/include/footer.php')?>