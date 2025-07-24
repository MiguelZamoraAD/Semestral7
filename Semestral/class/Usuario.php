<?php
require_once __DIR__ . '/../class/Sanitiza.php';

class UsuarioLogin {
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
    }

    public  function registrar($datos) {
        try {
            //sanatizar
            $nombre = Sanitizador::limpiarNombre($datos['nombre']);
            $apellido = Sanitizador::limpiarApellido($datos['apellido']);
            $correo = Sanitizador::limpiarCorreo($datos['correo']);
            $sexo = Sanitizador::validarSexo($datos['sexo']);
            $pass1 = $datos['hashMagic'];
            $pass2 = $datos['confirmPassword'];

            // Validaciones básicas
            if (!$nombre || !$apellido || !$correo || !$sexo || !$pass1 || !$pass2) {
                return ['error' => 'Faltan campos obligatorios.'];
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                return ['error' => 'Correo no válido.'];
            }

            if ($pass1 !== $pass2) {
                return ['error' => 'Las contraseñas no coinciden.'];
            }

            // Verificar que el usuario no exista
            $sql = "SELECT id FROM usuarios WHERE Usuario = :Correo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":Correo", $correo);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return "El usuario ya existe.";
            }
            $password_hash = password_hash($pass1, PASSWORD_DEFAULT);

            // Registrar usuario
             $insert = $this->conexion->prepare("
            INSERT INTO usuarios (Nombre, Apellido, Usuario, Tipo, sexo, HashMagic)
                VALUES (:Nombre, :Apellido, :Correo, :Tipo, :Sexo, :HashMagic)");
                    $insert->bindParam(":Nombre", $nombre);
                    $insert->bindParam(":Apellido", $apellido);
                    $insert->bindParam(":Correo", $correo);
                    $insert->bindParam(":Tipo", $tipo);
                    $insert->bindParam(":Sexo", $sexo);
                    $insert->bindParam(":HashMagic", $password_hash);
                    $insert->execute();
            return ['exito' => true, 'correo' => $correo];
        } catch (PDOException $e) {
             die("Error al insertar: " . $e->getMessage());
            }
    }

    public function verificarCredenciales($correo, $contrasena) {
        try {
            $sql = "SELECT * FROM usuarios WHERE Usuario = :Correo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":Correo", $correo);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado && password_verify($contrasena, $resultado['HashMagic'])) {
                return $resultado;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
