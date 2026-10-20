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

// CORREGIDO: Se agregó 'grado' a la consulta SQL para leer el curso de la base de datos
$query = "SELECT id, codigo_materia, nombre_materia, grado, descripcion FROM materias ORDER BY grado ASC, nombre_materia ASC";
$resultado = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Materias</title>
    <!-- Carga de iconos oficial para evitar que aparezcan cuadros vacíos -->
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/materias.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <!-- Contenedor principal que estructura la página -->
    <div class="main-content">
        <!-- Barra superior idéntica a la sección de profesores -->
        <header>
            <div class="page-title">
                <h2>Gestión de Materias / Cursos</h2>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            <div class="table-responsive">
                <div class="table-title">
                    <h3>Plan de Estudios Vigente</h3>
                    <div class="action-buttons">
                        <!-- Botón para descargar el reporte de materias -->
                        <a href="reporte-materias.php" target="_blank" class="btn-pdf"><i class="fas fa-file-pdf"></i> Reporte PDF</a>
                        <a href="crear-materia.php" class="btn-add"><i class="fas fa-plus"></i> Nueva Materia</a>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre de la Asignatura</th>
                            <th>Curso / Grado</th> <!-- Nueva columna web -->
                            <th>Descripción / Detalles</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultado) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['codigo_materia']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['nombre_materia']) ?></td>
                                    
                                    <!-- Muestra el Curso o Grado asignado o manda una alerta si está vacío -->
                                    <td>
                                        <?php 
                                        $val_grado = trim($row['grado'] ?? '');
                                        if (!empty($val_grado)): 
                                        ?>
                                            <span style="background-color: #e8f4fd; color: #2980b9; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 13px;">
                                                <?= htmlspecialchars($val_grado) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="background-color: #fdf2e9; color: #e67e22; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 13px; font-style: italic;">
                                                Sin Asignar
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td><?= htmlspecialchars($row['descripcion'] ?? 'Sin descripción disponible') ?></td>
                                    <td>
                                        <a href="editar-materia.php?id=<?= $row['id'] ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="eliminar-materia.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirmarEliminacion('<?= htmlspecialchars($row['nombre_materia']) ?>');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #7f8c8d;">No hay materias curriculares dadas de alta.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function confirmarEliminacion(nombreMateria) {
        return confirm("¿Estás seguro de eliminar el curso de " + nombreMateria + "? Se podrían perder las calificaciones registradas de esta materia.");
    }
    </script>

</body>
</html>
<?php mysqli_close($conexion); ?>
