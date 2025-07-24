<?php
require_once (__DIR__ . '/../conexion.php');
require_once __DIR__ . '/../sanitiza.php';

class Category{
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function guardar($datos) {
        $conn = $this->conexion;

    try {
            $sql = "INSERT INTO categorias_libros 
                    (titulo, descripcion, ruta_imagen, ruta_miniatura, fecha_creacion) 
                    VALUES 
                    (:nombre, :descripcion, :ruta_imagen, :ruta_miniatura, :fecha)";

            $stmt = $conn->prepare($sql);
            $resultado=$stmt->execute([
                ':nombre' => $datos['titulo'],
                ':descripcion' => $datos['descripcion'],
                ':ruta_imagen' => $datos['ruta_imagen'],
                ':ruta_miniatura' => $datos['ruta_miniatura'],
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
            $sql = "UPDATE categorias_libros SET 
                        titulo = :nombre, 
                        descripcion = :descripcion, 
                        ruta_imagen = :ruta_imagen, 
                        ruta_miniatura = :ruta_miniatura 
                    WHERE id = :id";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['titulo'],
                ':descripcion' => $datos['descripcion'],
                ':ruta_imagen' => $datos['ruta_imagen'],
                ':ruta_miniatura' => $datos['ruta_miniatura'],
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
                "SELECT * FROM categorias_libros WHERE id = ?"
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
                    'message' => 'Categoría no encontrada.'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al obtener categoría: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener datos de la categoría.'
            ];
        }

    }
    
    public function eliminar($id) {
        $conn = $this->conexion;

     try {
            $stmt = $conn->prepare("DELETE FROM categorias_libros WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log("Error al eliminar categoría: " . $e->getMessage());
            return false;
        }
    }


    public function contarCategorias($busqueda = '') {
        $conn = $this->conexion;
    if ($busqueda === '') {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM categorias_libros");
            $stmt->execute();
        } else {
            $busqueda = "%$busqueda%";
            $stmt = $conn->prepare("SELECT COUNT(*) FROM categorias_libros WHERE nombre_categoria LIKE :busqueda OR descripcion LIKE :busqueda");
            $stmt->execute([':busqueda' => $busqueda]);
        }
        return (int) $stmt->fetchColumn();
    }

    public function contar() {
        $conn = $this->conexion;
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM categorias_libros");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function listarTodos($limite = 3, $offset = 0, $busqueda = '') {
        $conn = $this->conexion;
    if ($busqueda === '') {
            $sql = "SELECT * FROM categorias_libros ORDER BY id ASC LIMIT :limite OFFSET :offset";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $busqueda = "%$busqueda%";
            $sql = "SELECT * FROM categorias_libros 
                    WHERE nombre_categoria LIKE :busqueda OR descripcion LIKE :busqueda
                    ORDER BY id ASC LIMIT :limite OFFSET :offset";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':busqueda', $busqueda, PDO::PARAM_STR);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function procesarImagenCategoria($archivo) {
        $directorioOriginal = __DIR__ . '/../../uploads/categoryUp/';
        $directorioMiniatura = __DIR__ . '/../../thumbnails/categoryTh/';

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
            'ruta_imagen' => "../../uploads/categoryUp/" . $nombreArchivo,
            'ruta_miniatura' => "../../thumbnails/categoryTh/" . $nombreArchivo
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