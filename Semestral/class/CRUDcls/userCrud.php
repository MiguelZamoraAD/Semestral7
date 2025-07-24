<?php
require_once (__DIR__ . '/../conexion.php');
require_once __DIR__ . '/../sanitiza.php';

class User{
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function guardar($datos) {
    $conn = $this->conexion;

    try {
        $conn->beginTransaction();

        // Insertar en tabla usuarios
        $sql1 = "INSERT INTO usuarios 
                (Nombre, Apellido, Usuario, Tipo, Sexo, HashMagic) 
                VALUES 
                (:nombre, :apellido, :usuario, :tipo, :sexo, :hashMagic)";

        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([
            ':nombre' => $datos['Nombre'],
            ':apellido' => $datos['Apellido'],
            ':usuario' => $datos['Usuario'],
            ':tipo' => $datos['Tipo'],
            ':sexo' => $datos['Sexo'],
            ':hashMagic' => password_hash($datos['Password'], PASSWORD_DEFAULT)
        ]);

        // Insertar en tabla estudiante
        $sql2 = "INSERT INTO estudiantes 
                (Cedula, Nombre, SegundoN, Apellido, SegundoA, FechaNacimiento, Carrera, Usuario) 
                VALUES 
                (:cedula, :nombre, :segundon, :apellido, :segundoa, :nacimiento, :carrera, :usuario)";

        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([
            ':cedula' => $datos['Cedula'],
            ':nombre' => $datos['Nombre'],
            ':segundon' => $datos['SegundoN'],
            ':apellido' => $datos['Apellido'],
            ':segundoa' => $datos['SegundoA'],
            ':nacimiento' => $datos['FechaNacimiento'],
            ':carrera' => $datos['Carrera'],
            ':usuario' => $datos['Usuario']
        ]);

        $conn->commit();
        return true;

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error al guardar usuario-estudiante: " . $e->getMessage());
        return false;
    }
}

public function editar($id, $datos) {
    $conn = $this->conexion;

    try {
        $conn->beginTransaction();

        // Actualizar en tabla usuarios
        $sql1 = "UPDATE usuarios SET 
                    Nombre = :nombre,
                    Apellido = :apellido,
                    Usuario = :usuario,
                    Tipo = :tipo,
                    Sexo = :sexo
                WHERE id = :id";

        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([
            ':nombre' => $datos['Nombre'],
            ':apellido' => $datos['Apellido'],
            ':usuario' => $datos['Usuario'],
            ':tipo' => $datos['Tipo'],
            ':sexo' => $datos['Sexo'],
            ':id' => $id
        ]);

        // Actualizar en tabla estudiante
        $sql2 = "UPDATE estudiantes SET 
                    Nombre = :nombre,
                    SegundoN = :segundon,
                    Apellido = :apellido,
                    SegundoA = :segundoa,
                    FechaNacimiento = :nacimiento,
                    Carrera = :carrera,
                    Usuario = :usuario
                WHERE Cedula = :cedula";

        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([
            ':cedula' => $datos['Cedula'],
            ':nombre' => $datos['Nombre'],
            ':segundon' => $datos['SegundoN'],
            ':apellido' => $datos['Apellido'],
            ':segundoa' => $datos['SegundoA'],
            ':nacimiento' => $datos['FechaNacimiento'],
            ':carrera' => $datos['Carrera'],
            ':usuario' => $datos['Usuario']
        ]);

        $conn->commit();
        return true;

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error al editar usuario-estudiante: " . $e->getMessage());
        return false;
    }
}

public function obtenerPorId($id) {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT u.id, u.Nombre, u.Apellido, u.Usuario, u.Tipo, u.Sexo, 
                        e.Cedula, e.SegundoN, e.SegundoA, e.FechaNacimiento, e.Carrera 
                 FROM usuarios u 
                 JOIN estudiantes e ON u.Usuario = e.Usuario 
                 WHERE u.id = ?"
            );
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                return [
                    'success' => true,
                    'usuario' => $usuario
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado.'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener datos del usuario.'
            ];
        }
    }
    
    public function eliminar($id) {
    $conn = $this->conexion;

    // Primero obtenemos el correo (Usuario) para buscar en estudiante
    $stmtSelect = $conn->prepare("SELECT Usuario FROM usuarios WHERE id = :id");
    $stmtSelect->execute([':id' => $id]);
    $usuario = $stmtSelect->fetchColumn();

    if (!$usuario) {
        return false; // No se encontró el usuario
    }

    try {
        $conn->beginTransaction();

        // Eliminar de la tabla estudiante
        $stmtEstudiante = $conn->prepare("DELETE FROM estudiantes WHERE Usuario = :usuario");
        $stmtEstudiante->execute([':usuario' => $usuario]);

        // Eliminar de la tabla usuarios
        $stmtUsuario = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmtUsuario->execute([':id' => $id]);

        $conn->commit();
        return true;

    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Error al eliminar usuario y estudiante: " . $e->getMessage());
        return false;
    }
}


