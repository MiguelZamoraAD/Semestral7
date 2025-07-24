<?php
// procesar_registro.php
require '../class/Usuario.php';
require_once '../class/Conexion.php';

$conexion = new Conexion(); // o como llames tu clase de conexión
$pdo = $conexion->getConexion(); // asegurarte que esta devuelve el PDO

//$gestor = new GestorRegistro($pdo);
try {
    $datos = $_POST['correo']??'';
     $correo = $datos;  // <== Este array debe tener "correo"
    // Verificación básica de existencia de campos requeridos
    if (
        !isset($_POST['nombre'], $_POST['apellido'], $_POST['correo'], $_POST['hashMagic'], $_POST['confirmPassword'], $_POST['sexo'])
    ) {
        throw new Exception("Faltan campos obligatorios.");
    }

        $usuarioLogin = new UsuarioLogin();
        $resultado = $usuarioLogin->registrar($_POST);
        //$gestor = new GestorRegistro($pdo);
        //$gestor->registrarUsuario($_POST);

    
    // Redirigir si todo salió bien
       exit;

} catch (Exception $e) {
    echo "<p style='color:black; font-family:sans-serif;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
