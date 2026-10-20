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

    $id = $_POST['id'];
    $matricula = $_POST['matricula'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = !empty($_POST['telefono']) ? $_POST['telefono'] : NULL;
    $grado = $_POST['grado'];

    // Consulta SQL para actualizar los registros del estudiante por su ID
    if ($stmt = $conexion->prepare('UPDATE alumnos SET matricula = ?, nombre = ?, email = ?, telefono = ?, grado = ? WHERE id = ?')) {
        $stmt->bind_param('sssssi', $matricula, $nombre, $email, $telefono, $grado, $id);
        
        if ($stmt->execute()) {
            header('Location: alumnos.php');
            exit;
        } else {
            echo "Error al actualizar los datos del alumno.";
        }
        $stmt->close();
    }
    
    mysqli_close($conexion);
} else {
    header('Location: alumnos.php');
    exit;
}
?>
