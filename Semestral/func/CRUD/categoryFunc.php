<?php

//file_put_contents("debugCategory.log", print_r($_POST, true));
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$path = realpath(__DIR__ . '/../../class/CRUDcls/categoryCrud.php');
if ($path === false) {
    die("ERROR: El archivo CategoryCrud.php NO existe en la ruta esperada.");
}
//file_put_contents("log_debug.txt", print_r($_FILES, true), FILE_APPEND);

require_once($path); // clase categoria
$response = [
    'success' => false,
    'message' => '',
    'accion' => '',
    'data' => [],
    'errors' => []
];

try {
    $accion = $_POST['Accion'] ?? '';
    $Category = new Category();

    switch ($accion) {
        case 'Crear':
            $rutas = $Category -> procesarImagenCategoria($_FILES['imagen']);

            if (isset($rutas['error'])) {
                $response['success'] = false;
                $response['message'] = $rutas['error'];
                break;
            }

            $_POST['ruta_imagen'] = $rutas['ruta_imagen'];
            $_POST['ruta_miniatura'] = $rutas['ruta_miniatura'];

            $resultado = $Category->guardar($_POST);
            $response['success'] = $resultado;
            $response['message'] = $resultado ? 'Categoría creada correctamente.' : 'Ocurrió un error al crear una categoria nueva.';
            $response['accion'] = 'Crear';
            break;


        case 'Obtener':
            $id = $_POST['id'] ?? null;

             if ($id && is_numeric($id)) {
                $resultado = $Category->obtenerPorId($id);
                $response = $resultado;
                $response['accion'] = 'Obtener';
            } else {
                $response['success'] = false;
                $response['message'] = 'ID inválido.';
            }
            break;


        case 'Editar':
            $id = $_POST['id'] ?? null;
            if ($id && is_numeric($id)) {
                // Solo procesar si se subió una nueva imagen
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    $rutas = $Category->procesarImagenCategoria($_FILES['imagen']);

                    if (isset($rutas['error'])) {
                        $response['success'] = false;
                        $response['message'] = $rutas['error'];
                        break;
                    }
                    $_POST['ruta_imagen'] = $rutas['ruta_imagen'];
                    $_POST['ruta_miniatura'] = $rutas['ruta_miniatura'];
                }

                $resultado = $Category->editar($id, $_POST);
                $response['success'] = $resultado;
                $response['message'] = $resultado ? 'Categoría actualizada correctamente.' : 'Ocurrió un error al actualizar.';
                $response['accion'] = 'Editar';
            } else {
                $response['success'] = false;
                $response['message'] = 'ID inválido para la edición.';
            }
            break;

        case 'Eliminar':
            $id = $_POST['id'] ?? null;

            if (!$id || !is_numeric($id)) {
                $response['success'] = false;
                $response['message'] = 'ID inválido.';
            } else {
                $resultado = $Category->eliminar($id);
                $response['success'] = $resultado;
                $response['message'] = $resultado ? 'Categoria eliminado correctamente.' : 'Error al eliminar el usuario.';
                $response['accion'] = 'Eliminar';
            }
            break;


        case 'Listar':
            $pagina = isset($_POST['pagina']) ? max(1, intval($_POST['pagina'])) : 1;
            $limite = isset($_POST['limite']) ? intval($_POST['limite']) : 9;
            $busqueda = isset($_POST['busqueda']) ? trim($_POST['busqueda']) : '';

            $offset = ($pagina - 1) * $limite;

            $totalCategorias = $Category->contarCategorias($busqueda);
            $categorias = $Category->listarTodos($limite, $offset, $busqueda);
            $total = $Category->contar(); 

            $response['success'] = true;
            $response['data'] = $categorias;
            $response['total Categoria'] = $totalCategorias;
            $response['total'] = $total;
            $response['accion'] = "Listar";
            break;

        default:
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Acción no válida.'
            ]);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
    exit;
} catch (Exception $e) {
    $response['message'] = "Error al procesar: " . $e->getMessage();
    $response['errors'][] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
exit;