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

// CORREGIDO: Consulta enlazada directamente con la nueva tabla de cursos institucionales
$query_grados = "SELECT nombre_curso AS grado FROM cursos ORDER BY nombre_curso ASC";
$res_grados = mysqli_query($conexion, $query_grados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Nueva Materia</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/crear-materia.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="page-title">
                <h2>Gestión de Materias</h2>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            <div class="form-container">
                <h3><i class="fas fa-book"></i> Registrar Nueva Asignatura</h3>
                
                <form action="procesar-materia.php" method="POST">
                    
                    <div class="form-group">
                        <label for="codigo_materia">Código de la Materia:</label>
                        <input type="text" id="codigo_materia" name="codigo_materia" placeholder="Ej: MAT-101" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre_materia">Nombre de la Asignatura:</label>
                        <input type="text" id="nombre_materia" name="nombre_materia" placeholder="Ej: Álgebra Lineal" required>
                    </div>

                    <div class="form-group">
                        <label for="grado">Asignar a un Curso / Grado:</label>
                        <select id="grado" name="grado" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff;">
                            <option value="">-- Selecciona un Curso --</option>
                            <?php if (mysqli_num_rows($res_grados) > 0): ?>
                                <?php while($g = mysqli_fetch_assoc($res_grados)): ?>
                                    <option value="<?= htmlspecialchars($g['grado']) ?>"><?= htmlspecialchars($g['grado']) ?></option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="General">General / Sin curso específico</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción / Detalles:</label>
                        <textarea id="descripcion" name="descripcion" placeholder="Escribe un breve resumen de los temas del curso..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Guardar Materia</button>
                    <a href="materias.php" class="btn-cancel">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
<?php mysqli_close($conexion); ?>
