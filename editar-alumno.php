<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// Validar que se recibió un ID válido por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: alumnos.php');
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

// Obtener los datos actuales del alumno elegido
$id_alumno = $_GET['id'];
$alumno = null;

if ($stmt = $conexion->prepare('SELECT matricula, nombre, email, telefono, grado FROM alumnos WHERE id = ?')) {
    $stmt->bind_param('i', $id_alumno);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $alumno = $resultado->fetch_assoc();
    } else {
        // Si el ID no existe en la BD, regresa al listado
        header('Location: alumnos.php');
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
    <title>Sistema Escolar - Modificar Alumno</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <!-- Reutilizamos el CSS del formulario de creación ya que usa los mismos campos -->
    <link rel="stylesheet" href="css/crear-alumno.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="page-title">
                <h2>Gestión de Alumnos</h2>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            <div class="form-container">
                <h3><i class="fas fa-user-edit"></i> Modificar Datos del Estudiante</h3>
                
                <form action="procesar-editar-alumno.php" method="POST">
                    
                    <!-- Campo oculto para enviar el ID del alumno al procesador -->
                    <input type="hidden" name="id" value="<?= $id_alumno ?>">
                    
                    <div class="form-group">
                        <label for="matricula">Matrícula Escolar:</label>
                        <input type="text" id="matricula" name="matricula" value="<?= htmlspecialchars($alumno['matricula']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre Completo:</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($alumno['nombre']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($alumno['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono de Contacto:</label>
                        <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($alumno['telefono'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="grado">Grado / Curso:</label>
                        <select id="grado" name="grado" required>
                            <option value="1º de Secundaria" <?= $alumno['grado'] == '1º de Secundaria' ? 'selected' : '' ?>>1º de Secundaria</option>
                            <option value="2º de Secundaria" <?= $alumno['grado'] == '2º de Secundaria' ? 'selected' : '' ?>>2º de Secundaria</option>
                            <option value="3º de Secundaria" <?= $alumno['grado'] == '3º de Secundaria' ? 'selected' : '' ?>>3º de Secundaria</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit" style="background-color: #3498db;">Actualizar Cambios</button>
                    <a href="alumnos.php" class="btn-cancel">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
<?php mysqli_close($conexion); ?>
