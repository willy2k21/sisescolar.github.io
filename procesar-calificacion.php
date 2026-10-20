<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'login-php';

    $conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    if (mysqli_connect_error()) {
        exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
    }

    $alumno_id = $_POST['alumno_id'];
    $materia_id = $_POST['materia_id'];
    $periodo = $_POST['periodo'];
    $nota = $_POST['nota'];

    // Inserción segura mediante una Sentencia Preparada (Prepare Statement)
    if ($stmt = $conexion->prepare('INSERT INTO calificaciones (alumno_id, materia_id, periodo, nota) VALUES (?, ?, ?, ?)')) {
        $stmt->bind_param('iiss', $alumno_id, $materia_id, $periodo, $nota);
        
        if ($stmt->execute()) {
            header('Location: calificaciones.php');
            exit;
        } else {
            echo "Error al registrar la calificación en la base de datos.";
        }
        $stmt->close();
    }
    
    mysqli_close($conexion);
} else {
    header('Location: calificaciones.php');
    exit;
}
?>
