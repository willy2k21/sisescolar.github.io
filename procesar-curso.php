<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_POST['nombre_curso']) || empty(trim($_POST['nombre_curso']))) {
        exit('Error: El nombre del curso es un campo obligatorio.');
    }

    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'login-php';

    $conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    if (mysqli_connect_error()) {
        exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
    }

    $nombre_curso = trim($_POST['nombre_curso']);
    $seccion = !empty(trim($_POST['seccion'])) ? trim($_POST['seccion']) : NULL;
    $descripcion = !empty(trim($_POST['descripcion'])) ? trim($_POST['descripcion']) : NULL;

    // Verificar duplicados
    $check_stmt = $conexion->prepare('SELECT id FROM cursos WHERE nombre_curso = ?');
    $check_stmt->bind_param('s', $nombre_curso);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        mysqli_close($conexion);
        exit('Error: Ya existe un curso registrado con ese mismo nombre.');
    }
    $check_stmt->close();

    // Guardado seguro
    if ($stmt = $conexion->prepare('INSERT INTO cursos (nombre_curso, seccion, descripcion) VALUES (?, ?, ?)')) {
        $stmt->bind_param('sss', $nombre_curso, $seccion, $descripcion);
        
        if ($stmt->execute()) {
            header('Location: cursos.php?status=success');
            exit;
        } else {
            echo "Error al registrar el curso: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error en la estructura SQL: " . $conexion->error;
    }
    
    mysqli_close($conexion);
} else {
    header('Location: cursos.php');
    exit;
}
?>
