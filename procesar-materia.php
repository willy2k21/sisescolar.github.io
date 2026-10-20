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

    $codigo_materia = $_POST['codigo_materia'];
    $nombre_materia = $_POST['nombre_materia'];
    $descripcion = !empty($_POST['descripcion']) ? $_POST['descripcion'] : NULL;

    if ($stmt = $conexion->prepare('INSERT INTO materias (codigo_materia, nombre_materia, descripcion) VALUES (?, ?, ?)')) {
        $stmt->bind_param('sss', $codigo_materia, $nombre_materia, $descripcion);
        
        if ($stmt->execute()) {
            header('Location: materias.php');
            exit;
        } else {
            echo "Error al registrar la materia. Es posible que el código ya exista.";
        }
        $stmt->close();
    }
    
    mysqli_close($conexion);
} else {
    header('Location: materias.php');
    exit;
}
?>
