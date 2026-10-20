<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: materias.php');
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

// CORREGIDO: Apunta al listado oficial de cursos
$query_grados = "SELECT nombre_curso AS grado FROM cursos ORDER BY nombre_curso ASC";
$res_grados = mysqli_query($conexion, $query_grados);

$id_materia = $_GET['id'];
$materia = null;

if ($stmt = $conexion->prepare('SELECT codigo_materia, nombre_materia, grado, descripcion FROM materias WHERE id = ?')) {
    $stmt->bind_param('i', $id_materia);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $materia = $resultado->fetch_assoc();
    } else {
        header('Location: materias.php');
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
    <title>Sistema Escolar - Modificar Materia</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/crear-materia.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
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
            <div class="form-container">
                <h3><i class="fas fa-edit"></i> Modificar Datos de la Asignatura</h3>
                
                <form action="procesar-editar-materia.php" method="POST">
                    
                    <input type="hidden" name="id" value="<?= $id_materia ?>">
                    
                    <div class="form-group">
                        <label for="codigo_materia">Código de la Materia:</label>
                        <input type="text" id="codigo_materia" name="codigo_materia" value="<?= htmlspecialchars($materia['codigo_materia']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre_materia">Nombre de la Asignatura:</label>
                        <input type="text" id="nombre_materia" name="nombre_materia" value="<?= htmlspecialchars($materia['nombre_materia']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="grado">Asignar a un Curso / Grado:</label>
                        <select id="grado" name="grado" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff;">
                            <option value="">-- Selecciona un Curso --</option>
                            <?php if (mysqli_num_rows($res_grados) > 0): ?>
                                <?php while($g = mysqli_fetch_assoc($res_grados)): ?>
                                    <option value="<?= htmlspecialchars($g['grado']) ?>" <?= ($g['grado'] == $materia['grado']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g['grado']) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="General" <?= ($materia['grado'] == 'General') ? 'selected' : '' ?>>General / Sin curso específico</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción / Detalles del Curso:</label>
                        <textarea id="descripcion" name="descripcion"><?= htmlspecialchars($materia['descripcion'] ?? '') ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Actualizar Materia</button>
                        <a href="materias.php" class="btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
<?php mysqli_close($conexion); ?>
