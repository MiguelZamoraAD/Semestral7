<?php
session_start();
file_put_contents("debugLibro.log", print_r($_POST, true));
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
file_put_contents("sesion_debug.txt", print_r($_SESSION, true), FILE_APPEND);


$path = realpath(__DIR__ . '/../../class/CRUDcls/libroClass.php');
if ($path === false) {
    die("ERROR: El archivo LibroClass.php NO existe en la ruta esperada.");
}
file_put_contents("log_debug.txt", print_r($_FILES, true), FILE_APPEND);

require_once($path); // clase categoria
$response = [
    'success' => false,
    'message' => '',
    'accion' => '',
    'data' => [],
    'errors' => []
];

try {
    $accion = $_POST['accion'] ?? '';
    $Libro = new Libro();

    switch ($accion) {
        case 'registrar_reserva':
            $libroId = $_POST['libro_id'] ?? null;
            $dias = $_POST['dias'] ?? null;
            $usuarioCorreo = $_SESSION['correo'] ?? null;

            if (!$libroId || !$dias || !$usuarioCorreo) {
                $response['message'] = 'Faltan datos obligatorios';
                break;
            }

            $resultado = $Libro->registrarReserva($libroId, $usuarioCorreo, $dias);
            $response = array_merge($response, $resultado);
            $response['accion'] = 'Reservar';
            break;
        
        case 'devolver_libro':
            $libroId = $_POST['libro_id'] ?? null;
            $usuarioCorreo = $_SESSION['correo'] ?? null;

            if (!$libroId || !$usuarioCorreo) {
                $response['message'] = 'Faltan datos para procesar la devolución.';
                break;
            }

            $resultado = $Libro->devolverLibro($libroId, $usuarioCorreo);
            $response = array_merge($response, $resultado);
            $response['accion'] = 'Devolver';
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