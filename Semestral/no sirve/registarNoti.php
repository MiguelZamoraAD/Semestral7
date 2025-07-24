<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

require_once "../Class/noticias.php";

$response = [
    'success' => false,
    'message' => '',
    'accion' => '',
    'errors' => []
];

try {
    $accion = $_POST['Accion'] ?? '';

    switch ($accion) {
        case 'Guardar':
            file_put_contents("debug.txt", print_r($_FILES, true));
            if (!isset($_FILES['imagen'])) throw new Exception("No se recibió imagen.");

            $file = $_FILES['imagen'];
            $nombreOriginal = $file['name'];
            $temp = $file['tmp_name'];
            $tipo = $file['type'];

            if (!in_array($tipo, ['image/jpeg', 'image/png'])) {
                throw new Exception("Formato no válido.");
            }

            $rutaFinal = "../uploads/" . uniqid() . "_" . basename($nombreOriginal);
            move_uploaded_file($temp, $rutaFinal);

            // Miniatura
            $rutaMini = "../thumbnails/thumb_" . basename($rutaFinal);
            list($w, $h) = getimagesize($rutaFinal);
            $nuevoAncho = 200;
            $nuevoAlto = intval($h * ($nuevoAncho / $w));
            $thumb = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
            $origen = ($tipo === "image/png") ? imagecreatefrompng($rutaFinal) : imagecreatefromjpeg($rutaFinal);
            imagecopyresampled($thumb, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $w, $h);
            imagejpeg($thumb, $rutaMini, 80);

            $noticia = new Noticia($_POST, $rutaFinal, $rutaMini);
            if ($noticia->guardar()) {
                $response['success'] = true;
                $response['message'] = "Noticia guardada correctamente.";
            } else {
                $response['message'] = "No se pudo guardar.";
            }
            $response['accion'] = "Guardar";
            break;

        case 'Modificar':
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0) {
                $noticia = new Noticia($_POST);
                if ($noticia->editar($id)) {
                    $response['success'] = true;
                    $response['message'] = "Noticia modificada correctamente.";
                } else {
                    $response['message'] = "No se pudo modificar.";
                }
            } else {
                $response['message'] = "ID inválido para modificar.";
            }
            $response['accion'] = "Modificar";
            break;

        case 'Eliminar':
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0 && Noticia::eliminar($id)) {
                $response['success'] = true;
                $response['message'] = "Noticia eliminada.";
            } else {
                $response['message'] = "No se pudo eliminar.";
            }
            $response['accion'] = "Eliminar";
            break;

        case 'Listar':
            $noticias = Noticia::listarTodos();
            $response['success'] = true;
            $response['data'] = $noticias;
            $response['accion'] = "Listar";
            break;

        default:
            $response['message'] = "Acción no válida.";
            break;
    }
} catch (Exception $e) {
    $response['errors'][] = $e->getMessage();
    $response['message'] = "Error al procesar.";
}

echo json_encode($response);
