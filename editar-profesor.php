<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: profesores.php');
    exit;
}

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'login-php';

$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
}

$id_profesor = $_GET['id'];
$profesor = null;

if ($stmt = $conexion->prepare('SELECT num_empleado, nombre, email, telefono, especialidad FROM profesores WHERE id = ?')) {
    $stmt->bind_param('i', $id_profesor);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $profesor = $resultado->fetch_assoc();
    } else {
        header('Location: profesores.php');
        exit;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Modificar Profesor</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/crear-profesor.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="page-title">
                <h2>Gestión de Profesores</h2>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            <div class="form-container">
                <h3><i class="fas fa-user-edit"></i> Modificar Datos del Profesor</h3>
                
                <form action="procesar-editar-profesor.php" method="POST">
                    
                    <input type="hidden" name="id" value="<?= $id_profesor ?>">
                    
                    <div class="form-group">
                        <label for="num_empleado">Número de Empleado / Ficha:</label>
                        <input type="text" id="num_empleado" name="num_empleado" value="<?= htmlspecialchars($profesor['num_empleado']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre del Profesor:</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($profesor['nombre']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($profesor['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($profesor['telefono'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="especialidad">Especialidad / Área Dictada:</label>
                        <input type="text" id="especialidad" name="especialidad" value="<?= htmlspecialchars($profesor['especialidad']) ?>" required>
                    </div>

                    <button type="submit" class="btn-submit" style="background-color: #3498db;">Actualizar Cambios</button>
                    <a href="profesores.php" class="btn-cancel">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
<?php mysqli_close($conexion); ?>
