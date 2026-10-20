<?php
session_start();

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'login-php';

$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
}

if (!isset($_POST['username'], $_POST['password'])) {
    header('Location: index.html');
    exit;
}

if ($stmt = $conexion->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
}

$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $password);
    $stmt->fetch();

    if ($_POST['password'] === $password) {
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['name'] = $_POST['username'];
        $_SESSION['id'] = $id;
        // Corregido: Te redirige al nuevo menú escolar moderno
        header('Location: menu.php');
        exit;
    }
}

header('Location: index.html');
$stmt->close();
mysqli_close($conexion);
?>
