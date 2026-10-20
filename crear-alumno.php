<?php
session_start();

// Validar que el usuario tenga sesión activa
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

// NUEVO: Obtener los cursos oficiales cargados en el sistema escolar
$query_cursos_db = "SELECT nombre_curso FROM cursos ORDER BY nombre_curso ASC";
$res_cursos_db = mysqli_query($conexion, $query_cursos_db);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Nuevo Alumno</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
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
                <h3><i class="fas fa-user-plus"></i> Registrar Nuevo Estudiante</h3>
                
                <form action="procesar-alumno.php" method="POST">
                    
                    <div class="form-group">
                        <label for="matricula">Matrícula Escolar:</label>
                        <input type="text" id="matricula" name="matricula" placeholder="Ej: ALU-2026-004" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre Completo:</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Nombre y Apellidos" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" placeholder="correo@estudiante.com" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono de Contacto:</label>
                        <input type="text" id="telefono" name="telefono" placeholder="Ej: 555-1234">
                    </div>

                    <!-- CORREGIDO: Carga de cursos dinámica desde la base de datos -->
                    <div class="form-group">
                        <label for="grado">Grado / Curso:</label>
                        <select id="grado" name="grado" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff;">
                            <option value="">-- Selecciona un Curso --</option>
                            <?php if (mysqli_num_rows($res_cursos_db) > 0): ?>
                                <?php while($c = mysqli_fetch_assoc($res_cursos_db)): ?>
                                    <option value="<?= htmlspecialchars($c['nombre_curso']) ?>"><?= htmlspecialchars($c['nombre_curso']) ?></option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="General">General / Sin curso específico</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">Guardar Alumno</button>
                    <a href="alumnos.php" class="btn-cancel">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
<?php mysqli_close($conexion); ?>
