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

$query = "SELECT id, num_empleado, nombre, email, telefono, especialidad FROM profesores";
$resultado = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Profesores</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/profesores.css">
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
            <div class="table-responsive">
                <div class="table-title">
                    <h3>Plantilla Docente Activa</h3>
                    <div class="action-buttons">
                        <!-- Botón para descargar PDF de Profesores -->
                        <a href="reporte-profesores.php" target="_blank" class="btn-pdf"><i class="fas fa-file-pdf"></i> Reporte PDF</a>
                        <a href="crear-profesor.php" class="btn-add"><i class="fas fa-plus"></i> Nuevo Profesor</a>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Nº Empleado</th>
                            <th>Nombre Docente</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Especialidad / Área</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultado) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['num_empleado']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['telefono'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['especialidad']) ?></td>
                                    <td>
                                        <a href="editar-profesor.php?id=<?= $row['id'] ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="eliminar-profesor.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirmarEliminacion('<?= htmlspecialchars($row['nombre']) ?>');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #7f8c8d;">No hay profesores registrados actualmente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function confirmarEliminacion(nombreProfesor) {
        return confirm("¿Estás seguro de que deseas eliminar al profesor " + nombreProfesor + "? Las materias vinculadas podrían verse afectadas.");
    }
    </script>

</body>
</html>
<?php mysqli_close($conexion); ?>
