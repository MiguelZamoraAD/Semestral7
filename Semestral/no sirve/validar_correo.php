<?php
// validar_correo.php
if (isset($_GET['correo'])) {
    $correo = $_GET['correo'];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=company_info", "miguel", "2003");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Usuario = ?");
        $stmt->execute([$correo]);
        $existe = $stmt->fetchColumn();

        echo json_encode(['existe' => $existe > 0]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error en la conexión']);
    }
} else {
    echo json_encode(['error' => 'Parámetro no recibido']);
}
