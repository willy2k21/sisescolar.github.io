<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
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

$query = "SELECT id, matricula, nombre, email, telefono, grado FROM alumnos";
$resultado = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Alumnos</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/alumnos.css">
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
            <div class="table-responsive">
                <div class="table-title">
                    <h3>Listado Oficial de Estudiantes</h3>
                    <div class="action-buttons">
                        <!-- Botón para descargar PDF -->
                        <a href="reporte-alumnos.php" target="_blank" class="btn-pdf"><i class="fas fa-file-pdf"></i> Reporte PDF</a>
                        <a href="crear-alumno.php" class="btn-add"><i class="fas fa-plus"></i> Nuevo Alumno</a>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nombre Completo</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Grado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultado) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['matricula']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['telefono'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['grado']) ?></td>
                                    <td>
                                        <a href="editar-alumno.php?id=<?= $row['id'] ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <!-- Enlace que ejecuta la validación de JavaScript antes de ir al PHP -->
                                        <a href="eliminar-alumno.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirmarEliminacion('<?= htmlspecialchars($row['nombre']) ?>');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #7f8c8d;">No hay alumnos registrados actualmente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Validación de JavaScript para confirmar eliminación -->
    <script>
    function confirmarEliminacion(nombreAlumno) {
        return confirm("¿Estás completamente seguro de que deseas eliminar al alumno " + nombreAlumno + "? Esta acción no se puede deshacer.");
    }
    </script>

</body>
</html>
<?php mysqli_close($conexion); ?>
