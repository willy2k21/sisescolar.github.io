<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

header('Content-Type: application/json');

if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'login-php';

    $conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    if (mysqli_connect_error()) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión']);
        exit;
    }

    $curso = $_GET['curso'];
    $alumnos = [];

    // Consultar alumnos que coincidan exactamente con el grado/curso seleccionado
    if ($stmt = $conexion->prepare('SELECT nombre FROM alumnos WHERE grado = ? ORDER BY nombre ASC')) {
        $stmt->bind_param('s', $curso);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        while ($row = $resultado->fetch_assoc()) {
            $alumnos[] = [
                'nombre' => $row['nombre']
            ];
        }
        $stmt->close();
    }
    
    mysqli_close($conexion);
    echo json_encode(['success' => true, 'alumnos' => $alumnos]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Curso no especificado']);
    exit;
}
?>