public function contarUsuarios($busqueda = '') {
    $conn = $this->conexion;
    if ($busqueda === '') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios");
        $stmt->execute();
    } else {
        $busqueda = "%$busqueda%";
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE Nombre LIKE :busqueda OR Apellido LIKE :busqueda OR Usuario LIKE :busqueda");
        $stmt->execute([':busqueda' => $busqueda]);
    }
    return (int) $stmt->fetchColumn();
}

public function listarTodos($limite = 3, $offset = 0, $busqueda = '') {
    $conn = $this->conexion;
    if ($busqueda === '') {
        $sql = "SELECT id, Nombre, Apellido, Usuario, Tipo, Sexo, HashMagic FROM usuarios ORDER BY id ASC LIMIT :limite OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $busqueda = "%$busqueda%";
        $sql = "SELECT id, Nombre, Apellido, Usuario, Tipo, Sexo, HashMagic 
                FROM usuarios 
                WHERE Nombre LIKE :busqueda OR Apellido LIKE :busqueda
                ORDER BY id ASC LIMIT :limite OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function verificarDuplicado($cedula, $correo, $id = null) {
    $conexion = $this->conexion;

    // Verificar si el correo ya está en uso en la tabla usuarios
    $sqlUsuario = "SELECT COUNT(*) FROM usuarios WHERE Usuario = ?";
    $paramCorreo = [$correo];
    if ($id !== null) {
        $sqlUsuario .= " AND id != ?";
        $paramCorreo[] = $id;
    }
    $stmtUsuario = $conexion->prepare($sqlUsuario);
    $stmtUsuario->execute($paramCorreo);
    $correoDuplicado = $stmtUsuario->fetchColumn() > 0;

    // Verificar si la cédula ya está en uso en la tabla estudiantes,
    // pero excluir al usuario actual comparando por el correo (Usuario)
    $sqlCedula = "SELECT COUNT(*) FROM estudiantes WHERE Cedula = ?";
    $paramCedula = [$cedula];

    if ($id !== null) {
        // Se busca obtener el correo actual del usuario con ese ID
        $sqlCorreo = "SELECT Usuario FROM usuarios WHERE id = ?";
        $stmtCorreo = $conexion->prepare($sqlCorreo);
        $stmtCorreo->execute([$id]);
        $correoActual = $stmtCorreo->fetchColumn();

        if ($correoActual) {
            $sqlCedula .= " AND Usuario != ?";
            $paramCedula[] = $correoActual;
        }
    }

    $stmtCedula = $conexion->prepare($sqlCedula);
    $stmtCedula->execute($paramCedula);
    $cedulaDuplicada = $stmtCedula->fetchColumn() > 0;

    return $correoDuplicado || $cedulaDuplicada;
}


}
?>