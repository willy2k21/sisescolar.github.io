<?php
session_start();

if (!isset(\$_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

\(DATABASE_HOST = 'localhost';\)DATABASE_USER = 'root';
\(DATABASE_PASS = '';\)DATABASE_NAME = 'login-php';

\$conexion = mysqli_connect(\(DATABASE_HOST,\)DATABASE_USER, \(DATABASE_PASS,\)DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
}

// CORREGIDO: Cambiado para leer desde la tabla global 'cursos'
\$query_grados = "SELECT nombre_curso AS grado FROM cursos ORDER BY nombre_curso ASC";
\$res_grados = mysqli_query(conexion, query_grados);

\$query = "SELECT c.id, a.nombre AS alumno, m.nombre_materia AS materia, c.nota, c.periodo 
          FROM calificaciones c
          INNER JOIN alumnos a ON c.alumno_id = a.id
          INNER JOIN materias m ON c.materia_id = m.id
          ORDER BY c.id DESC";
\$resultado = mysqli_query(conexion, query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Calificaciones</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/calificaciones.css">
    <link rel="stylesheet" href="css/filtro-calificaciones.css">
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
                <span><?= htmlspecialchars(\$_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            
            <div class="filters-bar">
                <div class="filter-group">
                    <label for="filtro_curso">Filtrar por Curso:</label>
                    <select id="filtro_curso">
                        <option value="">-- Todos los Cursos --</option>
                        <?php while(\(g = mysqli_fetch_assoc(\)res_grados)): ?>
                            <option value="<?= htmlspecialchars(\(g['grado']) ?>"><?= htmlspecialchars(\)g['grado']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filtro_alumno">Seleccionar Alumno:</label>
                    <select id="filtro_alumno" disabled>
                        <option value="">-- Selecciona primero un curso --</option>
                    </select>
                </div>

                <button id="btn_buscar" class="btn-filter-search">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-title">
                    <h3>Registro General de Notas</h3>
                    <div class="action-buttons">
                        <a href="reporte-calificaciones.php" target="_blank" class="btn-pdf"><i class="fas fa-file-pdf"></i> Reporte PDF</a>
                        <a href="crear-calificacion.php" class="btn-add"><i class="fas fa-plus"></i> Asignar Nota</a>
                    </div>
                </div>
                
                <table id="tabla_notas">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Materia / Curso</th>
                            <th>Periodo Evaluativo</th>
                            <th>Calificación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo_tabla">
                        <?php if (mysqli_num_rows(\$resultado) > 0): ?>
                        <?php while(\(row = mysqli_fetch_assoc(\)resultado)): ?>
                            <tr class="fila-nota">
                                <td class="nombre-estudiante"><?= htmlspecialchars(\$row['alumno']) ?></td>
                                <td><?= htmlspecialchars(\$row['materia']) ?></td>
                                <td><?= htmlspecialchars(\$row['periodo']) ?></td>
                                <td>
                                    <?php if (\$row['nota'] >= 70): ?>
                                        <span class="nota-aprobada" style="color: #2ecc71; font-weight: bold; background-color: #e8f8f5; padding: 4px 8px; border-radius: 4px;">
                                            <?= number_format(\$row['nota'], 0) ?> - Aprobado
                                        </span>
                                    <?php else: ?>
                                        <span class="nota-reprobada" style="color: #e74c3c; font-weight: bold; background-color: #fceae9; padding: 4px 8px; border-radius: 4px;">
                                            <?= number_format(\$row['nota'], 0) ?> - Reprobado
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="eliminar-calificacion.php?id=<?= \$row['id'] ?>" class="btn-delete" onclick="return confirmarEliminacion();">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #7f8c8d;">No hay calificaciones asentadas en el sistema actualmente.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('filtro_curso').addEventListener('change', function() {
        const cursoSeleccionado = this.value;
        const cbAlumno = document.getElementById('filtro_alumno');

        if (cursoSeleccionado === "") {
            cbAlumno.innerHTML = '<option value="">-- Selecciona primero un curso --</option>';
            cbAlumno.disabled = true;
            filtrarTablaDirecto("");
            return;
        }

        fetch('obtener-estudiantes-por-curso.php?curso=' + encodeURIComponent(cursoSeleccionado))
            .then(response => response.json())
            .then(data => {
                cbAlumno.innerHTML = '<option value="">-- Todos los Alumnos de este Curso --</option>';
                if (data.success && data.alumnos.length > 0) {
                    data.alumnos.forEach(alumno => {
                        cbAlumno.innerHTML += `<option value="${alumno.nombre}">${alumno.nombre}</option>`;
                    });
                    cbAlumno.disabled = false;
                } else {
                    cbAlumno.disabled = true;
                }
            })
            .catch(error => console.error('Error al cargar estudiantes:', error));
    });

    document.getElementById('btn_buscar').addEventListener('click', function() {
        const alumnoBuscado = document.getElementById('filtro_alumno').value;
        if(alumnoBuscado === "") {
            filtrarTablaDirecto("");
        } else {
            filtrarTablaDirecto(alumnoBuscado);
        }
    });

    function filtrarTablaDirecto(cadenaBusqueda) {
        const busqueda = cadenaBusqueda.toLowerCase();
        const filas = document.querySelectorAll('.fila-nota');
        
        filas.forEach(fila => {
            const nombreEstudiante = fila.querySelector('.nombre-estudiante').innerText.toLowerCase();
            if (nombreEstudiante.includes(busqueda)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }

    function confirmarEliminacion() {
        return confirm("¿Estás completamente seguro de borrar esta calificación?");
    }
    </script>

</body>
</html>
<?php mysqli_close(\$conexion); ?>
