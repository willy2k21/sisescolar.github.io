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
    $num_empleado = $_POST['num_empleado'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = !empty($_POST['telefono']) ? $_POST['telefono'] : NULL;
    $especialidad = $_POST['especialidad'];

    if ($stmt = $conexion->prepare('UPDATE profesores SET num_empleado = ?, nombre = ?, email = ?, telefono = ?, especialidad = ? WHERE id = ?')) {
        $stmt->bind_param('sssssi', $num_empleado, $nombre, $email, $telefono, $especialidad, $id);
        
        if ($stmt->execute()) {
            header('Location: profesores.php');
            exit;
        } else {
            echo "Error al actualizar los datos del profesor.";
        }
        $stmt->close();
    }
    mysqli_close($conexion);
} else {
    header('Location: profesores.php');
    exit;
}
?>
