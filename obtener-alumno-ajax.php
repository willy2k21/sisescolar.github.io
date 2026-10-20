<?php
session_start();

// Validar que el usuario tenga sesión activa antes de responder la petición
if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

header('Content-Type: application/json');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'login-php';

    $conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    if (mysqli_connect_error()) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión']);
        exit;
    }

    $id = $_GET['id'];
    $response = ['success' => false];

    // Buscar de forma segura el grado correspondiente al estudiante
    if ($stmt = $conexion->prepare('SELECT grado FROM alumnos WHERE id = ?')) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $alumno = $resultado->fetch_assoc();
            $response = [
                'success' => true,
                'grado' => $alumno['grado']
            ];
        }
        $stmt->close();
    }
    
    mysqli_close($conexion);
    echo json_encode($response);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}
?>
