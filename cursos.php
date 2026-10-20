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

$query = "SELECT id, nombre_curso, seccion, descripcion FROM cursos ORDER BY nombre_curso ASC";
$resultado = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Cursos</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/materias.css"> <!-- Reutiliza tus estilos de tablas -->
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="page-title">
                <h2>Gestión de Cursos / Grados</h2>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            <div class="table-responsive">
                <div class="table-title">
                    <h3>Listado de Niveles y Secciones</h3>
                    <div class="action-buttons">
                        <a href="crear-curso.php" class="btn-add"><i class="fas fa-plus"></i> Nuevo Curso</a>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Curso / Grado</th>
                            <th>Sección / Turno</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultado) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($row['nombre_curso']) ?></strong></td>
                                    <td>
                                        <span style="background-color: #e8f4fd; color: #2980b9; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 13px;">
                                            <?= htmlspecialchars($row['seccion'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['descripcion'] ?? 'Sin descripción') ?></td>
                                    <td>
                                        <a href="editar-curso.php?id=<?= $row['id'] ?>" class="btn-edit" style="color: #3498db; margin-right: 10px;">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="eliminar-curso.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirmarEliminacion('<?= htmlspecialchars($row['nombre_curso']) ?>');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #7f8c8d;">No hay cursos registrados en el sistema actualmente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function confirmarEliminacion(nombreCurso) {
        return confirm("¿Estás seguro de eliminar el curso '" + nombreCurso + "'? Se podrían desvincular alumnos de este grupo.");
    }
    </script>

</body>
</html>
<?php mysqli_close($conexion); ?>
