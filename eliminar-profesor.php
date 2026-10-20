<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'login-php';

    $conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    if (mysqli_connect_error()) {
        exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
    }

    $id = $_GET['id'];

    if ($stmt = $conexion->prepare('DELETE FROM profesores WHERE id = ?')) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    mysqli_close($conexion);
}

header('Location: profesores.php');
exit;
?>
