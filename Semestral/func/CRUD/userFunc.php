<?php

file_put_contents("debuguser.log", print_r($_POST, true));
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$path = realpath(__DIR__ . '/../../class/CRUDcls/userCrud.php');
if ($path === false) {
    die("ERROR: El archivo userCrud.php NO existe en la ruta esperada.");
}
require_once($path); //editor de usuarios

$response = [
    'success' => false,
    'message' => '',
    'accion' => '',
    'data' => [],
    'errors' => []
];

try {
    $accion = $_POST['Accion'] ?? '';
    $user = new User();

    switch ($accion) {
        case 'Guardar':
            $cedula = $_POST['Cedula'] ?? '';
            $usuario = $_POST['Usuario'] ?? '';
            $id = $_POST['id'] ?? null;
            if ($user->verificarDuplicado($cedula, $usuario, $id)) {
                $response['success'] = false;
                $response['message'] = 'La cédula o el correo ya están registrados.';
            } else {
                $resultado = $user->guardar($_POST);
                $response['success'] = $resultado;
                $response['message'] = $resultado ? 'Usuario guardado correctamente.' : 'Ocurrió un error al guardar.';
                $response['accion'] = 'Guardar';
            }
            break;

        case 'VerificarDuplicado':
            $cedula = $_POST['Cedula'] ?? '';
            $usuario = $_POST['Usuario'] ?? '';
            $id = $_POST['id'] ?? null;
            $existe = $user->verificarDuplicado($cedula, $usuario, $id);
            echo json_encode(['success' => true, 'existe' => $existe]);
            exit;

        case 'Obtener':
            $id = $_POST['id'] ?? null;

             if ($id && is_numeric($id)) {
                $resultado = $user->obtenerPorId($id);
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
                $resultado = $user->editar($id, $_POST);
                $response['success'] = $resultado;
                $response['message'] = $resultado ? 'Usuario actualizado correctamente.' : 'Ocurrió un error al actualizar.';
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
                $resultado = $user->eliminar($id);
                $response['success'] = $resultado;
                $response['message'] = $resultado ? 'Usuario eliminado correctamente.' : 'Error al eliminar el usuario.';
                $response['accion'] = 'Eliminar';
            }
            break;


        case 'Listar':
            $pagina = isset($_POST['pagina']) ? max(1, intval($_POST['pagina'])) : 1;
            $limite = isset($_POST['limite']) ? intval($_POST['limite']) : 3;
            $busqueda = isset($_POST['busqueda']) ? trim($_POST['busqueda']) : '';

            $offset = ($pagina - 1) * $limite;

            $totalUsuarios = $user->contarUsuarios($busqueda);
            $usuarios = $user->listarTodos($limite, $offset, $busqueda);

            $response['success'] = true;
            $response['data'] = $usuarios;
            $response['total'] = $totalUsuarios;
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