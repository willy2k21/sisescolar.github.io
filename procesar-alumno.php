<?php
session_start();

// Validar sesión activa
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// Validar que los datos llegaron por el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'login-php';

    $conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    if (mysqli_connect_error()) {
        exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
    }

    // Recibir y limpiar variables para evitar datos vacíos obligatorios
    $matricula = $_POST['matricula'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = !empty($_POST['telefono']) ? $_POST['telefono'] : NULL;
    $grado = $_POST['grado'];

    // Insertar datos de forma segura protegiendo contra Inyección SQL
    if ($stmt = $conexion->prepare('INSERT INTO alumnos (matricula, nombre, email, telefono, grado) VALUES (?, ?, ?, ?, ?)')) {
        $stmt->bind_param('sssss', $matricula, $nombre, $email, $telefono, $grado);
        
        if ($stmt->execute()) {
            // Si guarda con éxito, redirige de inmediato al listado de alumnos
            header('Location: alumnos.php');
            exit;
        } else {
            echo "Error al registrar el alumno. Es posible que la matrícula ya exista.";
        }
        $stmt->close();
    }
    
    mysqli_close($conexion);
} else {
    // Si alguien intenta entrar a este archivo a la fuerza, lo manda al listado
    header('Location: alumnos.php');
    exit;
}
?>
