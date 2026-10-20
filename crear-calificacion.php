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

// Consultar los alumnos registrados para el menú desplegable
$query_alumnos = "SELECT id, nombre, matricula FROM alumnos ORDER BY nombre ASC";
$res_alumnos = mysqli_query($conexion, $query_alumnos);

// Consultar las materias registradas para el menú desplegable
$query_materias = "SELECT id, nombre_materia, codigo_materia FROM materias ORDER BY nombre_materia ASC";
$res_materias = mysqli_query($conexion, $query_materias);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Asignar Nota</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/crear-materia.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="page-title">
                <h2>Gestión de Calificaciones</h2>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            <div class="form-container">
                <h3><i class="fas fa-star"></i> Registrar Calificación Académica</h3>
                
                <form action="procesar-calificacion.php" method="POST">
                    
                    <div class="form-group">
                        <label for="alumno_id">Seleccionar Estudiante:</label>
                        <select id="alumno_id" name="alumno_id" required style="width:100%; padding:12px; border:1px solid #dcdde1; border-radius:6px; background-color:#f8f9fa;">
                            <option value="">-- Elige un Alumno --</option>
                            <?php while($alu = mysqli_fetch_assoc($res_alumnos)): ?>
                                <option value="<?= $alu['id'] ?>"><?= htmlspecialchars($alu['nombre']) ?> (<?= htmlspecialchars($alu['matricula']) ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- NUEVO: Campo dinámico que muestra información filtrada del alumno mediante AJAX -->
                    <div class="form-group" id="info_filtro_alumno" style="display:none; background-color: #e8f4f8; padding: 12px; border-left: 4px solid #3498db; border-radius: 4px;">
                        <span style="font-size: 13.5px; color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-info-circle"></i> Grado del Alumno: <span id="txt_grado_alumno"></span>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="materia_id">Seleccionar Materia / Curso:</label>
                        <select id="materia_id" name="materia_id" required style="width:100%; padding:12px; border:1px solid #dcdde1; border-radius:6px; background-color:#f8f9fa;">
                            <option value="">-- Elige una Asignatura --</option>
                            <?php while($mat = mysqli_fetch_assoc($res_materias)): ?>
                                <option value="<?= $mat['id'] ?>"><?= htmlspecialchars($mat['nombre_materia']) ?> [<?= htmlspecialchars($mat['codigo_materia']) ?>]</option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="periodo">Periodo Evaluativo:</label>
                        <select id="periodo" name="periodo" required style="width:100%; padding:12px; border:1px solid #dcdde1; border-radius:6px; background-color:#f8f9fa;">
                            <option value="Primer Trimestre">Primer Trimestre</option>
                            <option value="Segundo Trimestre">Segundo Trimestre</option>
                            <option value="Tercer Trimestre">Tercer Trimestre</option>
                            <option value="Evaluación Final">Evaluación Final</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nota">Calificación Obtenida (0.00 al 10.00):</label>
                        <input type="number" id="nota" name="nota" min="0" max="10" step="0.1" placeholder="Ej: 8.5" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Guardar Calificación</button>
                        <a href="calificaciones.php" class="btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script JavaScript asíncrono para filtrar y reaccionar a la selección del alumno -->
    <script>
    document.getElementById('alumno_id').addEventListener('change', function() {
        const alumnoID = this.value;
        const contenedorInfo = document.getElementById('info_filtro_alumno');
        const txtGrado = document.getElementById('txt_grado_alumno');

        if(alumnoID === "") {
            contenedorInfo.style.display = 'none';
            return;
        }

        // Consultar al backend mediante Fetch API sin recargar la pantalla
        fetch('obtener-alumno-ajax.php?id=' + alumnoID)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    txtGrado.innerText = data.grado;
                    contenedorInfo.style.display = 'block';
                } else {
                    contenedorInfo.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error al filtrar los datos:', error);
                contenedorInfo.style.display = 'none';
            });
    });
    </script>

</body>
</html>
<?php mysqli_close($conexion); ?>
