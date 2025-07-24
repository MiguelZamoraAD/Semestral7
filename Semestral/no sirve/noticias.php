<?php
require_once "conexion.php";

class Noticia {
    private $titulo;
    private $contenido;
    private $imagen;
    private $miniatura;
    private $db;

    public function __construct($data, $imagen = '', $miniatura = '') {
        $this->titulo = trim($data['titulo'] ?? '');
        $this->contenido = trim($data['contenido'] ?? '');
        $this->imagen = $imagen;
        $this->miniatura = $miniatura;

        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    private function validar() {
        if (empty($this->titulo) || empty($this->contenido)) {
            throw new Exception("Título y contenido no pueden estar vacíos.");
        }
    }

    public function guardar() {
        $this->validar();
        $sql = "INSERT INTO noticias (titulo, contenido, ruta_imagen, ruta_miniatura)
                VALUES (:titulo, :contenido, :imagen, :miniatura)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo' => $this->titulo,
            ':contenido' => $this->contenido,
            ':imagen' => $this->imagen,
            ':miniatura' => $this->miniatura
        ]);
    }

    public function editar($id) {
        $this->validar();
        $sql = "UPDATE noticias SET titulo=:titulo, contenido=:contenido WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo' => $this->titulo,
            ':contenido' => $this->contenido,
            ':id' => $id
        ]);
    }

    public static function eliminar($id) {
        $conexion = new Conexion();
        $db = $conexion->getConexion();
        $stmt = $db->prepare("DELETE FROM noticias WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function listarTodos() {
        $conexion = new Conexion();
        $db = $conexion->getConexion();
        $stmt = $db->prepare("SELECT * FROM noticias ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id) {
    $conexion= new Conexion();
    $conn = $conexion->getConexion();
    $stmt = $conn->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}
?>
