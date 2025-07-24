<?php
require_once (__DIR__ . '/../conexion.php');
require_once __DIR__ . '/../sanitiza.php';

class Book{
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function guardar($datos) {
        $conn = $this->conexion;

    try {
            $sql = "INSERT INTO libros 
                    (titulo, descripcion, ruta_imagen, ruta_miniatura, unidades, categoria, fecha_creacion) 
                    VALUES 
                    (:nombre, :descripcion, :ruta_imagen, :ruta_miniatura, :unidades, :categoria, :fecha)";

            $stmt = $conn->prepare($sql);
            $resultado=$stmt->execute([
                ':nombre' => $datos['titulo'],
                ':descripcion' => $datos['descripcion'],
                ':ruta_imagen' => $datos['ruta_imagen'],
                ':ruta_miniatura' => $datos['ruta_miniatura'],
                ':unidades' => $datos['unidades'],
                ':categoria' => $datos['categoria'],
                ':fecha' => date('Y-m-d H:i:s')
            ]);

        if (!$resultado) {
            error_log("❌ Error al ejecutar SQL: " . implode(", ", $stmt->errorInfo()));
        }

            return true;
        } catch (PDOException $e) {
            error_log("Error al guardar categoría: " . $e->getMessage());
            return false;
        }
    }

    public function editar($id, $datos) {
        $conn = $this->conexion;

     try {
            $sql = "UPDATE libros SET 
                        titulo = :nombre, 
                        descripcion = :descripcion, 
                        ruta_imagen = :ruta_imagen, 
                        ruta_miniatura = :ruta_miniatura,
                        unidades = :unidades,
                        categoria = :categoria 
                    WHERE id = :id";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['titulo'],
                ':descripcion' => $datos['descripcion'],
                ':ruta_imagen' => $datos['ruta_imagen'],
                ':ruta_miniatura' => $datos['ruta_miniatura'],
                ':unidades' => $datos['unidades'],
                ':categoria' => $datos['categoria'],
                ':id' => $id
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Error al editar categoría: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT * FROM libros WHERE id = ?"
            );
            $stmt->execute([$id]);
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($categoria) {
                return [
                    'success' => true,
                    'categoria' => $categoria
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Libro no encontrado.'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al obtener el Libro: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener datos del Libro.'
            ];
        }

    }
    
    public function eliminar($id) {
        $conn = $this->conexion;

     try {
            $stmt = $conn->prepare("DELETE FROM libros WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log("Error al eliminar el libro: " . $e->getMessage());
            return false;
        }
    }


    public function contarLibros( $categoria, $busqueda = '') {
        $conn = $this->conexion;
        $sql = "SELECT COUNT(*) FROM libros WHERE 1=1";
        $params = [];

        if ($busqueda !== '') {
            $sql .= " AND (categoria LIKE :busqueda OR descripcion LIKE :busqueda)";
            $params[':busqueda'] = "%$busqueda%";
        }

        if ($categoria !== null && $categoria !== '') {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function contar() {
        $conn = $this->conexion;
        if (!empty($categoria)) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM libros WHERE categoria = :categoria");
            $stmt->execute([':categoria' => $categoria]);
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM libros");
            $stmt->execute();
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }

    public function listarTodos($limite = 3, $offset = 0, $busqueda = '', $categoria = null) {
        $conn = $this->conexion;

            $sql = "SELECT * FROM libros WHERE 1=1";
            $params = [];

            if ($busqueda !== '') {
            $sql .= " AND (categoria LIKE :busqueda OR descripcion LIKE :busqueda)";
            $params[':busqueda'] = "%$busqueda%";
        }

        if ($categoria !== null && $categoria !== '') {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        $sql .= " ORDER BY id ASC LIMIT :limite OFFSET :offset";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function procesarImagenCategoria($archivo) {
        $directorioOriginal = __DIR__ . '/../../uploads/bookUp/';
        $directorioMiniatura = __DIR__ . '/../../thumbnails/bookTh/';

        if (!file_exists($directorioOriginal)) {
            mkdir($directorioOriginal, 0777, true);
        }

        if (!file_exists($directorioMiniatura)) {
            mkdir($directorioMiniatura, 0777, true);
        }

        // Verifica si se subió correctamente
        if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Error al subir la imagen.'];
        }

        $nombreArchivo = time() . '_' . basename($archivo['name']);
        $rutaDestino = $directorioOriginal . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return ['error' => 'No se pudo mover la imagen al directorio de subida.'];
        }

        // Crear miniatura
        $rutaMini = $directorioMiniatura . $nombreArchivo;
        $exitoMiniatura = $this->crearMiniatura($rutaDestino, $rutaMini, 160, 125);

        if (!$exitoMiniatura) {
            return ['error' => 'No se pudo crear la miniatura.'];
        }

        // Retornar rutas relativas
        return [
            'ruta_imagen' => "../../../uploads/bookUp/" . $nombreArchivo,
            'ruta_miniatura' => "../../../thumbnails/bookTh/" . $nombreArchivo
        ];
    }

    private function crearMiniatura($origen, $destino, $anchoMax, $altoMax) {
        $info = getimagesize($origen);
        if (!$info) {
            error_log("⚠️ No se pudo obtener info de imagen: $origen");
            return false;
        }

        list($anchoOriginal, $altoOriginal) = $info;
        $tipo = $info[2];

        switch ($tipo) {
            case IMAGETYPE_JPEG:
                error_log("📷 Tipo: JPEG");
                $imagen = imagecreatefromjpeg($origen);
                break;
            case IMAGETYPE_PNG:
                error_log("📷 Tipo: PNG");
                $imagen = imagecreatefrompng($origen);
                break;
            case IMAGETYPE_GIF:
                error_log("📷 Tipo: GIF");
                $imagen = imagecreatefromgif($origen);
                break;
            default:
                error_log("❌ Tipo de imagen no soportado: $tipo");
                return false;
        }

        $miniatura = imagecreatetruecolor($anchoMax, $altoMax);
        imagecopyresampled($miniatura, $imagen, 0, 0, 0, 0, $anchoMax, $altoMax, $anchoOriginal, $altoOriginal);

        switch ($tipo) {
            case IMAGETYPE_JPEG:
                imagejpeg($miniatura, $destino);
                break;
            case IMAGETYPE_PNG:
                imagepng($miniatura, $destino);
                break;
            case IMAGETYPE_GIF:
                imagegif($miniatura, $destino);
                break;
        }

        imagedestroy($imagen);
        imagedestroy($miniatura);
        return true;
    }

}
?>