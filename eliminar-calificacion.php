<?php
session_start();

// 1. Validar que el usuario tenga sesión activa
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// 2. Verificar que se recibió un ID válido a través de la URL
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

    // 3. Eliminación segura utilizando una Sentencia Preparada (Prepare Statement)
    if ($stmt = $conexion->prepare('DELETE FROM calificaciones WHERE id = ?')) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    
    mysqli_close($conexion);
}

// 4. Redirigir de inmediato al listado general de calificaciones
header('Location: calificaciones.php');
exit;
?>
