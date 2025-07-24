<?php
// clases/GestorRegistro.php
require_once 'sanitiza.php';
require_once 'Usuario.php';

class GestorRegistro {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarUsuario($data) {
    // Sanitizar
    $nombre = Sanitizador::limpiarNombre($data['nombre'] ?? '');
    $apellido = Sanitizador::limpiarApellido($data['apellido'] ?? '');
    $correo = Sanitizador::limpiarCorreo($data['correo'] ?? '');
    $sexo = Sanitizador::validarSexo($data['sexo'] ?? '');

    $pass1 = $data['hashMagic'] ?? '';
    $pass2 = $data['confirmPassword'] ?? '';

    if (empty($correo)) {
        return ['error' => 'Correo no proporcionado o vacío'];
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Correo inválido.");
    }


    if (!$sexo) {
        throw new Exception("Sexo inválido.");
    }

    if ($pass1 !== $pass2) {
        throw new Exception("Las contraseñas no coinciden.");
    }

    // Verificar que no exista el correo (mejor hacer consulta directa)
    $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE Usuario = ?");
    $stmt->execute([$correo]);
    if ($stmt->rowCount() > 0) {
        throw new Exception("El usuario ya existe.");
    }

     // Hashear contraseña
    $hashMagic = password_hash($pass1, PASSWORD_DEFAULT);

    // Insertar usuario
    $stmt = $this->pdo->prepare("INSERT INTO usuarios (Nombre, Apellido, Usuario, HashMagic, sexo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $correo, $hashMagic, $sexo]);

    return true;
    
}

}
